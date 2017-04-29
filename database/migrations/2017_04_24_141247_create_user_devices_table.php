<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('user_id');
            $table->enum('type', ['ios', 'android', 'pc'])->index('idx_type');
            $table->string('device_id', 512)->index();
            $table->string('reg_id', 512)->index();
            $table->timestamps();
            $table->foreign('user_id', 'fk_user_devices_users1')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_devices', function (Blueprint $table) {
            $table->dropForeign('fk_user_devices_users1');
        });

        Schema::dropIfExists('user_devices');
    }
}
