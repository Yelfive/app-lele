<?php

namespace App\Models;

/**
 * Fields in the table `user_coupon`
 *
 * @property integer $id
 * @property integer $coupon_id
 * @property float $coupon_value
 * @property integer $belongs_to
 * @property integer $num How many coupons user has
 * @property \Carbon\Carbon $got_at When the coupon got
 * @property \Carbon\Carbon $expires_at When the coupon expires, 0000-00-00 00:00:00 means never expire
 *
 *
 */
class UserCoupon extends Model
{

    const CREATED_AT = null;
    const UPDATED_AT = null;
    const CREATED_BY = 'belongs_to';

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'user_coupon';

    public function rules()
    {
        return [
            'coupon_id' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'coupon_value' => ['required', 'numeric'],
            'belongs_to' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'num' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'got_at' => ['date'],
            'expires_at' => ['date'],
        ];
    }

}