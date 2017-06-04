<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-22
 */

namespace App\Http\Controllers\Sinopec;

use App\Components\HttpStatusCode;
use App\Components\SinopecApiEncapsulate;
use App\Http\Controllers\ApiController;
use App\Models\TopUp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends ApiController
{

    public function store(Request $request, SinopecApiEncapsulate $sinopec)
    {
        $args = $this->validate($request, [
            'name' => 'required',
            'mobile' => 'required',
            'address' => 'required|string|max:255',
            'idcard' => 'required|string|max:18|min:15',
            'idcard_front' => 'required|file',
            'idcard_back' => 'required|file',
        ]);
//        $idCardFront = &$args['idcard_front'];
//        $idCardBack = &$args['idcard_back'];
//        $idCardFront = $sinopec->sendImages();
//        $idCardFront = $sinopec->sendImage($idCardBack);
        /**
         * @var array $result
         *  [
         *      img_name
         *      img_url
         *  ]
         */
        foreach ($sinopec->sendImages($args) as $result) {

        }

        $data = $sinopec->applyForCard(...array_values($args));
        if ($data['code'] == 100) {
            $user = new User();
            $user->fill($args);
            $user->gas_card_id = $data['data']['id'];
            if ($user->save()) {
                $this->result
                    ->message(__('base.Success'))
                    ->extend(['redirect' => $sinopec->payUrl()]);
            } else {
                $this->result
                    ->code(HttpStatusCode::SERVER_SAVE_FAILED)
                    ->message(__('sinopec-user.Failed in saving user'))
                    ->extend(['extra' => $user->errors->all()]);
            }
        } else {
            $this->result
                ->code(HttpStatusCode::SERVER_THIRD_PARTY_ERROR)
                ->message($data['message'] ?? __('sinopec-user.Failed to connect indoorbuy.com.'))
                ->extend(['extra' => $data]);
        }
    }

    public function topUp(Request $request, SinopecApiEncapsulate $sinopec)
    {
        $amountArray = [200, 400, 600, 800, 1000];
        $data = $this->validate($request, [
            'amount' => ['required', 'int', Rule::in($amountArray)],
            'pay_type' => ['required', 'string', Rule::in(TopUp::payTypes())],
            'gas_card_id' => 'required|int',
            'coupon_id' => 'int',
        ]);

        $model = new TopUp($data);
        if ($model->save()) {
            $this->result->message(__('top-up.Top up successfully. :hour', [':hour' => 24]));
        }
    }

}