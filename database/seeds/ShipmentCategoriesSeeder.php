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
			['name' => 'Documents', 'description' => $faker->sentence(), 'multiplier' => $faker->randomFloat(2, 1, 6)],
			['name' => 'Box', 'description' => $faker->sentence(), 'multiplier' => $faker->randomFloat(2, 1, 6)],
			['name' => 'Electronics', 'description' => $faker->sentence(), 'multiplier' => $faker->randomFloat(2, 1, 6)],
			['name' => 'Other', 'description' => 'None of the existing categories', 'multiplier' => $faker->randomFloat(2, 2, 9)]
		];

		foreach ($data as $item) {
			ShipmentCategory::create($item);
		}

	}
}
