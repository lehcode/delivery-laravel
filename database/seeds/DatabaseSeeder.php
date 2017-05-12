<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call(CitiesSeeder::class);
		$this->call(PaymentTypesSeeder::class);
		$this->call(SetupAclTablesSeed::class);
		$this->call(SettingsSeeder::class);
		$this->call(LanguagesSeeder::class);
		$this->call(RecipientSeeder::class);
	}
}
