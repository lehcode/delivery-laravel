<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateShipmentCategoriesTable
 */
class CreateShipmentCategoriesTable extends Migration
{
	/**
	 * @var string
	 */
	protected $name = 'shipment_categories';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->name, function (Blueprint $table) {

			$table->increments('id');
			
			$table->string('name', 64);
			$table->string('description');

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
		Schema::dropIfExists($this->name);
		DB::statement("SET FOREIGN_KEY_CHECKS=1");
	}
}
