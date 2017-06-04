<?php

namespace App\Models;

/**
 * Fields in the table `user_pool`
 *
 * @property integer $id
 * @property string $mobile Mobile number, used as
 * @property string $user_input All raw data of user input, json encoded
 * @property integer $paid [Default 0] Whether paid
 * @property integer $status [Default 0] 0=Unhandled, 1=Register Succeeded, -1=Register Failed, -2=Discarded due to mobile has been registered already
 * @property integer $order_id [Default 0] Related order id
 * @property string $extra Extra information, used to store third-party response
 * @property integer $uid [Default 0] User ID created by this pool row
 * @property integer $tp_uid User id of third-party, this is set after register at third party
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 *
 */
class UserPool extends Model
{

    const CREATED_BY = null;

    const PAID_YES = 1;
    const PAID_NO = 0;

    const STATUS_UNHANDLED = 0;
    const STATUS_REGISTER_SUCCEEDED = 1;
    const STATUS_REGISTER_FAILED = -1;
    const STATUS_DISCARDED = -2;

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'user_pool';

    public function rules()
    {
        return [
            'mobile' => ['required', 'string', 'max:11'],
            'user_input' => ['required'],
            'paid' => ['integer', 'min:0', 'max:255'],
            'status' => ['integer', 'min:-128', 'max:127'],
            'order_id' => ['integer', 'min:0', 'max:4294967295'],
            'extra' => ['required'],
            'uid' => ['integer', 'min:0', 'max:4294967295'],
            'tp_uid' => ['integer', 'min:0', 'max:4294967295'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
        ];
    }

}