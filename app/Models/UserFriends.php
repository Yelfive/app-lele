<?php

namespace App\Models;

/**
 * Fields in the table `user_friends`
 *
 * @property integer $id 
 * @property integer $created_by Record creator, indicates whose friend this is
 * @property integer $friend_id User ID of the friend
 * @property string $friend_nickname [Default ''] Nickname of the friend
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 *
 *
 */
class UserFriends extends Model 
{

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'user_friends';

    public function rules()
    {
        return [
            'created_by' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'friend_id' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'friend_nickname' => ['string', 'max:255'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
        ];
    }

}