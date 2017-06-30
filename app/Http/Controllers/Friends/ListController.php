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


    protected function listFields()
    {
        return [
            'u' => [
                'id', 'nickname', 'state_code', 'mobile', 'avatar', 'account', 'im_account', 'sex',
                'city_name', 'city_code', 'age', 'it_says', 'address', 'created_at', 'updated_at'
            ],
            'uf' => ['friend_nickname']
        ];
    }

    public function index(Request $request)
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = Model::from('user_friends as uf')
            ->select($this->listFields())
            ->where('created_by', Auth::id())
            ->leftJoin('user as u', 'u.id', '=', 'uf.friend_id')
            ->where('uf.created_by', Auth::id())
            ->orderBy('uf.id', 'DESC')
            ->paginate($request->get('per_page', 1000));

        $this->result
            ->message('获取好友列表成功')
            ->extend($paginator->toFKStyle());
    }

}