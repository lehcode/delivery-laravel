<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLanguageTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_language', function (Blueprint $table) {

			$table->uuid('id')->index();
			$table->foreign('id')->references('id')->on('users')
				->onUpdate('restrict');

			$table->unsignedInteger('language_id')->index();
			$table->primary(['id', 'language_id']);

			$table->foreign('language_id')->references('id')->on('languages')
				->onUpdate('CASCADE');
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
		Schema::dropIfExists('user_language');
		DB::statement("SET FOREIGN_KEY_CHECKS=1");
	}
}
