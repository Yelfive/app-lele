<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-25
 */

namespace app\Http\Controllers\User;

use App\Components\MongoDB;
use App\Http\Controllers\ApiController;
use fk\utility\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoordinateController extends ApiController
{
    public function update(Request $request)
    {
        $this->validate($request, [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $collection = MongoDB::collection('user');
        $collection->updateOne(['_id' => Auth::id()], [
            '$set' => [
                'location' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$request->input('longitude'), (float)$request->input('latitude')]
                ]
            ]
        ]);
        $collection->createIndex(['location' => '2dsphere'], ['background' => true]);
        $this->result->message('更新成功');
    }
}