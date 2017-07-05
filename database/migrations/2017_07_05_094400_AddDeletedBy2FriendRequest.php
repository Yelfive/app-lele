<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedBy2FriendRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('friend_request', function (Blueprint $table) {
            $table->unsignedTinyInteger('deleted_by')->default(0)->comment('Deleted by, 1=sender,2=friend,3=1|2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('friend_request', function (Blueprint $table) {
            $table->dropColumn('deleted_by');
        });
    }
}
