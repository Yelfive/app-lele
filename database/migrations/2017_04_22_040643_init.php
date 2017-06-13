<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Init extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nickname', 50)->comment('User\\\'s nickname');
            $table->string('state_code', 10)->comment('State code, +86=china');
            $table->string('mobile', 11)->comment('Mobile phone number')->unique();
            $table->string('avatar')->default('')->comment('Avatar for the user');
            $table->string('account', 20)->comment('User\\\'s LeLe Number')->unique();
            $table->string('im_account')->default('')->comment('Login of the IM');
            $table->string('im_password')->default('')->comment('Password of the IM');
            $table->enum('sex', ['unknown', 'male', 'female'])->default('unknown')->comment('user gender');
            $table->string('city_name')->comment('Register location, city name');
            $table->string('city_code')->comment('Register location, city code');
            $table->unsignedTinyInteger('age')->default(0);
            $table->text('it_says')->comment('What he/she says');
            $table->string('address', 500)->default(0)->comment('The address');
            $table->string('password_hash', 100)->comment('Hash of the password, not storing plain password in db');
            $table->timestamps();
            $table->unsignedTinyInteger('deleted')->default(0)->comment('Whether the user is deleted');
        });

        Schema::create('user_friends', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('created_by')->comment('Record creator, indicates whose friend this is');
            $table->unsignedInteger('friend_id')->comment('User ID of the friend');
            $table->string('friend_nickname')->default('')->comment('Nickname of the friend');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user');
        Schema::drop('user_friends');
    }
}
