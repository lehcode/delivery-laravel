<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use Jenssegers\Date\Date;
use App\Models\Recipient;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Recipient::class, function (Faker $faker) {

	return [
		'id' => Uuid::generate(4),
		'name' => $faker->name,
		'phone' => '+37529' . mt_rand(1111111, 9999999),
		'notes' => ''
	];

});
