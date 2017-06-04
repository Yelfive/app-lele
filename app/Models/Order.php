<?php

namespace App\Models;

/**
 * Fields in the table `order`
 *
 * @property integer $id
 * @property integer $category Category for the order, top up or apply for card staff
 * @property string $order_sn Order Serial Number
 * @property string $order_sn_tp [Default ''] Third-party order SN
 * @property float $total_amount Total amount paid for the order
 * @property float $actual_amount The money this order cost
 * @property integer $coupon_id [Default 0] ID of coupon used, if any
 * @property float $coupon_amount [Default 0.00] How much coupon used
 * @property integer $status [Default 0] Integer indicates the status of the order, 0=waiting paying,1=success,2=finished,-1=closed
 * @property integer $belongs_to ID of the user this order belongs to
 * @property string $tp_app_id Third party app id
 * @property string $tp_uid Third-party unique user identity token
 * @property integer $platform Platform the user used
 * @property string $channel [Default 0] The channel under platform, e.g. balance or quick pay
 * @property string $express_sn [Default ''] Express sn
 * @property string $express_code [Default ''] Express code,e.g. yuantong
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $deleted [Default 0] Where this order is deleted
 *
 */
class Order extends Model
{

    const CREATED_BY = 'belongs_to';

    const STATUS_CLOSED = -2;
    const STATUS_FAILED = -1;
    const STATUS_WAIT_PAYING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FINISHED = 2;

    const PLATFORM_ALI_PAY = 1;
    const PLATFORM_WE_CHAT = 2;

    const CHANNEL_NONE = '';

    /**
     * Category is associated with goods id
     * goods id from 100~999 is taken for system usage
     */
    const CATEGORY_DEFAULT = 100;
    const CATEGORY_APPLY_FOR_CARD = 101;
    const CATEGORY_TOP_UP = 102;

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'order';

    public function rules()
    {
        return [
            'category' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'order_sn' => ['required', 'string', 'max:255'],
            'order_sn_tp' => ['string', 'max:255'],
            'total_amount' => ['required', 'numeric'],
            'actual_amount' => ['required', 'numeric'],
            'coupon_id' => ['integer', 'min:0', 'max:4294967295'],
            'coupon_amount' => ['numeric'],
            'status' => ['integer', 'min:-128', 'max:127'],
            'belongs_to' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'tp_app_id' => ['required', 'string', 'max:255'],
            'tp_uid' => ['required', 'string', 'max:100'],
            'platform' => ['required', 'integer', 'min:0', 'max:255'],
            'channel' => ['string'],
            'express_sn' => ['string', 'max:100'],
            'express_code' => ['string', 'max:100'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
            'deleted' => ['integer', 'min:0', 'max:255'],
        ];
    }

    public static function getOrderCategoryByGoods($id)
    {
        if ($id + self::CATEGORY_DEFAULT <= 999) return $id + self::CATEGORY_DEFAULT;

        return self::CATEGORY_DEFAULT;
    }
}