<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
	protected $name = 'payments';
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

			$table->uuid('order_id')->index();
			$table->foreign('order_id')->references('id')->on('orders');

			$table->float('amount');

			$table->integer('type_id')->unsigned()->index();
			$table->foreign('type_id')->references('id')->on('payment_types');

			$table->boolean('success')->default(0);

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
