<?php

namespace App\Models;

/**
 * Fields in the table `coupon`
 *
 * @property integer $id
 * @property float $coupon_value Value of the coupon, in CNY
 * @property string $description Description of the coupon, e.g. where it comes from
 * @property integer $rule How the coupon should be spent
 * @property integer $created_by ID of the user this coupon belongs to
 * @property \Carbon\Carbon $created_at [Default 'CURRENT_TIMESTAMP'] When this coupon is created
 * @property integer $deleted [Default 0] Deleted or not, default 0
 *
 *
 */
class Coupon extends Model
{

    /**
     * ID <= 1000 is reserved for system usage
     */
    const ID_M_POINT = 1;

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'coupon';

    public function rules()
    {
        return [
            'coupon_value' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:255'],
            'rule' => ['required', 'integer', 'min:0', 'max:255'],
            'created_by' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'created_at' => ['date'],
            'deleted' => ['integer', 'min:0', 'max:255'],
        ];
    }

}