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

			$table->uuid('id')->primary();
			$table->foreign('id')->references('id')->on('users')
				->onUpdate('CASCADE')->onDelete('CASCADE');
			
			$table->enum('type', ['ios', 'android', 'pc'])->index();
			$table->string('device_id', 512)->index();
			$table->string('reg_id', 512)->index();
			
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
		DB::statement("SET FOREIGN_KEY_CHECKS=0");
		Schema::dropIfExists('user_devices');
		DB::statement("SET FOREIGN_KEY_CHECKS=1");
	}
}
