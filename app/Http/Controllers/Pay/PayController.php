<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-27
 */

namespace App\Http\Controllers\Pay;

use App\Components\HttpStatusCode;
use App\Http\Controllers\ApiController;
use App\Models\Order;
use App\Components\Pay;
use App\Models\PandaFCode;
use fk\utility\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PayController extends ApiController
{

    /**
     * @@method GET
     * @@route api/order/paid
     * @param Request $request
     */
    public function checkOrderPaid(Request $request)
    {
        $this->validate($request, [
            'sn' => 'required'
        ]);
        $SN = $request->get('sn');
        /** @var Order $order */
        $order = Order::where('order_sn', $SN)->first(['status']);
        if ($order && $order->status >= Order::STATUS_SUCCESS) {
            $FCode = PandaFCode::pop($order->id);
            $this->result
                ->message('Order paid')
                ->extend([
                    'f_code' => $FCode,
                    'indoor_buy_account' => 1, // always 1: indoor buy account registered
                ]);
            if (!$FCode) $this->result->extend(['notice' => '没有可用的F码']);
        } else {
            $this->result->code(HttpStatusCode::SUCCESS_ACCEPTED)
                ->message('Cannot find order info or order is unpaid.');
        }
    }

    /**
     * @@method POST
     * @@route api/pay
     *
     * @param Request $request
     * @param Pay $pay
     *
     * @request-input int $goods_id
     * @request-input int $num
     * @request-input int $coupon_id
     * @request-input int $coupon_value
     * @request-input string $return_url
     */
    public function pay(Request $request, Pay $pay)
    {
        if ($info = $pay->generateOrder($request)) {
            if (is_string($info)) {
                $this->result->extend(['info' => $info]);
            } else {
                $this->result->data($info);
            }
            $this->result->message('生成支付订单成功'); // TODO: i18n
        } else {
            $this->result
                ->code($pay->code ?: HttpStatusCode::SERVER_THIRD_PARTY_ERROR)
                ->message('无法生成支付订单')// TODO: i18n
                ->extend(['errors' => $pay->errors]);
        }
    }
}