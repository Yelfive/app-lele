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
            'page' => 'integer|min:1',
            'page_size' => 'integer|min:1|max:200',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ]);
        $distance = 60 * 1000;

        $pipeline = [];
        $geoNear = [
            'near' => [
                'type' => 'Point',
                'coordinates' => [floatval($request->get('longitude')), floatval($request->get('latitude'))]
            ],
            'maxDistance' => $distance,
            'distanceField' => 'distance',
            'spherical' => true
        ];

        if ($sex = $request->get('sex')) {
            $geoNear['query'] = [
                'sex' => [
                    '$eq' => $sex
                ]
            ];
        }

        if ($timestamp = $request->get('location_uploaded_at')) {
            $geoNear['query']['location_uploaded_at'] = [
                '$gt' => $timestamp
            ];
        }

        $pipeline['$geoNear'] = $geoNear;

        $list = MongoDB::paginate('user', $pipeline, $request->get('per_page', 20));

        $this->result
            ->message('查询成功')
            ->extend($list);
    }
}