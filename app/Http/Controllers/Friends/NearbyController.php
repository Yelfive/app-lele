<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-26
 */

namespace App\Http\Controllers\Friends;

use App\Components\MongoDB;
use App\Http\Controllers\ApiController;
use fk\utility\Http\Request;

class NearbyController extends ApiController
{
    public function search(Request $request)
    {
        $this->validate($request, [
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ]);
        $distance = 60 * 1000;

        $collection = MongoDB::collection('user');

        $pipeline = [
            [
                '$geoNear' => [
                    'near' => [
                        'type' => 'Point',
                        'coordinates' => [floatval($request->get('longitude')), floatval($request->get('latitude'))]
                    ],
                    'maxDistance' => $distance,
                    'distanceField' => 'distance',
                    'spherical' => true
                ]
            ],
            [
                '$match' => [
                    'sex' => [
                        '$eq' => $request->get('sex'),
                    ]
                ],
            ],
        ];

        $list = MongoDB::paginate('user', $pipeline);

        $collection->createIndex(['location' => '2dsphere']);
//        $users = $collection->aggregate($pipeline);

//        $list = MongoDB::populate($users);
        $this->result
            ->message('查询成功')
            ->list($list);
    }
}