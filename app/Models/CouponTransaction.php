<?php

namespace App\Models;

/**
 * Fields in the table `coupon_transaction`
 *
 * @property integer $id
 * @property integer $coupon_id
 * @property string $description Description of the coupon used, mainly coupon name
 * @property float $coupon_value
 * @property integer $num The number of coupon used/got
 * @property float $coupon_amount Total deduct amount
 * @property integer $created_for [Default 0] The record is created for, 0=get,1=pre-spend, 2=spend
 * @property integer $order_id [Default 0] The order this coupon is used with, when created_for>0
 * @property integer $created_by ID who used/got the coupon
 * @property \Carbon\Carbon $created_at When the coupon is used/got
 * @property integer $deleted [Default 0] Whether this is deleted
 *
 *
 */
class CouponTransaction extends Model
{

    const UPDATED_AT = null;

    const CREATED_FOR_GETTING = 0;
    const CREATED_FOR_SPENDING_PRE = 1;
    const CREATED_FOR_SPENDING = 2;

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'coupon_transaction';

    public function rules()
    {
        return [
            'coupon_id' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'description' => ['required', 'string', 'max:255'],
            'coupon_value' => ['required', 'numeric'],
            'num' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'coupon_amount' => ['required', 'numeric'],
            'created_for' => ['required', 'integer', 'min:0', 'max:255'],
            'order_id' => ['integer', 'min:0', 'max:4294967295'],
            'created_by' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'created_at' => ['date'],
            'deleted' => ['integer', 'min:0', 'max: 255']
        ];
    }

    /**
     * @param integer $couponID
     * @param float $couponValue
     * @param integer $num
     * @param integer $createdFor
     * @param $orderID
     * @param string|null $description
     * @return $this
     */
    public static function add($couponID, $couponValue, $num, $createdFor, $orderID, $description = null)
    {
        return static::create([
            'description' => $description ?? static::getCouponTransactionDescription($couponID),
            'coupon_id' => $couponID,
            'coupon_value' => $couponValue,
            'num' => $num,
            'coupon_amount' => $couponValue * $num,
            'created_for' => $createdFor,
            'order_id' => $orderID,
        ]);
    }


    protected static function getCouponTransactionDescription($couponID)
    {
        switch ($couponID) {
            case Coupon::ID_M_POINT:
                return '加油券使用'; // TODO: i18n
        }
        return '';
    }

}