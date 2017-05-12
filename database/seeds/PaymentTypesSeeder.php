<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		PaymentType::create(['name'=>'card']);
		PaymentType::create(['name'=>'cash']);
	}
}
