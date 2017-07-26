<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-26
 */

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Admin\Controller;
use App\Models\User;

class IndexController extends Controller
{
    public function index()
    {
        $paginator = User::where('deleted', User::DELETED_NO)
            ->orderBy('id', 'desc')
            ->paginate(20);
        return view('user.index', [
            'paginator' => $paginator,
        ]);
    }
}