<?php

namespace App\Models;

use App\Events\UserCreating;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Fields in the table `user`
 *
 * @property integer $id
 * @property string $nickname User's nickname
 * @property string $state_code State code, +86=china
 * @property string $mobile Mobile phone number
 * @property string $avatar Avatar for the user
 * @property string $account User's LeLe Number
 * @property string $im_account [Default ''] Login of the IM
 * @property string $im_password [Default ''] Password of the IM
 * @property string $sex [Default 'unknown'] user gender
 * @property string $city_name Register location, city name
 * @property string $city_code Register location, city code
 * @property integer $age [Default 0]
 * @property string $it_says What he/she says
 * @property string $address
 * @property string $password_hash Hash of the password, not storing plain password in db
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $deleted [Default 0] Whether the user is deleted
 *
 *
 */
class User extends Model implements Authenticatable
{

    const DELETED_NO = 0;
    const DELETED_YES = 1;

    public $events = [
        'creating' => UserCreating::class
    ];

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'user';

    public function rules()
    {
        return [
            'nickname' => ['required', 'string', 'max:50'],
            'state_code' => ['required', 'string', 'max:10'],
            'mobile' => ['required', 'string', 'max:11', 'unique:user'],
            'avatar' => ['required', 'string', 'max:255'],
            'account' => ['required', 'string', 'max:20'],
            'sex' => ['string'],
            'city_name' => ['required', 'string', 'max:255'],
            'city_code' => ['required', 'string', 'max:255'],
            'im_account' => ['string', 'max:255'],
            'im_password' => ['string', 'max:255'],
            'age' => ['integer', 'min:0', 'max:255'],
            'it_says' => ['string', 'max:100000'],
            'address' => ['string', 'max:500'],
            'password_hash' => ['required', 'string', 'max:100'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
            'deleted' => ['integer', 'min:0', 'max:255'],
        ];
    }

    public function generateAccount(int $base = null)
    {
        $base = $base ?: $this->findBase();
        while (true) {
            if (!$this->skipAccount(++$base)) {
                break;
            }
        }
        return (string)$base;
    }

    public function setAccount()
    {
        $this->account = $this->generateAccount();
        return $this;
    }

    protected function skipAccount($number)
    {
        // same num repeated over 5/8 times
        // serial number like 12345xxx over 5/8 numbers
        // 11111000 11110001 11100011 11100000
        $percent = 5 / 8;
        $number = (string)$number;
        str_split($number, 1);
        $len = strlen($number);
        $maxAllowed = floor($len * $percent);

        // Check repeat
        $repeat = [];
        for ($i = 0; $i < $len; $i++) {
            $v = $number[$i];
            if (!isset($repeat[$v])) $repeat[$v] = 0;
            if (++$repeat[$v] >= $maxAllowed) {
                return true;
            }
        }

        // Check multi-bytes repeat
        // 10101010 12121212 12312312
        $splitLen = 2;
        $maxSplitLength = floor($len / 2);
        if ($maxSplitLength >= $splitLen) {
            $skip = true;
            $maxAllowedRepeat = $maxSplitLength - 1;
            $repeat = 1;
            for (; $splitLen <= $maxSplitLength; $splitLen++) {
                $array = str_split($number, $splitLen);
                $prev = array_shift($array);
                while ($next = array_shift($array)) {
                    if ($prev != $next) {
                        $skip = false;
                        break;
                    }

                    if (++$repeat >= $maxAllowedRepeat) {
                        return true;
                    }
                }
                if ($skip) return $skip;
            }
        }

        // Check continuous
        // 12345678
        $continued = 1;
        $ascend = true;
        for ($i = 2; $i < $len - 1; $i++) {
            $next = $number[$i];
            $prev = $number[$i - 1];
            if ($next == $prev + 1) {
                $ascend = true;
                if ($ascend === false) {
                    $continued = 0;
                    $ascend = false;
                }
            } else if ($next == $prev - 1) {
                if ($ascend === true) {
                    $continued = 0;
                }
            }

            if (++$continued > $maxAllowed) {
                return true;
            }

        }

        return false;
    }

    protected function findBase()
    {
        return User::orderBy('id', 'DESC')->take(1)->pluck('account')->first() ?: ('1' . str_repeat('0', 7));
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return int
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * Get the password for the user.
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

    public function getProfile()
    {
        $attributes = $this->getAttributes(null, ['password_hash', 'deleted', 'updated_at']);
        return $attributes;
    }
}