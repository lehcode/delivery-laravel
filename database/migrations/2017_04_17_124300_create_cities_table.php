<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesTable extends Migration
{
	protected $name = 'cities';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->name, function (Blueprint $table) {
			$table->increments('id')->unsigned();
			$table->string('name', 128);
			$table->boolean('active')->default(true);

			$table->unsignedInteger('country_id');
			$table->foreign('country_id')->references('id')->on('countries')
				->onUpdate('cascade');
			
			$table->index(['name', 'country_id']);
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
