<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{

	/**
	 * @var string
	 */
	var $name = 'trips';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->name, function (Blueprint $table) {

			$table->uuid('id');
			$table->primary('id');
			$table->timestamp('time_length');

			$table->integer('payment_type_id')->unsigned()->index();
			$table->foreign('payment_type_id')->references('id')->on('payment_types')
				->onUpdate('cascade');

			$table->uuid('driver_id');
			$table->foreign('driver_id')->references('id')->on('users')
				->onUpdate('cascade');

			$table->timestamps();
			$table->softDeletes();

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
//        Schema::table($this->name, function(Blueprint $table){
//            $table->dropForeign(['payment_type_id']);
//            $table->dropForeign(['driver_id']);
//        });
		Schema::drop($this->name);
		DB::statement("SET FOREIGN_KEY_CHECKS=1");
	}
}
