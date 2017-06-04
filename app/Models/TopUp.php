<?php

namespace App\Models;

use Illuminate\Validation\Rule;

/**
 * @property integer $id
 * @property float $amount
 * @property integer $created_by
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $pay_type
 * @property string $order_sn
 * @property string $tp_sn
 */
class TopUp extends Model
{

    const PAY_TYPE_WE_CHAT = 1;
    const PAY_TYPE_ALI_PAY = 2;

    public function rules()
    {
        return [
            'amount' => 'required|numeric',
            'created_by' => 'int',
            'created_at' => 'date',
            'updated_at' => 'date',
            'pay_type' => ['required', 'int', Rule::in(self::payTypes())],
            'order_sn' => 'required|string|max:50',
            'tp_sn' => 'required|string|max:50'
        ];
    }

    public static function payTypes()
    {
        return [self::PAY_TYPE_ALI_PAY, self::PAY_TYPE_WE_CHAT];
    }
}
