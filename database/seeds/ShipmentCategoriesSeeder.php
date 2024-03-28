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
		$this->command->info('Generating shipment categories');
		$faker = Faker::create('en_GB');

		$data = [
			['name' => 'Documents', 'description' => $faker->sentence()],
			['name' => 'Box', 'description' => $faker->sentence()],
			['name' => 'Electronics', 'description' => $faker->sentence()],
			['name' => 'Other', 'description' => 'None of the existing categories']
		];

		foreach ($data as $item) {
			ShipmentCategory::create($item);
		}

	}
}
