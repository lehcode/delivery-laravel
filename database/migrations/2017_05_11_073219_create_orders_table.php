<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
	protected $name = 'orders';
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

			$table->dateTime('departure_date');
			$table->dateTime('expected_delivery_date');

			$table->string('recipient_name');

			$table->uuid('customer_id');
			$table->foreign('customer_id')->references('user_id')->on('customers')
				->onUpdate('restrict')->onDelete('restrict');

			$table->uuid('shipment_id');
			$table->foreign('shipment_id')->references('id')->on('shipments')
				->onUpdate('restrict')->onDelete('restrict');

			$table->uuid('route_id');
			$table->foreign('route_id')->references('id')->on('routes')
				->onUpdate('restrict')->onDelete('restrict');

			$table->uuid('trip_id');
			$table->foreign('trip_id')->references('id')->on('trips')
				->onUpdate('restrict')->onDelete('restrict');

			$table->timestamps();
			$table->softDeletes();
		});

		DB::statement("ALTER TABLE {$this->name} ADD COLUMN startPoint POINT");
		DB::statement("ALTER TABLE {$this->name} ADD COLUMN endPoint POINT");
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
