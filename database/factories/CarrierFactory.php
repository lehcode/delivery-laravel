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

	$data = [
		'is_online' => false,
		'current_city' => null,
		'default_address' => null,
		'notes' => null,
	];

//	if (env('APP_ENV') == 'local' || env('APP_ENV') === 'testing') {
//		$data = [
//			'is_online' => rand(1, 4) === 4 ? false : true,
//			'current_city' => $cities->random()->id,
//			'default_address' => $faker->streetAddress,
//			'notes' => $faker->text(128),
//		];
//	};
	
	$data = array_merge($data,
		[
			'birthday' => null,
			'nationality' => null,
			'id_number' => null,
		]);

	return $data;
});
