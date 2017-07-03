<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-05
 */

namespace App\Http\Controllers\Friends;

use App\Components\HttpStatusCode;
use App\Components\MongoDB;
use App\Http\Controllers\ApiController;
use App\Models\FriendRequest;
use App\Models\User;
use App\Models\UserFriends;
use fk\ease\mob\IM;
use fk\utility\Database\Eloquent\Builder;
use fk\utility\Http\Request;
use fk\utility\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MongoDB\Model\BSONDocument;

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
        /** @var User $friend */
        $friend = User::where($condition)->where('deleted', User::DELETED_NO)->first(['id']);

        if (!$friend) {
            return $this->result->code(HttpStatusCode::CLIENT_NOT_FOUND)
                ->message('用户不存在,不能添加好友');
        }

        $requestSentAlready = FriendRequest::where(['friend_id' => $friend->id, 'sender' => Auth::id(), 'status' => FriendRequest::STATUS_UNHANDLED])->count();
        if ($requestSentAlready) {
            return $this->result
                ->code(HttpStatusCode::SUCCESS_ACCEPTED)
                ->message('已发送好友申请，不能重复发送');
        }

        $alreadyFriends = UserFriends::where(['friend_id' => $friend->id, 'created_by' => Auth::id()])->count();
        if ($alreadyFriends) {
            return $this->result->code(HttpStatusCode::SUCCESS_ACCEPTED)
                ->message('TA已经是您的好友了,不需要再添加啦');
        }

        $from = $friendID ? FriendRequest::FROM_SEARCH_NEARBY : FriendRequest::FROM_MOBILE_SPECIFY;

        FriendRequest::create([
            'sender' => Auth::id(),
            'friend_id' => $friend->id,
            'remark' => $request->input('remark', ''),
            'from' => $from,
            'distance' => $this->distanceToMe($friend),
        ]);

        return $this->result->message('好友请求发送成功');
    }

    protected function distanceToMe(User $user): int
    {
        /**
         * @var BSONDocument $document
         * @var BSONDocument $myDocument
         */
        $document = MongoDB::collection('user')->findOne(['_id' => $user->id]);
        $myDocument = MongoDB::collection('user')->findOne(['_id' => Auth::id()]);

        $longitude = $document['location']['coordinates'][0] ?? 0;
        $latitude = $document['location']['coordinates'][1] ?? 0;

        $myLongitude = $myDocument['location']['coordinates'][0] ?? 0;
        $myLatitude = $myDocument['location']['coordinates'][1] ?? 0;

        if (!$latitude || !$longitude || !$myLatitude || !$myLongitude) {
            return User::DISTANCE_UNKNOWN;
        }

        return (int)round(6378.138 * 1000 * 2 * asin(
                sqrt(
                    pow(sin(($myLatitude - $latitude) * pi() / 360), 2)
                    + cos($myLatitude * pi() / 180) * cos($latitude * pi() / 180)
                    * pow(sin(($myLongitude - $longitude) * pi() / 360), 2)
                )
            ));
    }

    public function agree(Request $request, IM $IM)
    {

        $this->validateData($request->input(), [
            'request_id' => 'required|int|min:0'
        ]);

        /** @var FriendRequest $friendRequest */
        $friendRequest = FriendRequest::where([
            'id' => $request->input('request_id'),
            'friend_id' => Auth::id()
        ])->first();

        if (!$friendRequest) {
            return $this->result
                ->code(HttpStatusCode::CLIENT_NOT_FOUND)
                ->message('该好友申请不存在');
        }

        /** @var User $friend */
        $friend = User::where('id', $friendRequest->sender)->first();
        if (!$friend) {
            return $this->result
                ->code(HttpStatusCode::CLIENT_NOT_FOUND)
                ->message('该用户不存在');
        }

        $alreadyFriends = UserFriends::where(['created_by' => Auth::id(), 'friend_id' => $friend->id])->count();
        if ($alreadyFriends) {
            return $this->result
                ->code(HttpStatusCode::CLIENT_BAD_REQUEST)
                ->message('你们已经是好友了，不能重复添加');
        }

        DB::beginTransaction();

        $friendRequest->update(['status' => FriendRequest::STATUS_AGREED]);
        if ($friendRequest->hasErrors()) {
            $this->agreeFailedResponse();
        }

        /**
         * @var User $youAreMime
         * @var User $iAmYours
         */
        $youAreMime = UserFriends::create([
            'friend_id' => $friendRequest->friend_id,
            'friend_nickname' => '',
            'created_by' => $friendRequest->sender,
        ]);

        $iAmYours = UserFriends::create([
            'friend_id' => $friendRequest->sender,
            'friend_nickname' => '',
            'created_by' => $friendRequest->friend_id,
        ]);

        if ($youAreMime->hasErrors() || $iAmYours->hasErrors()) {
            $this->agreeFailedResponse();
        } else {
            $IM->addFriend(Auth::user()->im_account, $friend->im_account);
            $this->agreeSuccessResponse();
        }
    }

    public function decline(Request $request)
    {
        $this->validateData($request->input(), [
            'request_id' => 'required|int|mix:0',
        ]);

        $friendRequest = FriendRequest::where([
            'id' => $request->input('id'),
            'friend_id' => Auth::id(),
        ]);

        if (!$friendRequest) {
            return $this->result
                ->code(HttpStatusCode::CLIENT_NOT_FOUND)
                ->message('没有找到对应好友请求');
        }

        /** @var FriendRequest $friendRequest */
        $friendRequest->update(['status' => FriendRequest::STATUS_DECLINED]);
        if ($errors = $friendRequest->errors) {
            $this->result
                ->code(HttpStatusCode::CLIENT_VALIDATION_ERROR)
                ->message('操作失败')
                ->extend(['errors' => $errors->toArray()]);
        } else {
            $this->result->message('操作成功');
        }
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
        $builder = FriendRequest::from('friend_request as r')
            ->select(['u.*', 'u.id as uid', 'r.*'])
            ->select([
                'u' => [
                    'id as uid', 'nickname', 'state_code', 'mobile', 'avatar', 'account',
                    'im_account', 'sex', 'city_name', 'city_code', 'age', 'it_says', 'address',
                ],
                'r' => ['id as request_id', 'sender', 'friend_id', 'distance', 'created_at', 'updated_at', 'status', 'from', 'remark'],
            ])
            ->leftJoin('user as u', function (JoinClause $join) {
                return $join->on('u.id', 'r.sender')->orOn('u.id', 'r.friend_id');
            })
            ->where(function (Builder $builder) {
                $builder->where('sender', Auth::id())
                    ->orWhere('friend_id', Auth::id());
            })
            ->where('u.id', '!=', Auth::id())
            ->orderBy('r.id', 'desc');

        $paginator = $builder->paginate();
        FriendRequest::$serializeDateAsInteger = true;

        $this->result->message('获取列表成功')
            ->extend($paginator->toFKStyle());
    }
}