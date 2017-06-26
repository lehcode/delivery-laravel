<?php
/**
 * Created by Antony Repin
 * Date: 28.05.2017
 * Time: 14:42
 */

use App\Models\Trip;
use App\Models\PaymentType;
use App\Models\City;
use Jenssegers\Date\Date;
use Webpatser\Uuid\Uuid;

$factory->define(Trip::class, function (Faker\Generator $faker) {
	
	$paymentTypes = PaymentType::all();
	$cities = City::all();
	$departureDate = Date::now();

	return [
		'carrier_id' => null,
		'payment_type_id' => $paymentTypes->random()->id,
		'from_city_id' => $cities->random(),
		'to_city_id' => $cities->random(),
		'departure_date' => $departureDate->addDays(rand(1, 30)),
		'approx_time' => $faker->randomNumber(rand(2,4)),
	];
});
