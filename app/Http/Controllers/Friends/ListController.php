<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-05
 */

namespace App\Http\Controllers\Friends;

use App\Http\Controllers\ApiController;
use App\Models\Model;
use fk\utility\Http\Request;
use fk\utility\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ListController extends ApiController
{

    public function index(Request $request)
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = Model::where('created_by', Auth::id())
            ->from('user_friends as uf')
            ->leftJoin('user as u', 'u.id', '=', 'uf.created_by')
            ->where('uf.created_by', Auth::id())
            ->orderBy('uf.id', 'DESC')
            ->paginate($request->get('per_page', 1000), $this->listFields());

        $this->result
            ->message('获取好友列表成功')
            ->extend($paginator->toFKStyle());
    }

    protected function listFields()
    {
        return [
            'uf.id', 'friend_id', 'friend_nickname',
            'u.avatar', 'u.mobile', 'u.city_name', 'u.it_says'
        ];
    }

}