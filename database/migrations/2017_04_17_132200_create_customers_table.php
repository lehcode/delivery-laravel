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

			$table->uuid('id');
			$table->primary('id');
			$table->foreign('id')->references('id')->on('users')
				->onDelete('restrict')->onUpdate('restrict');

			$table->unsignedInteger('current_city')->index();
			$table->foreign('current_city')->references('id')->on('cities')
				->onDelete('restrict')->onUpdate('restrict');

			$table->string('name');
			$table->text('notes')->nullable();

			$table->string('card_name')->nullable();
			$table->enum('card_type', ['Visa', 'MasterCard'])->nullable();
			$table->string('card_number', 16)->nullable();
			$table->date('card_expiry')->nullable();
			$table->string('card_cvc', 3)->nullable();

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
