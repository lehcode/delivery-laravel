<?php

/**
 * Created by Antony Repin
 * Date: 29.05.2017
 * Time: 3:14
 */

use App\Models\Route;
use App\Models\City;
use Jenssegers\Date\Date;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;

$factory->define(Route::class, function (Faker $faker) {

	$cities = City::all();
	$startCity = $cities->random();
	$destinationCity = $cities->random();
	//$now = Date::now();
	$type = $faker->randomElement(['order', 'trip']);

	return [
		'id' => Uuid::generate(4),
		'from_city_id' => $startCity->id,
		'to_city_id' => $destinationCity->id,
		'type' => $type,
		//'departure_date' => $now->addDays(rand(1, 5)),
	];

});
