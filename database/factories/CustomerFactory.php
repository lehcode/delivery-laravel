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

	$cardType = $faker->randomElement(['Visa', 'MasterCard']);
	$cities = City::all();

	return [
		'id' => null,
		'notes' => $faker->text(128),
		'current_city' => $cities->random()->id,
		'card_number' => $faker->creditCardNumber($cardType),
		'card_type' => $cardType,
		'card_name' => '',
		'card_expiry' => $faker->creditCardExpirationDate,
		'card_cvc' => $faker->randomNumber(3),
	];

});
