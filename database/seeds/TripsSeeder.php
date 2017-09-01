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

		User::all()
			->filter(function ($u) {
				if ($u->roles()->first()->name == User::ROLE_CARRIER) {
					return $u;
				}
			})
			->each(function ($u) use ($faker) {
				$carrier = $u->carrier()->with('trips')->first();
				for ($i = 0; $i < rand(3, 15); $i++) {

					$tripEntity = factory(Trip::class)->make([ 'carrier_id' => $carrier->id ]);

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
