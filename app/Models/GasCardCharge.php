<?php

namespace App\Models;

/**
 * Fields in the table `gas_card_charge`
 *
 * @property integer $id
 * @property integer $order_id Order ID to top up
 * @property integer $distributor_order_id Order ID of the card's distributor
 * @property string $gas_card_sn SN of the gas card
 * @property float $total_amount Total amount that should pay
 * @property float $actual_amount Amount that actually paid
 * @property float $coupon_amount [Default 0.00] The amount coupon deducts
 * @property integer $coupon_id [Default 0] The coupon ID, if exists
 * @property integer $status Status indicates such as succeeded updating TP
 * @property integer $created_by ID who tops up
 * @property string $extra JSON indicates log, mainly for error
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 *
 */
class GasCardCharge extends Model
{

    const STATUS_NOTIFY_TP_FAILED = -1;
    const STATUS_WAIT_CONFIRMING = 0;
    const STATUS_NOTIFY_TP_SUCCEEDED = 1;

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'gas_card_charge';

    public function rules()
    {
        return [
            'order_id' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'distributor_order_id' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'gas_card_sn' => ['required', 'string', 'max:255'],
            'total_amount' => ['required', 'numeric'],
            'actual_amount' => ['required', 'numeric'],
            'coupon_amount' => ['numeric'],
            'coupon_id' => ['integer', 'min:0', 'max:4294967295'],
            'status' => ['required', 'integer', 'min:-128', 'max:127'],
            'created_by' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'extra' => ['required'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
        ];
    }

}