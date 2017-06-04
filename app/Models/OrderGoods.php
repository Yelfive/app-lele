<?php

namespace App\Models;

/**
 * Fields in the table `order_goods`
 *
 * @property integer $id 
 * @property integer $order_id 
 * @property integer $goods_id 
 * @property integer $goods_num Goods number
 * @property float $unit_price Unit goods price
 * @property float $total_amount Total money for this goods, num * unit_price
 * @property integer $buyer_id The buyer id
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 *
 *
 */
class OrderGoods extends Model 
{

    const CREATED_BY = 'buyer_id';

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'order_goods';

    public function rules()
    {
        return [
            'order_id' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'goods_id' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'goods_num' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'unit_price' => ['required', 'numeric'],
            'total_amount' => ['required', 'numeric'],
            'buyer_id' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
        ];
    }

}