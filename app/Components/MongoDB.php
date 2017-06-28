<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-26
 */

namespace App\Components;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use MongoDB\Client;
use MongoDB\Collection;
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
            $data = $item->getArrayCopy();
            unset(
                $data['_id'], $data['deleted'], $data['latitude'], $data['longitude'],
                $data['location'], $data['password_hash']
            );
            $data['distance'] = (int)$data['distance'];
            $data['updated_at'] = strtotime($data['updated_at']);
            $data['created_at'] = strtotime($data['created_at']);
            $list[] = $data;
        }
        return $list;
    }

    public static function paginate(string $table, array $query, $pageSize = 20, $pageParam = 'page')
    {
        $page = (int)Request::get($pageParam, 1);
        $collection = static::collection($table);

        $pipeline = [];
        foreach ($query as $k => $v) {
            $pipeline[] = [$k => $v];
        }

        $totalCount = static::count($collection, $pipeline);

        $pipeline[] = ['$skip' => intval($pageSize * ($page - 1))];
        $pipeline[] = ['$limit' => (int)$pageSize];
        /** @var Cursor $cursor */
        $cursor = $collection->aggregate($pipeline);
        $list = static::populate($cursor);
        $pagination = [
            'total' => $totalCount,
            'per_page' => $pageSize,
            'current_page' => $page,
            'last_page' => (int)ceil($totalCount / $pageSize),
            'from' => $pageSize * ($page - 1),
        ];
        $pagination['to'] = $page === $pagination['last_page'] ? $totalCount - $pagination['from'] : $pageSize * $page;

        return [
            'list' => $list,
            'pagination' => $pagination
        ];
    }

    protected static function count(Collection $collection, $pipeline): int
    {
        $pipeline[]['$count'] = 'total_count';

        return $collection->aggregate($pipeline)->toArray()[0]['total_count'] ?? 0;
    }
}