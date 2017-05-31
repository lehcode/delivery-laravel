<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateRoutesTable
 */
class CreateRoutesTable extends Migration
{
	/**
	 * @var string
	 */
	var $name = 'routes';

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

			$table->unsignedInteger('from_city_id')->index();
			$table->foreign('from_city_id')->references('id')->on('cities');

			$table->unsignedInteger('to_city_id')->index();
			$table->foreign('to_city_id')->references('id')->on('cities');

			$table->enum('type', ['order', 'trip']);
			//$table->dateTime('departure_date');
			$table->softDeletes();
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
		Schema::drop($this->name);
		DB::statement("SET FOREIGN_KEY_CHECKS=1");
	}
}
