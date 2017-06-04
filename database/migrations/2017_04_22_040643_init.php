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
            $table->string('name', 50)->comment('Real name of the user');
            $table->string('mobile', 11)->comment('Mobile phone number');
            $table->string('address', 500)->comment('The address to send the gas card to');
            $table->string('password_hash', 100)->comment('Hash of the password, not storing plain password in db');
            $table->timestamps();
            $table->unsignedTinyInteger('deleted')->default(0)->comment('Whether the user is deleted');
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
    }
}
