<?php

/**
 * Created by Antony Repin
 * Date: 30.05.2017
 * Time: 22:18
 */

use Faker\Generator as Faker;
use App\Models\Shipment;

$factory->define(Shipment::class, function (Faker $faker) {
	
	$sizes = [10, 20, 25, 50, 60, 100, 120];
	
	return [
		'name' => $faker->word,
		'height' => $faker->randomElement($sizes),
		'width' => $faker->randomElement($sizes),
		'length' => $faker->randomElement($sizes),
		'weight' => $faker->randomElement([0.1, 0.25, 0.5, 0.75, 1, 2, 5, 10, 12, 15, 20, 25, 50]),
		'category_id' => null,
	];
});
