<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-26
 */

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Admin\Controller;
use App\Models\User;
use fk\utility\Database\Eloquent\Builder;
use fk\utility\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        /** @var Builder $builder */
        $builder = User::where('deleted', User::DELETED_NO)
            ->orderBy('id', 'desc');

        if ($keyword = $request->get('keyword')) {
            $builder->where(function (Builder $builder) use ($keyword) {
                $builder->where('mobile', 'like', "%$keyword%")
                    ->orWhere('nickname', 'like', "%$keyword%")
                    ->orWhere('account', 'like', "%$keyword%");
            });
        }

        $paginator = $builder->paginate($request->get('per_page', 20));
        return view('user.index', [
            'paginator' => $paginator,
        ]);
    }
}