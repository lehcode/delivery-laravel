<?php

use App\Models\User\Carrier;
use App\Models\City;

/**
 * Created by Antony Repin
 * Date: 26.06.2017
 * Time: 12:41
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(Carrier::class, function (Faker\Generator $faker) {

	$cities = City::all();

	return [
		'current_city' => $cities->random()->id,
		'default_address' => $faker->streetAddress,
		'notes' => $faker->text(128),
	];

});
