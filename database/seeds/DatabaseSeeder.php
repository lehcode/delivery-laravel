<?php

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call(CountriesSeeder::class);
		$this->call(CitiesSeeder::class);
		$this->call(PaymentTypesSeeder::class);
		$this->call(SetupAclTablesSeed::class);
		$this->call(SettingsSeeder::class);
		$this->call(LanguagesSeeder::class);
		$this->call(TripsSeeder::class);
		$this->call(ShipmentCategoriesSeeder::class);
		$this->call(ShipmentSizesSeeder::class);
		$this->call(OrdersSeeder::class);
	}
}
