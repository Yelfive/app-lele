<?php

namespace App\Models;

/**
 * Fields in the table `friend_request`
 *
 * @property integer $id
 * @property integer $sender ID of request sender
 * @property integer $friend_id ID of user the sender want to add
 * @property string $remark [Default '']
 * @property integer $from The request come from, e.g. mobile, user search
 * @property integer $distance Distance between sender and friend_id, when sending request
 * @property integer $status [Default 0] Status of the request, agree or decline
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $deleted_by [Default 0] Deleted by, 1=sender,2=friend,3=1|2
 *
 *
 */
class FriendRequest extends Model
{


    const FROM_MOBILE_SPECIFY = 1;
    const FROM_SEARCH_NEARBY = 2;

    const STATUS_DECLINED = -1;
    const STATUS_UNHANDLED = 0;
    const STATUS_AGREED = 1;

    const DELETED_BY_SENDER = 1;
    const DELETED_BY_RECEIVER = 2;

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'friend_request';

    public function rules()
    {
        return [
            'sender' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'friend_id' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'remark' => ['string', 'max:1000'],
            'from' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'distance' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'status' => ['integer', 'min:-128', 'max:127'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
            'deleted_by' => ['integer', 'min:0', 'max:255'],
        ];
    }

}