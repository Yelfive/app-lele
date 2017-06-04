<?php

namespace App\Models;

/**
 * Fields in the table `goods`
 *
 * @property integer $id
 * @property string $name Goods name
 * @property float $price Goods price
 * @property string $description Goods description
 * @property integer $on_shelf Whether the goods is on shelf for sale
 * @property string $created_at
 * @property string $updated_at
 *
 */
class Goods extends Model
{

    const CATEGORY_APPLY_FOR_CARD = 1;
    const CATEGORY_TOP_UP = 2;

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'goods';

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:1000'],
            'on_shelf' => ['required', 'integer', 'min:0', 'max:255'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
        ];
    }

}