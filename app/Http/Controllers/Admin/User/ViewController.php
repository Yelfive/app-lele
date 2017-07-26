<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-26
 */

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Admin\Controller;
use App\Models\User;

class ViewController extends Controller
{
    public function view($id)
    {
        $user = User::where('id', $id)->first();

        return view('user.view', [
            'user' => $user
        ]);
    }

    public function delete($id)
    {
        User::where('id', $id)->delete();
        return redirect()->to('admin/users');
    }
}