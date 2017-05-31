<?php
/**
 * Created by Antony Repin
 * Date: 28.05.2017
 * Time: 14:42
 */

use App\Models\Trip;
use Faker\Generator as Faker;
use App\Models\PaymentType;

$factory->define(Trip::class, function (Faker $faker) {
	
	$paymentTypes = PaymentType::all();
	
	return [
		'carrier_id' => null,
		'payment_type_id' => $paymentTypes->random()->id
	];
});
