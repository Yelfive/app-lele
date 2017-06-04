<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-25
 */

namespace App\Components;

use App\Models\Coupon;
use App\Models\CouponTransaction;
use App\Models\GasCardCharge;
use App\Models\Goods;
use App\Models\Model;
use App\Models\OrderGoods;
use App\Models\UserCoupon;
use fk\pay\Component;
use App\Models\Order;
use fk\pay\config\Platform;
use fk\pay\lib\OrderHelper;
use fk\utility\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class Pay extends Component
{

    public $code;

    /**
     * @var array
     */
    public $errors;

    /**
     * @var Order
     */
    public $order;

    /**
     * @var Coupon
     */
    public $coupon;

    /**
     * @var UserCoupon
     */
    public $userCoupon;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var CouponTransaction
     */
    public $couponTransaction;

    /**
     * @var Goods
     */
    public $goods;

    public function __construct(array $config = [])
    {
        $config = array_merge($config, config('pay'));
        parent::__construct($config);
    }

    /**
     * @param Request $request
     *  - goods_id=1 gasoline card
     *  - num=1 goods count
     *  - channel=AliPay WeChat AliPay
     * @param array $attributes
     * @return array|false|string false on failure
     * @throws \Exception
     * @throws \fk\pay\Exception
     * @request-input int $goods_id
     * @request-input int $num
     * @request-input string $return_url
     * @request-input string $platform
     * @request-input string $client_type
     */
    public function generateOrder(Request $request, array $attributes = [])
    {
        if (!$this->validate($this->request = $request)) return false;

        $goodsID = $request->input('goods_id');
        $goodsNum = $request->input('num');

        $category = Order::getOrderCategoryByGoods($goodsID);

        $orderSN = OrderHelper::generateSN($attributes['belongs_to'] ?? Auth::id(), $category);

        if (false === $goods = $this->getGoods()) return false;

        if (false === $couponDeduction = $this->getCouponDeduction($request)) return false;

        /** @var UserCoupon $userCoupon */
        $totalAmount = $goods->price * $goodsNum;
        $actualAmount = $totalAmount - $couponDeduction;

        if ($actualAmount < 0) {
            $this->code = HttpStatusCode::CLIENT_VALIDATION_ERROR;
            $this->errors['actual_amount'] = '实付金额不能小于0'; // TODO: i18n
            return false;
        }

        $extra = $this->getPayExtra($request);

        $platform = $this->getPlatformFromRequest($request);
        $pay = (new Pay())->with($platform);
        if ($returnUrl = $request->input('return_url')) $pay->setReturnUrl($returnUrl);
        $form = $pay->pay($orderSN, $actualAmount, $goods->name, $goods->description, $extra);

        $this->order = new Order(array_merge([
            'order_sn' => $orderSN,
            'category' => $category,
            'order_sn_tp' => '',
            'total_amount' => $totalAmount,
            'actual_amount' => $actualAmount,
            'coupon_id' => $request->input('coupon_id', 0),
            'coupon_amount' => $couponDeduction,
            'status' => Order::STATUS_WAIT_PAYING,
            'tp_app_id' => $this->getAppID($request),
            'tp_uid' => '0',
            'platform' => $this->translatePlatform($request),
            'channel' => Order::CHANNEL_NONE,
        ], $attributes));

        if (
            $this->order->save()
            && $this->saveOrderGoods($goodsNum)
            && $this->notifyIndoorBuy($request)
        ) {
            return $form;
        } else {
            foreach (['order', 'couponTransaction'] as $property) {
                /** @var Model $object */
                $object = $this->$property;
                if ($object && $object->hasErrors()) $this->errors['order_errors'] = $object->errors->toArray();
            }
            if ($this->errors) $this->errors['pay'] = $this->errors;
            return false;
        }
    }

    protected function saveOrderGoods($goodsNum)
    {
        $goods = $this->goods;
        /** @var OrderGoods $orderGoods */
        $orderGoods = OrderGoods::create([
            'order_id' => $this->order->id,
            'goods_id' => $goods->id,
            'goods_num' => $goodsNum,
            'unit_price' => $goods->price,
            'total_amount' => $goodsNum * $goods->price,
            'buyer_id' => $this->order->belongs_to,
        ]);
        return $orderGoods->hasErrors();
    }

    /**
     * @return bool|Goods
     */
    protected function getGoods()
    {
        if ($goods = Goods::find($this->request->input('goods_id'))) return $this->goods = $goods;

        $this->errors['goods_id'] = '商品不存在或已下架'; // TODO: i18n
        return false;
    }

    /**
     * Indicates coupon is not enough
     * @return bool
     */
    protected function insufficientCoupon()
    {
        if ($this->userCoupon->num < $this->request->input('coupon_num')) {
            $this->code = HttpStatusCode::CLIENT_VALIDATION_ERROR;
            $this->errors['coupon_num'] = '抵用券不足'; // TODO: i18n
            return true;
        } else {
            return false;
        }
    }

    protected function getCouponDeduction(Request $request)
    {
        if ($request->input('goods_id') == Goods::CATEGORY_APPLY_FOR_CARD) {
            $deduction = 0;
        } else if (
            ($couponNum = $request->input('coupon_num'))
            && ($couponID = $request->input('coupon_id'))
        ) {
            /** @var UserCoupon|null $userCoupon */
            $this->userCoupon = $userCoupon = Auth::id() ? UserCoupon::where('id', $couponID)->where('belongs_to', Auth::id())->first() : null;

            if (!$userCoupon) {
                $deduction = 0;
            } else if ($this->insufficientCoupon()) {
                return false;
            } else {
                $deduction = $userCoupon->coupon_value * $couponNum;
            }
        } else {
            $deduction = 0;
        }

        // Reset coupon usage to none,
        // in case it is accessed somewhere else
        if ($deduction === 0) {
            $request->request->set('coupon_id', 0);
            $request->request->set('coupon_num', 0);
        }
        return $deduction;
    }

    /**
     * @param Request $request
     * @return bool Indicates if the coupon updated successfully
     */
    protected function notifyIndoorBuy(Request $request): bool
    {

        if ($this->order->category == Order::CATEGORY_TOP_UP && !$this->topUpInAdvance()) return false;

        $num = $request->input('coupon_num');
        if (!$num || !$this->userCoupon) return true;

        if ($this->insufficientCoupon()) return false;

        $couponID = $request->input('coupon_id', Coupon::ID_M_POINT);
        if (Coupon::ID_M_POINT == $couponID) return false;

        $updatedCount = $this->userCoupon->decrement('num', $num);
        $this->couponTransaction = CouponTransaction::add(
            $this->userCoupon->coupon_id,
            $this->userCoupon->coupon_value,
            $num,
            CouponTransaction::CREATED_FOR_SPENDING_PRE,
            $this->order->id
        );
        if ($updatedCount > 0 && !$this->couponTransaction->hasErrors()) {
            return true;
        } else {
            $this->code = HttpStatusCode::SERVER_SAVE_FAILED;
            return false;
        }
    }

    protected function topUpInAdvance()
    {
        /** @var SinopecApiEncapsulate $sinopec */
        $sinopec = App::make(SinopecApiEncapsulate::class);
        $user = Auth::user();
        $response = $sinopec->topUpInAdvance(
            $user->mobile, $user->gas_card_sn,
            $this->order->total_amount, $user->name, $this->order->coupon_amount
        );

        if (isset($response['code']) && $response['code'] == 100) {
            $this->addGasCardCharge($response);
            return true;
        } else {
            $this->code = HttpStatusCode::SERVER_THIRD_PARTY_ERROR;
            $this->errors = $response;
            return false;
        }
    }

    protected function addGasCardCharge($response)
    {
        $user = Auth::user();
        /** @var GasCardCharge $charge */
        $charge = GasCardCharge::create([
            'order_id' => $this->order->id,
            'distributor_order_id' => $response['data']['id'],
            'gas_card_sn' => $user->gas_card_sn,
            'total_amount' => $this->order->total_amount,
            'actual_amount' => $this->order->actual_amount,
            'coupon_amount' => $this->order->coupon_amount,
            'coupon_id' => $this->order->coupon_id,
            'created_by' => $this->order->belongs_to,
            'status' => GasCardCharge::STATUS_WAIT_CONFIRMING,
            'extra' => '[]',
        ]);

        return !$charge->hasErrors();
    }

    protected function validate(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'goods_id' => 'required|int',
            'num' => 'required|int',
            'coupon_id' => 'int',
            'coupon_num' => 'int',
        ]);

        if (!$validator->passes()) {
            $this->errors = $validator->errors()->toArray();
            return false;
        }
        return true;
    }

    protected function translatePlatform(Request $request)
    {
        switch ($this->getPlatformFromRequest($request)) {
            case Platform::WITH_ALI_PAY:
                return Order::PLATFORM_ALI_PAY;
            case Platform::WITH_WE_CHAT:
                return Order::PLATFORM_WE_CHAT;
            default :
                throw new \Exception('Payment platform not supported yet.');
        }
    }

    protected function getPlatformFromRequest(Request $request)
    {
        return $request->input('platform', Platform::WITH_ALI_PAY);
    }

    protected function getAppID(Request $request)
    {
        switch ($this->getPlatformFromRequest($request)) {
            case Platform::WITH_ALI_PAY:
                return config('pay.platforms.AliPay.app_id');
            case Platform::WITH_WE_CHAT:
                $clientType = $request->input('client_type');
                return config("pay.platforms.WeChat.$clientType.app_id");
            default:
                throw new \Exception('Invalid payment channel, cannot fetch app id');
        }
    }

    protected function getPayExtra(Request $request)
    {
        switch ($this->getPlatformFromRequest($request)) {
            case Platform::WITH_ALI_PAY:
                $extra = [
                    'timeout_express' => '1d', // order expires after one day
                ];
                break;
            case Platform::WITH_WE_CHAT:
                $extra = [
                ];
                break;
            default:
                throw new InvalidParameterException('The payment is not supported yet.');
        }
        return $extra;
    }
}