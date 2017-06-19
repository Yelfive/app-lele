<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-06
 */

namespace App\Http\Controllers\Supports;

use App\Http\Controllers\ApiController;
use App\Models\StateCode;
use fk\utility\Http\Request;
use fk\utility\Pagination\LengthAwarePaginator;

class StateController extends ApiController
{
    public function index(Request $request)
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = StateCode::orderBy('id')
            ->paginate($request->get('per_page', 1000));
        $extend = $paginator->toFKStyle();
        $this->result
            ->message('获取国家/地区代码成功')
            ->extend($extend);
    }
}