<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('setting');
        Schema::create('setting', function (Blueprint $table) {
            $table->string('code', 255)->charset('ascii');
            $table->enum('json', ['no', 'yes']);
            $table->text('setting');
            $table->unique('code');
        });

        DB::table('setting')->insert([
            'code' => 'sms_signature',
            'json' => 'no',
            'setting' => '洛洛交友'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting');
    }
}
