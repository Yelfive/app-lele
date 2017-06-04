<?php

namespace App\Models;

use App\Events\ModelSaving;

/**
 * Fields in the table `user_info`
 *
 * @property integer $id
 * @property string $idcard_front
 * @property string $idcard_back
 * @property integer $updated_at
 * @property integer $tp_tables
 *
 */
class UserInfo extends Model
{

    const CREATED_BY = null;

    /**
     * Third-party tables, use binary number, so one filed can present multi-tables
     * 0B1
     * 0B10
     * 0B100
     * 0B1000
     */
    const TP_TABLES_NONE = 0;
    const TP_TABLES_INDOOR_BUY = 0B1;

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'user_info';

    protected $events = [
        'saving' => ModelSaving::class
    ];

    public function rules()
    {
        return [
            'idcard_front' => ['string', 'max:255'],
            'idcard_back' => ['string', 'max:255'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
            'tp_tables' => ['integer', 'min:0', 'max:4294967295'],
        ];
    }

}