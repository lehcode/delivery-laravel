<?php

use Illuminate\Database\Seeder;
use App\Models\ShipmentSize;
use Faker\Factory as Faker;
use App\Exceptions\MultipleExceptions;

/**
 * Class ShipmentSizesSeeder
 */
class ShipmentSizesSeeder extends Seeder
{
	/**
	 * @throws MultipleExceptions
	 */
	public function run()
	{
		$this->command->info('Generating shipment sizes');
		$faker = Faker::create('en_GB');

		$data = [
			[
				'name' => 'S',
				'description' => 'Small size package',
				'length' => $faker->numberBetween(10, 20),
				'width' => $faker->numberBetween(10, 20),
				'height' => $faker->numberBetween(10, 20),
				'weight' => $faker->randomFloat(3, 1, 64),
			],
			[
				'name' => 'M',
				'description' => 'Medium size package',
				'length' => $faker->numberBetween(20, 50),
				'width' => $faker->numberBetween(20, 50),
				'height' => $faker->numberBetween(20, 50),
				'weight' => $faker->randomFloat(3, 64, 256),
			],
			[
				'name' => 'L',
				'description' => 'Large size package',
				'length' => $faker->numberBetween(50, 100),
				'width' => $faker->numberBetween(50, 100),
				'height' => $faker->numberBetween(50, 100),
				'weight' => $faker->randomFloat(3, 256, 1500),
			]
		];

		foreach ($data as $item) {
			$created = ShipmentSize::create($item);

			if (!$created->isValid()) {
				$errors = $created->getErrors()->messages();
				foreach ($errors as $req => $error) {
					foreach ($error as $text) {
						throw new \Exception($text, 1);
					}
				}
			}

		}


	}
}
