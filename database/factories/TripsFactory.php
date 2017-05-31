<?php
/**
 * Created by Antony Repin
 * Date: 28.05.2017
 * Time: 14:42
 */

use App\Models\Trip;
use Faker\Generator as Faker;
use App\Models\PaymentType;
use App\Models\City;
use Jenssegers\Date\Date;
use Webpatser\Uuid\Uuid;

$factory->define(Trip::class, function (Faker $faker) {
	
	$paymentTypes = PaymentType::all();
	$cities = City::all();
	$departureDate = Date::now();

	return [
		'carrier_id' => Uuid::generate(4),
		'payment_type_id' => $paymentTypes->random()->id,
		'from_city_id' => $cities->random(),
		'to_city_id' => $cities->random(),
		'departure_date' => $departureDate->addDays(rand(1, 5)),
	];
});
