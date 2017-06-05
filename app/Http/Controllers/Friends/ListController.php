<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-05
 */

namespace App\Http\Controllers\Friends;

use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Models\UserFriends;
use fk\utility\Database\Eloquent\Model;
use fk\utility\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ListController extends ApiController
{

    public function index(Request $request)
    {
        /** @var LengthAwarePaginator $users */
        $users = Model::where('created_by', Auth::id())
            ->from('user_friends as uf')
            ->leftJoin('user as u', 'u.id', '=', 'uf.created_by')
            ->orderBy('uf.id', 'DESC')
            ->paginate($request->get('per_page', 1000), $this->listFields());
        $records = $users->toArray();
        $pagination = (new Collection($records))
            ->only('total', 'per_page', 'current_page', 'last_page', 'from', 'to')
            ->toArray();

        $this->result
            ->message('获取好友列表成功')
            ->list($records['data'] ?? [])
            ->extend(compact('pagination'));
    }

    protected function listFields()
    {
        return [
            'uf.id', 'friend_id', 'friend_nickname',
            'u.avatar', 'u.mobile', 'u.city_name', 'u.it_says'
        ];
    }

}