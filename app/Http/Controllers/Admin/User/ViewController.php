<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-26
 */

namespace App\Http\Controllers\Admin\User;

use App\Components\MongoDB;
use App\Http\Controllers\Admin\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();
        User::where('id', $id)->delete();
        MongoDB::collection('user')->deleteOne(['_id' => $id]);
        /** @var \MongoDB\DeleteResult $result */
        $result = MongoDB::collection('user')->deleteOne(['_id' => (int)$id]);
        if ($result->getDeletedCount()) {
            DB::commit();
        } else {
            DB::rollBack();
        }

        return redirect()->to('admin/users');
    }
}