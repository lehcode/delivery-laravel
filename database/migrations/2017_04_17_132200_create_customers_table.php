<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{

	protected $name = 'customers';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->name, function (Blueprint $table) {

			$table->uuid('id')->primary();
			$table->foreign('id')->references('id')->on('users')
				->onDelete('restrict')->onUpdate('restrict');

			$table->string('name');
			$table->text('notes')->nullable();
			
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
