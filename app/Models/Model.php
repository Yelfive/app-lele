<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-05
 */

namespace App\Models;

use fk\utility\Database\Eloquent\Model as ModelBase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class Model extends ModelBase
{

    /**
     * @param LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @return array
     */
    public static function formatPaginate(LengthAwarePaginator $paginator)
    {
        $array = $paginator->toArray();
        $pagination = array_map(function ($value) {
            return (int)$value;
        }, Arr::only($array, ['total', 'per_page', 'current_page', 'last_page', 'from', 'to']));
        return [
            'list' => $array['data'] ?? [],
            'pagination' => $pagination,
        ];
    }
}