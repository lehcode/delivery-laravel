<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shipments', function (Blueprint $table) {

			$table->uuid('id');
			$table->primary('id');

			$table->string('name', 64);
			$table->integer('height')->unsigned();
			$table->integer('length')->unsigned();
			$table->integer('width')->unsigned();
			$table->float('weight')->unsigned();
			$table->integer('category_id')->unsigned();
			$table->foreign('category_id')
				->references('id')->on('shipment_categories');

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
		Schema::drop('shipments');
		DB::statement("SET FOREIGN_KEY_CHECKS=1");
	}
}
