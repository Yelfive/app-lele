<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-26
 */

namespace App\Components;

use MongoDB\Client;

class MongoDB
{

    /**
     * @param string $collection
     * @param string $database
     * @return \MongoDB\Collection
     */
    public static function collection($collection, $database = 'lele')
    {
        $client = new Client(config('mongo.uri'));
        return $client->$database->$collection;
    }
}