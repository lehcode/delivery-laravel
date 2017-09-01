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
			$table->dateTime('departure_date');
			$table->unsignedSmallInteger('approx_time');

			$table->uuid('carrier_id');
			$table->foreign('carrier_id')->references('id')->on('carriers')
				->onUpdate('restrict')->onDelete('restrict');

			$table->unsignedInteger('from_city_id');
			$table->foreign('from_city_id')->references('id')->on('cities')
				->onUpdate('restrict')->onDelete('restrict');

			$table->unsignedInteger('to_city_id');
			$table->foreign('to_city_id')->references('id')->on('cities')
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
