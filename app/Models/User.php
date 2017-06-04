<?php

namespace App\Models;

use App\Components\SinopecApiEncapsulate;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\App;
use App\Events\{
    ModelSaving, UserCreated, UserUpdated
};

/**
 * Fields in the table `user`
 *
 * @property integer $id
 * @property string $name
 * @property string $idcard
 * @property string $mobile
 * @property string $address
 * @property integer $gas_card_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted
 * @property string $gas_card_sn
 * @property string $password_hash
 *
 * @property UserInfo $info
 * @property TpIndoorBuy|null $indoorBuy
 * @property UserCoupon|null $m_point
 */
class User extends Model implements Authenticatable
{

    const CREATED_BY = null;

    protected $events = [
        'creating' => ModelSaving::class,
        'created' => UserCreated::class,
        'updated' => UserUpdated::class
    ];

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'user';

    public function rules()
    {
        // todo: add field: `locked`
        return [
            'name' => ['string', 'max:50'],
            'idcard' => ['string', 'max:18'],
            'mobile' => ['string', 'max:11'],
            'address' => ['string', 'max:500'],
            'gas_card_id' => ['integer', 'min:0', 'max:4294967295'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
            'deleted' => ['integer', 'min:0', 'max:255'],
            'gas_card_sn' => ['string', 'max:20'],
            'password_hash' => ['string', 'max:100'],
        ];
    }

    public function info()
    {
        return $this->hasOne(UserInfo::class, 'id');
    }

    public function indoorBuy()
    {
        return $this->hasOne(TpIndoorBuy::class, 'created_by');
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        // TODO: Implement getAuthIdentifierName() method.
    }

    /**
     * Get the unique identifier for the user.
     * @see Auth::id()
     * @see \Illuminate\Auth\GuardHelpers::id()
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * Get the password for the user.
     * Used to check password hash against user input
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        // TODO: Implement getRememberToken() method.
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // TODO: Implement setRememberToken() method.
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        // TODO: Implement getRememberTokenName() method.
    }

    public function getExpressStatus()
    {
        $sn = Order::where('belongs_to', $this->id)
            ->where('status', '>=', Order::STATUS_SUCCESS)
            ->orderBy('id', 'desc')
            ->take(1)
            ->pluck('express_sn');
        $status = $sn === null ? -1 : ($sn === '' ? 0 : 1);
        return $status;
    }

    public function getProfile()
    {
        $data = $this->getAttributes(null, ['password_hash']);
        $data['express_status'] = $this->getExpressStatus();
        $data['coupons'] = $this->getCoupons();
        return $data;
    }

    protected function getCoupons()
    {
        return [
            $this->updateIndoorMPoint()
        ];
    }

    protected function updateIndoorMPoint()
    {
        $points = ['coupon_id' => Coupon::ID_M_POINT, 'coupon_num' => 0];;
        /** @var SinopecApiEncapsulate $sinopec */
        $sinopec = App::make(SinopecApiEncapsulate::class);
        $response = $sinopec->memberQuery($this->mobile);

//        $response = [
//            'code' => 100,
//            'data' => [
//                'balance' => 90,
//            ],
//        ];
        if (!$response
            || !isset($response['code'])
            || $response['code'] != 100
            || !isset($response['data']['balance'])
        ) {
            // TODO: log the failure, request and response
            return $points;
        }
        $pointBalance = $response['data']['balance'];

        /** @var Coupon $coupon */
        $coupon = Coupon::where('id', Coupon::ID_M_POINT)->first();
        $num = intval($pointBalance / $coupon->coupon_value);

        /** @var UserCoupon|null $mPoint */
        if (null == $mPoint = $this->m_point) {
            if (!$coupon) return false;
            $datetime = new Carbon();
            $mPoint = UserCoupon::create([
                'coupon_id' => Coupon::ID_M_POINT,
                'coupon_value' => $coupon->coupon_value,
                'belongs_to' => $this->id,
                'num' => $num,
                'got_at' => $datetime,
                'expires_at' => $datetime,
            ]);
            if (!$mPoint->hasErrors()) $points['coupon_num'] = $num;
            else var_dump($mPoint->errors);

            return $points;
        } else if ($mPoint->num != $num) {
            if ($mPoint->num > $num) {
                CouponTransaction::add(
                    '帮麦使用优惠券',
                    $coupon->id, $coupon->coupon_value, $mPoint->num - $num,
                    CouponTransaction::CREATED_FOR_SPENDING, 0
                );
            } else {
                CouponTransaction::add(
                    '帮麦消费返券',
                    $coupon->id, $coupon->coupon_value, $num - $mPoint->num,
                    CouponTransaction::CREATED_FOR_GETTING, 0
                );
            }
            $mPoint->update(['num' => $num]);
        }
        $points['coupon_num'] = $num;
        return $points;
    }

    public function m_point()
    {
        return $this->hasOne(UserCoupon::class, 'belongs_to', 'id');
    }

}