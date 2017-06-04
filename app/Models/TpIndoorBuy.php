<?php

namespace App\Models;

/**
 * Fields in the table `tp_indoor_buy`
 *
 * @property integer $id
 * @property integer $tp_id ID of indoor-buy
 * @property integer $gas_card_id Gasoline card ID
 * @property float $m_point M-Point at indoor-buy
 * @property string $idcard_front Url of the front image of the id card
 * @property string $idcard_back Url of the back image of the id card
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $created_by User id this record belongs to
 *
 *
 */
class TpIndoorBuy extends Model
{

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'tp_indoor_buy';

    public function rules()
    {
        return [
            'tp_id' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'm_point' => ['required', 'numeric'],
            'gas_card_id' => ['integer', 'min:0', 'max:4294967295'],
            'idcard_front' => ['required', 'string', 'max:255'],
            'idcard_back' => ['required', 'string', 'max:255'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
            'created_by' => ['required', 'integer', 'min:0', 'max:4294967295'],
        ];
    }

}