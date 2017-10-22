<?php
/**
 * Created by Antony Repin
 * Date: 28.05.2017
 * Time: 14:42
 */

use App\Models\Trip;
use Jenssegers\Date\Date;

$factory->define(Trip::class, function (Faker\Generator $faker) {
	
	$departureDate = Date::now();

	return [
		'carrier_id' => null,
		'from_city_id' => null,
		'to_city_id' => null,
		'departure_date' => $departureDate->addDays(rand(1, 30)),
		'approx_time' => $faker->randomNumber(rand(2,4)),
		'geo_start' => null,
		'geo_end' => null,
	];
});
