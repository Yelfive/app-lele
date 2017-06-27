<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-05
 */

namespace App\Http\Controllers\Friends;

use App\Components\HttpStatusCode;
use App\Http\Controllers\ApiController;
use App\Models\FriendRequest;
use App\Models\User;
use App\Models\UserFriends;
use fk\utility\Http\Request;
use fk\utility\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestController extends ApiController
{
    public function send(Request $request)
    {
        $this->validate($request, [
            'friend_id' => 'required_without:mobile|int|min:0',
            'mobile' => 'required_without:friend_id|string|size:11',
        ]);

        if ($friendID = $request->input('friend_id')) {
            $condition = ['id' => $friendID];
        } else {
            $condition = ['mobile' => $request->input('mobile')];
        }
        /** @var int $friend */
        $friend = User::where($condition)->where('deleted', User::DELETED_NO)->count();

        if (!$friend) {
            return $this->result->code(HttpStatusCode::CLIENT_NOT_FOUND)
                ->message('用户不存在,不能添加好友');
        }

        $alreadyFriends = UserFriends::where(['friend_id' => $friendID, 'created_by' => Auth::id()])->count();
        if ($alreadyFriends) {
            return $this->result->code(HttpStatusCode::SUCCESS_ACCEPTED)
                ->message('TA已经是您的好友了,不需要再添加啦');
        }

        $from = $request->input('friend_id') ? FriendRequest::FROM_SEARCH_NEARBY : FriendRequest::FROM_MOBILE_SPECIFY;

        FriendRequest::create([
            'sender' => Auth::id(),
            'friend_id' => $friendID,
            'remark' => $request->input('remark', ''),
            'from' => $from,
        ]);
    }

    public function agree(Request $request)
    {

        $this->validateData($request->input(), [
            'request_id' => 'required|int|min:0'
        ]);

        /** @var FriendRequest $friendRequest */
        $friendRequest = FriendRequest::where([
            'request_id' => $request->input('request_id'),
            'friend_id' => Auth::id()
        ])->first();

        if (!$friendRequest) {
            return $this->result
                ->code(HttpStatusCode::CLIENT_NOT_FOUND)
                ->message('该好友申请不存在');
        }

        /** @var User $friend */
        $friend = User::where('id', $friendRequest->friend_id);

        DB::beginTransaction();

        $friendRequest->update(['status' => FriendRequest::STATUS_AGREE]);
        if ($friendRequest->hasErrors()) {
            $this->agreeFailedResponse();
        }

        /**
         * @var User $youAreMime
         * @var User $iAmYours
         */
        $youAreMime = UserFriends::create([
            'friend_id' => $friendRequest->friend_id,
            'friend_nickname' => $friend->nickname,
            'created_by' => $friendRequest->sender,
        ]);

        $iAmYours = UserFriends::create([
            'friend_id' => $friendRequest->sender,
            'friend_nickname' => Auth::user()->nickname,
            'created_by' => $friendRequest->friend_id,
        ]);

        if ($youAreMime->hasErrors() || $iAmYours->hasErrors()) {
            $this->agreeFailedResponse();
        } else {
            $this->agreeSuccessResponse();
        }
    }

    public function decline(Request $request)
    {
        $this->validateData($request->input(), [
            'request_id' => 'required|int|mix:0',
        ]);

        $friendRequest = FriendRequest::where([
            'request_id' => $request->input('id'),
            'friend_id' => Auth::id(),
        ]);

        if (!$friendRequest) {
            return $this->result
                ->code(HttpStatusCode::CLIENT_NOT_FOUND)
                ->message('没有找到对应好友请求');
        }

        $friendRequest->update(['status' => FriendRequest::STATUS_DECLINE]);
    }

    protected function agreeSuccessResponse()
    {
        DB::commit();
        return $this->result->message('添加好友成功');
    }

    protected function agreeFailedResponse()
    {
        DB::rollBack();
        return $this->result
            ->code(HttpStatusCode::SERVER_SAVE_FAILED)
            ->message('添加好友失败,请稍后尝试');
    }

    public function index()
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = FriendRequest::where([
            'friend_id' => Auth::id(),
            'status' => FriendRequest::STATUS_UNHANDLED,
        ])->paginate();
        FriendRequest::$serializeDateAsInteger = true;

        $this->result->message('获取列表成功')
            ->extend($paginator->toFKStyle());
    }
}