<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-25
 */

namespace app\Http\Controllers\User;

use App\Components\HttpStatusCode;
use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Models\UserFriends;
use fk\utility\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomepageController extends ApiController
{
    public function profile($id, Request $request)
    {
        $this->validate($request, [
            'id' => 'required|int|mix:0'
        ]);

        /** @var User $user */
        $user = User::where([
            'deleted' => User::DELETED_NO,
            'id' => $request->get('id'),
        ]);

        if (!$user) {
            return $this->result
                ->code(HttpStatusCode::CLIENT_NOT_FOUND)
                ->message('用户不存在');
        }

        $isFriend = UserFriends::where(['friend_id' => $request->get('id'), 'created_by' => Auth::id()])->count();
        $data = $user->getProfile();
        $data['is_friend'] = $isFriend ? 1 : 0;

        $this->result
            ->message('获取资料成功')
            ->data($data);
    }
}