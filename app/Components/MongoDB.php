<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-26
 */

namespace App\Components;

use Illuminate\Support\Facades\Request;
use MongoDB\Client;
use MongoDB\Driver\Cursor;
use MongoDB\Model\BSONDocument;

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

    /**
     * @param \Traversable|Cursor|BSONDocument[] $cursor
     * @return array
     */
    public static function populate(Cursor $cursor)
    {
        $list = [];
        foreach ($cursor as $item) {
            $list[] = $item->getArrayCopy();
        }
        return $list;
    }

    public static function paginate(string $collection, array $pipeline, $pageSize = 20, $pageParam = 'page')
    {
        $page = Request::get($pageParam, 1);
        $cursor = static::collection($collection)->aggregate($pipeline, [
//            '$limit' => $pageSize,
//            '$offset' => $pageSize * ($page - 1)
        ]);
        return static::populate($cursor);
    }
}