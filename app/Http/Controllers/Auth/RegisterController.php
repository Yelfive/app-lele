<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-09
 */

namespace app\Http\Controllers\Auth;

use App\Components\HttpStatusCode;
use App\Components\Pay;
use App\Components\SinopecApiEncapsulate;
use App\Http\Controllers\ApiController;
use App\Models\Goods;
use App\Models\Order;
use App\Models\TpIndoorBuy;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserPool;
use fk\pay\config\Platform;
use fk\utility\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class RegisterController extends ApiController
{
    use RegistersUsers;

    protected $cardTypes = ['sinopec'/*, 'petro'*/];

    /**
     * Handle a registration request for the application.
     *
     * @param Request $request
     * @param SinopecApiEncapsulate $sinopec
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request, SinopecApiEncapsulate $sinopec)
    {
        $data = $this->validateAndRetrieve($request);

        $response = $sinopec->memberRegister($request->input('mobile'), $request->input('nickname'));
        if (isset($response['code']) && $response['code'] == 100) {
            $extra = '[]';
            $tpUID = $response['data']['id'];
        } else {
            $extra = $response ? json_encode($response, JSON_UNESCAPED_UNICODE) : '[]';
            $tpUID = 0;
        }

        $start = strlen(base_path()) + 1;
        $data['idcard_back'] = substr($sinopec->fileStore($data['idcard_back']), $start);
        $data['idcard_front'] = substr($sinopec->fileStore($data['idcard_front']), $start);

        /** @var UserPool $pool */
        $pool = UserPool::create([
            'mobile' => $data['mobile'],
            'user_input' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'paid' => UserPool::PAID_NO,
            'status' => UserPool::STATUS_UNHANDLED,
            'order_id' => 0,
            'tp_uid' => $tpUID,
            'extra' => $extra,
        ]);

        if (!$tpUID) {
            return $this->result->code(HttpStatusCode::SERVER_THIRD_PARTY_ERROR)
                ->message('该手机号已经注册,请更用其他手机号.'); // TODO: i18n
        }

        if ($pool->errors) {
            $this->result
                ->code(HttpStatusCode::CLIENT_VALIDATION_ERROR)
                ->message('请求参数验证失败')// TODO: i18n
                ->extend(['errors' => $pool->errors->toArray()]);
        } else if (false === $order = $this->pay($request)) {
            $this->result->message('生成订单失败'); // TODO: i18n
        } else if ($pool->update(['order_id' => $order->id])) {
            $this->result->message('生成订单成功'); // TODO: i18n
        } else {
            $this->result
                ->code(HttpStatusCode::SERVER_SAVE_FAILED)
                ->message('提交失败')// TODO: i18n
                ->extend(['error' => '无法更新用户池用户开卡订单ID']); // TODO: i18n
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    protected function validateAndRetrieve(Request $request)
    {
        return $this->validate($request, [
            'name' => 'required|string|max:50',
            'mobile' => 'required|unique:user|string|size:11',
            'address' => 'required|string|max:255',
            'idcard' => 'required|string|max:18|min:15',
            'password' => 'required|string|min:6',
            'idcard_front' => 'required|file',
            'idcard_back' => 'required|file',
            'apply_for' => ['string', Rule::in($this->cardTypes)],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param Request $request
     * @return User
     */
    protected function create(Request $request): User
    {
        $args = $this->validateAndRetrieve($request);
        $args['password_hash'] = Hash::make($args['password']);
        $user = new User($args);

        // Save anyway
        // So that even when the third party call failed,
        // we still have the user information
        if (!$user->save()) {
            $this->result
                ->code(HttpStatusCode::CLIENT_VALIDATION_ERROR)
                ->message(__('sinopec-user.Failed in saving user'))
                ->extend(['errors' => $user->errors->toArray()]);
            return $user;
        }

        $this->guard()->login($user);

        // Calling third part registering
        $applyFor = $request->input('apply_for', $this->cardTypes[0]);
        $method = 'applyFor' . ucfirst($applyFor);
        $this->$method($args, $user);
        return $user;
    }

    protected function applyForSinopec(array $args, User $user)
    {
        /** @var SinopecApiEncapsulate $sinopec */
        $sinopec = App::make(SinopecApiEncapsulate::class);
        $sinopec->checkFake();

        $registered = $sinopec->memberRegister($user->mobile, $user->name);
        if (!isset($registered['code']) || $registered['code'] != 100) {
            DB::connection()->rollBack();
            $this->result
                ->code(HttpStatusCode::SERVER_THIRD_PARTY_ERROR)
                ->message(__('sinopec.Register failed'))
                ->extend(['indoor_buy_register' => $registered]);
            return false;
        }
        DB::connection()->commit();

        /**
         * @var array $result
         *  [
         *      img_name
         *      img_url
         *  ]
         */
        $images = $sinopec->sendImages(Arr::only($args, ['idcard_front', 'idcard_back']));

        // Update User Profile
        $start = strlen($sinopec->getImageDirectory());
        $info = $user->info;
        $info->tp_tables = UserInfo::TP_TABLES_INDOOR_BUY | $info->tp_tables;
        $info->idcard_front = substr($images['idcard_front']['path'], $start);
        $info->idcard_back = substr($images['idcard_back']['path'], $start);
        if (!$info->update()) {
            $this->result
                ->code(HttpStatusCode::CLIENT_VALIDATION_ERROR)
                ->message('Saving tp_tables failed')
                ->extend(['info' => $info->getAttributes()]);
            return false;
        }

        foreach ($images as $k => $image) {
            $args[$k] = $image['img_name'];
        }

        $data = $sinopec->applyForCard(
            $args['name'], $args['mobile'], $args['idcard'], $args['address'],
            $args['idcard_front'], $args['idcard_back']
        );

        if (is_array($data) && $data['code'] == 100 && isset($data['data']['id'])) {

            $indoorBuy = new TpIndoorBuy([
                'tp_id' => $registered['data']['id'] ?? 0,
                'idcard_front' => $images['idcard_front']['img_url'],
                'idcard_back' => $images['idcard_back']['img_url'],
            ]);

            $indoorBuy->created_by = $user->id;

            if (!$indoorBuy->save()) {
                $indoorBuy->errors;
            }

            $user->gas_card_id = $data['data']['id'];
            if ($user->save()) {
                if ($this->pay(App::make('request'))) {
                    $this->result
                        ->message(__('base.Success'));
                }
            } else {
                $this->result
                    ->code(HttpStatusCode::SERVER_SAVE_FAILED)
                    ->message(__('sinopec-user.Failed in saving user'))
                    ->extend(['card_apply' => $user->errors->all() ?: []]);
            }
        } else {
            $this->result
                ->code(HttpStatusCode::SERVER_THIRD_PARTY_ERROR)
                ->extend(['card_apply' => $data ?: []]);

            $this->result->message($data['message'] ?? (
                $sinopec->code === SinopecApiEncapsulate::CODE_CONNECT_FAILED
                    ? __('sinopec-user.Failed to connect indoorbuy.com.')
                    : __('sinopec-user.Result from indoorbuy.com is not json.:response', [':response' => $sinopec->getResponse()])
                )
            );
        }
    }

    /**
     * @param Request $request
     *  - goods_id=1 gasoline card
     *  - num=1 goods count
     *  - channel=AliPay WeChat AliPay
     * @return false|Order
     * @throws \Exception
     * @throws \fk\pay\Exception
     */
    protected function pay(Request $request)
    {
        $pay = new Pay();

        $request->request->set('goods_id', Goods::CATEGORY_APPLY_FOR_CARD);
        $request->request->set('num', 1);
        if ($form = $pay->generateOrder($request, ['belongs_to' => 0])) {
            $this->result->extend(['form' => $form]);
            return $pay->order;
        } else {
            $this->result->code(HttpStatusCode::SERVER_SAVE_FAILED)
                ->extend(['order_errors' => $pay->errors]);
            return false;
        }
    }

    protected function translatePlatform(Request $request)
    {
        switch ($this->getPlatformFromRequest($request)) {
            case Platform::WITH_ALI_PAY:
                return Order::PLATFORM_ALI_PAY;
            case Platform::WITH_WE_CHAT:
                return Order::PLATFORM_WE_CHAT;
            default :
                throw new \Exception('Payment platform not supported yet.');
        }
    }

    protected function getPlatformFromRequest(Request $request)
    {
        return $request->input('platform', Platform::WITH_ALI_PAY);
    }

    protected function getAppID(Request $request)
    {
        switch ($this->getPlatformFromRequest($request)) {
            case Platform::WITH_ALI_PAY:
                return config('pay.platforms.AliPay.app_id');
            case Platform::WITH_WE_CHAT:
                $clientType = $request->input('client_type');
                return config("pay.platforms.WeChat.$clientType.app_id");
            default:
                throw new \Exception('Invalid payment channel, cannot fetch app id');
        }
    }

    protected function getPayExtra(Request $request)
    {
        switch ($this->getPlatformFromRequest($request)) {
            case Platform::WITH_ALI_PAY:
                $extra = [
                    'timeout_express' => '1d', // order expires after one day
                ];
                break;
            case Platform::WITH_WE_CHAT:
                $extra = [
                ];
                break;
            default:
                throw new InvalidParameterException('The payment is not supported yet.');
        }
        return $extra;
    }

}