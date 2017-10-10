<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Payment;

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

			$statuses = [
				Payment::STATUS_UNPAID,
				Payment::STATUS_PROCESSING,
				Payment::STATUS_PAID
			];

			$table->uuid('id');
			$table->primary('id');

			$table->dateTime('departure_date')->nullable()->index();
			$table->dateTime('expected_delivery_date')->index();
			//$table->enum('status', $statuses)->index();
			//$table->float('price', 8, 2);

			$table->uuid('recipient_id')->index();
			$table->foreign('recipient_id')->references('id')->on('recipients')
				->onUpdate('restrict')->onDelete('restrict');

			$table->uuid('customer_id')->index();
			$table->foreign('customer_id')->references('id')->on('customers')
				->onUpdate('restrict')->onDelete('restrict');

			$table->uuid('shipment_id')->index()->unique();
			$table->foreign('shipment_id')->references('id')->on('shipments')
				->onUpdate('restrict')->onDelete('restrict');
			
			$table->uuid('trip_id')->nullable();
			$table->foreign('trip_id')->references('id')->on('trips')
				->onUpdate('restrict')->onDelete('restrict');

			$table->uuid('payment_id')->nullable();
			$table->foreign('payment_id')->references('id')->on('payments')
				->onUpdate('restrict')->onDelete('restrict');

			$table->timestamps();
			$table->softDeletes();

		});

		DB::statement("ALTER TABLE {$this->name} ADD COLUMN geo_start POINT");
		DB::statement("ALTER TABLE {$this->name} ADD COLUMN geo_end POINT");
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
