<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Trip;
use Faker\Factory as Faker;
use App\Models\City;

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
		$cities = City::all();

		User\Carrier::all()
			->each(function ($carrier) use ($faker, $cities) {

				for ($i = 0; $i < rand(3, 15); $i++) {

					$fromCity = $cities->random();
					$toCity = $cities->random();

					$tripEntity = factory(Trip::class)->make([
						'carrier_id' => $carrier->id,
						'from_city_id' => $fromCity,
						'to_city_id' => $toCity,
						'geo_start' => OrdersSeeder::makePoint($this->getGeoData($fromCity)),
						'geo_end' => OrdersSeeder::makePoint($this->getGeoData($fromCity)),
					]);

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

	/**
	 * @param City $city
	 *
	 * @return mixed
	 */
	private function getGeoData($city)
	{

		$srcCities = collect(CitiesSeeder::CITIES);
		$c = $srcCities->where('name', '=', $city->name)->first();
		$geo = $c['geo'];

		return $geo;
	}
}
