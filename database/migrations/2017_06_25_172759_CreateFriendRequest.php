<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friend_request', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sender')->comment('ID of request sender');
            $table->unsignedInteger('friend_id')->comment('ID of user the sender want to add');
            $table->string('remark', 1000)->default('')->comment('');
            $table->unsignedInteger('from')->comment('The request come from, e.g. mobile, user search');
            $table->integer('distance')->comment('Distance between sender and friend_id, when sending request');
            $table->tinyInteger('status')->default(0)->comment('Status of the request, agree or decline');
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
        Schema::dropIfExists('friend_request');
    }
}
