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

		$carriers = User::all()->filter(function ($u) {

			if ($u->roles()->first()->name == User::ROLE_CARRIER) {
				return $u;
			}
		});

		$carriers->each(function ($c) use ($faker) {

			$user = $c->carrier()->first();

			for ($i = 0; $i < rand(3, 9); $i++) {
				try {
					$user->trips()->save(factory(Trip::class)->make([
						'carrier_id' => $c->id,
					]));
				} catch (\Exception $e) {
					throw $e;
				}

			}

		});
	}
}
