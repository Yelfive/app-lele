<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Query\Expression;

/**
 * Fields in the table `panda_f_code`
 *
 * @property integer $id
 * @property string $code F Code
 * @property integer $claimed_order_id [Default 0] The order id the f code is claimed with
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $claimed_at The time user claimed the code
 *
 *
 */
class PandaFCode extends Model
{

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'panda_f_code';

    public function rules()
    {
        return [
            'code' => ['required', 'string', 'max:100'],
            'claimed_order_id' => ['integer', 'min:0', 'max:255'],
            'created_at' => ['date'],
            'claimed_at' => ['date'],
        ];
    }

    /**
     * Pops a code:
     * Get a code, and mark the code claimed
     * @param integer $orderID
     * @return string
     */
    public static function pop($orderID): string
    {
        /** @var static $model */
        $model = static::where(new Expression('claimed_at!=created_at'))
            ->orWhere('claimed_order_id', $orderID)
            ->orderBy('id')
            ->first();
        if (!$model) return '0';

        if (!$model->claimed_order_id) {
            $model->update(['claimed_at' => new Carbon(), 'claimed_order_id' => $orderID]);
        }
        return $model->code;
    }

}