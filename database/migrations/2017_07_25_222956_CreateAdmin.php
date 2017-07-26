<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\{
    DB, Hash, Schema
};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 50)->comment('Username for login');
            $table->string('nickname', 100)->comment('Nickname of the admin for displaying');
            $table->string('password_hash', 255)->comment('Hashed password');
            $table->enum('is_super', ['no', 'yes'])->comment('Whether it is a super admin');
            $table->timestamps();
        });

        DB::table('admin')->insert([
            'id' => 1,
            'username' => 'admin',
            'nickname' => '超级管理员',
            'password_hash' => Hash::make('admin@lele'),
            'is_super' => 'yes',
            'created_at' => new Carbon(),
            'updated_at' => new Carbon(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin');
    }
}
