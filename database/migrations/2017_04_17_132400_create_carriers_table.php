<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarriersTable extends Migration
{

	protected $name = 'carriers';

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

			$table->string('name');
			$table->string('default_address')->nullable();

			$table->unsignedInteger('current_city')->nullable()->index();
			$table->foreign('current_city')->references('id')->on('cities')
				->onDelete('restrict')->onUpdate('restrict');

			$table->double('rating', 2, 2)->unsigned()->nullable();
			$table->text('notes')->nullable();
			$table->boolean('is_online')->default(false);

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
