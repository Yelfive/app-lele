<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-05
 */

namespace App\Http\Controllers\Friends;

use App\Http\Controllers\ApiController;
use App\Models\UserFriends;
use fk\utility\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ListController extends ApiController
{

    public function index(Request $request)
    {
        /** @var LengthAwarePaginator $users */
        $users = UserFriends::where('created_by', Auth::id())
            ->orderBy('id', 'DESC')
            ->paginate($request->get('per_page', 1000), ['id', 'friend_id', 'friend_nickname']);
        $records = $users->toArray();
        $pagination = (new Collection($records))
            ->only('total', 'per_page', 'current_page', 'last_page', 'from', 'to')
            ->toArray();

        $this->result
            ->message('获取好友列表成功')
            ->list($records['data'] ?? [])
            ->extend(compact('pagination'));
    }

}