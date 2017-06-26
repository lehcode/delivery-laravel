<?php

/**
 * Created by Antony Repin
 * Date: 29.05.2017
 * Time: 4:05
 */

use App\Models\Order;
use Jenssegers\Date\Date;
use Webpatser\Uuid\Uuid;

$factory->define(Order::class, function (Faker\Generator $faker) {

	$dptrDate = Date::now()->addDays(rand(3, 10));
	$dlvrDate = Date::createFromFormat(Date::ISO8601, $dptrDate->format(Date::ISO8601));
	$dlvrDate->addDays(rand(1, 3));

	return [
		'id' => Uuid::generate(4),
		'departure_date' => $dptrDate,
		'expected_delivery_date' => $dlvrDate,
		'geo_start' => null,
		'geo_end' => null,
		'price' => null,
	];
});
