<?php

/**
 * Created by Antony Repin
 * Date: 29.05.2017
 * Time: 4:05
 */

use App\Models\Order;
use Jenssegers\Date\Date;

$factory->define(Order::class, function (Faker\Factory $faker) {

	$now = Date::now();

	return [
		'departure_date' => $now->addDays(rand(3, 10)),
	];
});
