<?php

/**
 * Created by Antony Repin
 * Date: 29.05.2017
 * Time: 3:14
 */

use App\Models\Route;
use App\Models\City;
use Webpatser\Uuid\Uuid;

$factory->define(Route::class, function (Faker\Generator $faker) {

	$cities = City::all();
	$startCity = $cities->random();
	$destinationCity = $cities->random();
	$type = $faker->randomElement(['order', 'trip']);

	return [
		'id' => Uuid::generate(4),
		'from_city_id' => $startCity->id,
		'to_city_id' => $destinationCity->id,
		'type' => $type,
	];

});
