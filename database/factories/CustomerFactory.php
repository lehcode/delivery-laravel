<?php

use App\Models\User\Customer;
use App\Models\City;

/**
 * Created by Antony Repin
 * Date: 02.06.2017
 * Time: 16:46
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(Customer::class, function (Faker\Generator $faker) {

	return [
		'id' => null,
		'notes' => null,
		'current_city' => null,
		'card_number' => null,
		'card_type' => null,
		'card_name' => null,
		'card_expiry' => null,
		'card_cvc' => null,
	];

});
