<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-24
 */

namespace App\Http\Controllers\Pay;

use App\Components\Pay;
use App\Components\SinopecApiEncapsulate;
use App\Http\Controllers\Controller;
use App\Models\CouponTransaction;
use App\Models\GasCardCharge;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPool;
use fk\helpers\debug\Capture;
use fk\pay\entries\AliPayEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NotifyController extends Controller
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Pay
     */
    protected $pay;

    /**
     * @var Order
     */
    protected $order;

    public function __construct(Request $request, Pay $pay)
    {
        $this->request = $request;
        $this->pay = $pay;
    }

    public function weChat()
    {
    }

    /**
     * @method POST
     * @link https://doc.open.alipay.com/doc2/detail.htm?treeId=203&articleId=105286&docType=1
     * @return string
     */
    public function aliPay()
    {

        if (!$this->validateAliPay()) return AliPayEntry::NOTIFY_RESULT_FAILED;

        $request = $this->request;

        $this->prepareOrder(
            $request->input('out_trade_no'),
            $request->input('total_amount')
        );

        if (false == $order = $this->order) return AliPayEntry::NOTIFY_RESULT_FAILED;

        if ($order->status == Order::STATUS_SUCCESS) return AliPayEntry::NOTIFY_RESULT_SUCCESS;

        if ($this->updateOrder(
            $request->input('trade_no'),
            $request->input('app_id'),
            $request->input('buyer_id', '0'),
            $this->translateAliPayStatus($request->input('trade_status')),
            $request->input('fund_bill_list')
        )
        ) {
            $this->afterNotified();
            return AliPayEntry::NOTIFY_RESULT_SUCCESS;
        } else {
            return AliPayEntry::NOTIFY_RESULT_FAILED;
        }
    }

    protected function updateOrder($tpOrderSN, $appID, $tpUID, $status, $channel = '')
    {
        $this->order->fill([
            'order_sn_tp' => $tpOrderSN,
            'app_id' => $appID,
            'tp_uid' => $tpUID,
            'channel' => $channel,
        ]);

        if ($status === false) return AliPayEntry::NOTIFY_RESULT_FAILED;

        $this->order->status = $status;

        return $this->order->save();
    }

    protected function prepareOrder($orderSN, $totalAmount)
    {
        /** @var Order $order */
        $this->order = Order::where('order_sn', $orderSN)
            ->where('total_amount', $totalAmount)
            ->first();

    }

    public function validateAliPay()
    {
        /*
         * notify_time	通知时间	    Date	     是	通知的发送时间。格式为yyyy-MM-dd HH:mm:ss	2015-14-27 15:45:58
         * notify_type	通知类型	    String(64)	 是	通知的类型	trade_status_sync
         * notify_id	通知校验ID	String(128)	 是	通知校验ID	ac05099524730693a8b330c5ecf72da9786
         * charset      编码格式     String(10)   是 编码格式，如utf-8、gbk、gb2312等 utf-8
         * version      接口版本     String(3)    是 调用的接口版本，固定为：1.0 1.0
         * sign_type    签名类型     String(10)   是 商户生成签名字符串所使用的签名算法类型，目前支持RSA2和RSA，推荐使用RSA2 RSA2
         * sign         签名        String(256)  是 请参考异步返回结果的验签 601510b7970e52cc63db0f44997cf70e
         * trade_no     支付宝交易号 String(64)   是 支付宝交易凭证号 2013112011001004330000121536
         * out_trade_no 商户订单号   String(64)   是 原支付请求的商户订单号 6823789339978248
         */
        $this->validate($this->request, [
            'notify_time' => 'required',
            'notify_type' => 'required|string|max:64',
            'notify_id' => 'required|string|max:128',
            'app_id' => ['required', Rule::in([config('pay.platforms.AliPay.app_id')])],
            'charset' => ['required', Rule::in(['utf-8', 'UTF-8'])],
            'version' => 'required',
            'sign_type' => ['required', Rule::in(['RSA', 'RSA2'])],
            'sign' => 'required',
            'trade_no' => 'required|string|max:64',
            'out_trade_no' => 'required|string|max:64',
            'trade_status' => ['required', Rule::in(AliPayEntry::getStatuses())]
        ]);

        /*
         * 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
         * 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
         * 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email），
         * 4、验证app_id是否为该商户本身。
         *
         * 上述1、2、3、4有任何一个验证不通过，则表明本次通知是异常通知，务必忽略。
         * 在上述验证通过后商户必须根据支付宝不同类型的业务通知，正确的进行不同的业务处理，并且过滤重复的通知结果数据。
         * 在支付宝的业务通知中，只有交易通知状态为TRADE_SUCCESS或TRADE_FINISHED时，支付宝才会认定为买家付款成功。
         */
        return $this->pay->checkSignature($this->request->input());
    }

    protected function updateUserCoupon()
    {
        CouponTransaction::where('order_id', $this->order->id)->update(['created_for' => CouponTransaction::CREATED_FOR_SPENDING]);
    }

    protected function afterNotified()
    {
        $this->updateUserPool();

        $this->addTransaction(
            $this->request->input('subject', '交易成功'), // TODO: i18n
            mb_substr($this->request->input('body', ''), 0, 20, 'utf-8') . '...'
        );

        $this->updateUserCoupon();

        switch ($this->order->category) {
            case Order::CATEGORY_TOP_UP:
                $this->afterTopUp();
                break;
        }
    }

    protected function updateUserPool()
    {
        UserPool::where('order_id', $this->order->id)->update(['paid' => UserPool::PAID_YES]);
    }

    protected function addTransaction($summary, $remark)
    {
        Transaction::add(
            -$this->order->actual_amount,
            $summary,
            $this->order->category,
            $remark,
            Transaction::OUTGOING_NO,
            $this->order->belongs_to
        );
    }

    protected function afterTopUp()
    {
        $this->topUpIndoorBuy();
    }

    protected function topUpIndoorBuy()
    {
        // TODO: refund, only when coupon used, order expires in 15m
        /**
         * @var User $user
         * @var SinopecApiEncapsulate $sinopec
         */
        $user = User::where('id', $this->order->belongs_to)->first();
        if (!$user) {
            Capture::add(__METHOD__, 'Cannot find user');
            return;
        }

        CouponTransaction::where('order_id', $this->order->id)->update(['created_for' => CouponTransaction::CREATED_FOR_SPENDING]);

        $sinopec = App::make(SinopecApiEncapsulate::class);
        $response = $sinopec->topUpConfirm($user->mobile, $user->gas_card_sn, $this->order->actual_amount, $user->name);

        if (isset($response['code']) && $response['code'] == 100) {
            $attributes = [
                'status' => GasCardCharge::STATUS_NOTIFY_TP_FAILED,
                'extra' => json_encode($response, JSON_UNESCAPED_UNICODE),
            ];
        } else {
            $attributes = ['status' => GasCardCharge::STATUS_NOTIFY_TP_SUCCEEDED];
        }

        GasCardCharge::where(['order_id' => $this->order->id])->update($attributes);
    }

    protected function translateAliPayStatus($aliPayStatus)
    {
        switch ($aliPayStatus) {
            case AliPayEntry::TRADE_STATUS_CLOSED:
                return Order::STATUS_FAILED;
                break;
            case AliPayEntry::TRADE_STATUS_SUCCESS:
                return Order::STATUS_SUCCESS;
                break;
            case AliPayEntry::TRADE_STATUS_FINISHED;
                return Order::STATUS_FINISHED;
                break;
            default:
                return false;
        }
    }
}