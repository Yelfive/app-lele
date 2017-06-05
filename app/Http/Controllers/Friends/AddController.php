<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-05
 */

namespace App\Http\Controllers\Friends;

use App\Components\HttpStatusCode;
use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Models\UserFriends;
use fk\utility\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddController extends ApiController
{
    public function add(Request $request)
    {
        $this->validate($request, [
            'friend_id' => 'required|int|min:0'
        ]);

        $friendID = $request->input('friend_id');

        /** @var User $friend */
        $friend = User::where(['id' => $friendID, 'deleted' => User::DELETED_NO])->first();
        if (!$friend) {
            return $this->result->code(HttpStatusCode::CLIENT_NOT_FOUND)
                ->message('用户不存在,不能添加好友');
        }

        $alreadyFriends = UserFriends::where(['friend_id' => $friendID, 'created_by' => Auth::id()])->count();
        if ($alreadyFriends) {
            return $this->result->code(HttpStatusCode::SUCCESS_ACCEPTED)
                ->message('TA已经是您的好友了,不需要再添加啦');
        }

        DB::beginTransaction();
        /**
         * @var User $youAreMime
         * @var User $iAmYours
         */
        $youAreMime = UserFriends::create([
            'friend_id' => $friendID,
            'friend_nickname' => $friend->nickname,
            'created_by' => Auth::id(),
        ]);

        if (Auth::id() != $friendID) {
            $iAmYours = UserFriends::create([
                'friend_id' => Auth::id(),
                'friend_nickname' => Auth::user()->nickname,
                'created_by' => $friendID,
            ]);
        }
        if ($youAreMime->hasErrors() || Auth::id() != $friendID && $iAmYours->hasErrors()) {
            DB::rollBack();
            return $this->result->code(HttpStatusCode::SERVER_SAVE_FAILED)->message('添加好友失败,请稍后尝试');
        } else {
            DB::commit();
            return $this->result->message('添加好友成功');
        }
    }
}