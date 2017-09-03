<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Trip;
use Faker\Factory as Faker;

/**
 * Class TripsSeeder
 */
class TripsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->command->info('Creating Carrier\'s trips');

		$faker = Faker::create('en_GB');

		User\Carrier::all()
			->each(function ($carrier) use ($faker) {

				for ($i = 0; $i < rand(3, 15); $i++) {

					$tripEntity = factory(Trip::class)->make(['carrier_id' => $carrier->id]);

					if (!$tripEntity->isValid()) {
						$errors = $tripEntity->getErrors()->messages();
						foreach ($errors as $req => $error) {
							foreach ($error as $text) {
								throw new \Exception($text, 1);
							}
						}
					}

					$carrier->trips()->save($tripEntity);

				}

			});
	}
}
