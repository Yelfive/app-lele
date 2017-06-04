<?php

namespace App\Models;

/**
 * Fields in the table `transaction`
 *
 * @property integer $id
 * @property string $summary Summary of the remark
 * @property string $remark The description for this transaction
 * @property float $amount How much this transaction is about, positive for top-upping, negative for spending
 * @property integer $outgoing [Default 0] 0-1 to indicates whether this transaction is outgoing one, where gasoline pays to the third-party
 * @property integer $category What this log generated for
 * @property integer $created_by ID of user creates this transaction
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 *
 */
class Transaction extends Model
{

//    const TYPE_APPLY_FOR_CARD = 1;
//    const TYPE_TOP_UP = 2;

    const OUTGOING_NO = 0;
    const OUTGOING_YES = 1;

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'transaction';

    public function rules()
    {
        return [
            'summary' => ['required', 'string', 'max:100'],
            'remark' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'outgoing' => ['integer', 'min:0', 'max:255'],
            'category' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'created_by' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
        ];
    }

    public static function add($amount, $summary, $category, $remark = null, $outgoing = self::OUTGOING_NO, $createdBy = null)
    {
        if (is_string($remark)) $remark = '交易成功';

        $data = compact('amount', 'summary', 'category', 'remark', 'outgoing');
        if (is_numeric($createdBy)) $data['created_by'] = $createdBy;

        $model = new static($data);
        $model->save();
        return $model;
    }

}