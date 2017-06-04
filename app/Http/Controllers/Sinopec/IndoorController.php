<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-17
 */

namespace App\Http\Controllers\Sinopec;

use App\Components\HttpStatusCode;
use App\Components\SinopecApiEncapsulate;
use App\Http\Controllers\ApiController;
use App\Models\TpIndoorBuy;
use App\Models\User;
use fk\utility\Http\Request;
use Illuminate\Support\Facades\App;

class IndoorController extends ApiController
{

    public function synchronizePoints(Request $request, SinopecApiEncapsulate $sinopec)
    {

        $data = $request->input();
        if (!$data) {
            return $this->result
                ->code(HttpStatusCode::CLIENT_EXPECTATION_FAILED)
                ->message('You visit here without carrying expected data');
        }

        $valid = $this->validateData($data, [
            'current_points' => 'required|integer',
            'id' => 'required|integer',
        ]);

        if (!$valid) {
            return $this->result
                ->code(HttpStatusCode::CLIENT_VALIDATION_ERROR)
                ->message('Invalid params')
                ->extend(['errors' => $this->errors->toArray()]);
        }

        $updated = TpIndoorBuy::where('tp_id', $data['id'])->update(['m_point' => $data['current_points']]);
        if ($updated) {
            $this->result->message('Points synchronized');
        } else {
            $this->result->code(HttpStatusCode::SERVER_SAVE_FAILED)->message('Failed updating points');
        }
    }

}