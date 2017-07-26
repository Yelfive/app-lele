<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Fields in the table `admin`
 *
 * @property integer $id 
 * @property string $username Username for login
 * @property string $nickname Nickname of the admin for displaying
 * @property string $password_hash Hashed password
 * @property string $is_super Whether it is a super admin
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 *
 *
 */
class Admin extends Model implements Authenticatable
{

    /**
     * @var string Name of the table, without prefix
     */
    public $table = 'admin';

    public function rules()
    {
        return [
            'username' => ['required', 'string', 'max:50'],
            'nickname' => ['required', 'string', 'max:100'],
            'password_hash' => ['required', 'string', 'max:255'],
            'is_super' => ['required', 'string'],
            'created_at' => ['date'],
            'updated_at' => ['date'],
        ];
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
     * @return mixed
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
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
    }
}