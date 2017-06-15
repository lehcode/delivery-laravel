<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\ShipmentCategory;

class ShipmentCategoriesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->command->info('Creating shipment categories');
		$faker = Faker::create('en_GB');

		ShipmentCategory::create(['name' => 'S', 'description' => 'Small size package']);
		ShipmentCategory::create(['name' => 'M', 'description' => 'Medium size package']);
		ShipmentCategory::create(['name' => 'L', 'description' => 'Large size package']);
		ShipmentCategory::create(['name' => 'Documents', 'description' => $faker->sentence()]);
		ShipmentCategory::create(['name' => 'Box', 'description' => $faker->sentence()]);
		ShipmentCategory::create(['name' => 'Electronics', 'description' => $faker->sentence()]);
		ShipmentCategory::create(['name' => 'Other', 'description' => 'None of the existing categories']);
	}
}
