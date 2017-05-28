<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipientsTable extends Migration
{
	var $name = 'recipients';

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

			$table->string('name');
			$table->string('phone');
			$table->text('notes');

			$table->uuid('sender_id');
			$table->foreign('sender_id')->references('user_id')->on('customers')
				->onUpdate('cascade');

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
		Schema::dropIfExists($this->name);
		DB::statement("SET FOREIGN_KEY_CHECKS=1");
	}
}
