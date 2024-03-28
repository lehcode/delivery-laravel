<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Payment;


/**
 * Class CreatePaymentsTable
 */
class CreatePaymentsTable extends Migration
{
	/**
	 * @var string
	 */
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
			$table->float('amount');

			$table->integer('payment_type_id')->unsigned()->index();
			$table->foreign('payment_type_id')->references('id')->on('payment_types')
				->onDelete('restrict')
				->onUpdate('restrict');

			$table->enum('status', [
				Payment::STATUS_UNPAID,
				Payment::STATUS_PROCESSING,
				Payment::STATUS_PAID
			])->default(Payment::STATUS_UNPAID);

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
