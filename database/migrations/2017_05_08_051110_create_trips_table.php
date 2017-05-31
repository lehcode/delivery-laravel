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
			//$table->integer('time_length');

			$table->integer('payment_type_id')->unsigned()->index();
			$table->foreign('payment_type_id')->references('id')->on('payment_types')
				->onUpdate('cascade')->onDelete('restrict');

			$table->uuid('carrier_id');
			$table->foreign('carrier_id')->references('id')->on('carriers')
				->onUpdate('restrict')->onDelete('restrict');

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
		Schema::drop($this->name);
		DB::statement("SET FOREIGN_KEY_CHECKS=1");
	}
}
