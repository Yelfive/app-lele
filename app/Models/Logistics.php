<?php

namespace App\Models;

/**
 * Fields in the table `logistics`
 *
 * @property integer $id
 * @property integer $company Code that represents the company this sn belongs to
 * @property string $sn Serial number for the logistics
 * @property integer $signed [Default 0] Whether the express order has be signed by the receiver
 * @property string $trace Trace of the logistics for the sn
 * @property integer $belongs_to This logistics record belongs to
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 *
 */
class Logistics extends Model
{

    const CREATED_BY = 'belongs_to';

    const SIGNED_NO = 0;
    const SIGNED_YES = 1;

    const COMPANY_YUAN_TONG = 1;

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'logistics';

    public function rules()
    {
        return [
            'company' => ['string', 'max:50'],
            'sn' => ['required', 'string', 'max:100'],
            'signed' => ['integer', 'min:0', 'max:255'],
            'trace' => ['required'],
            'belongs_to' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
        ];
    }

}