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
use App\Models\User;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker $faker) {
	
	$userData = [
		'id' => Uuid::generate(4),
		'email' => $faker->safeEmail,
		'name' => $faker->name,
		'password' => 'Qrab17',
		'phone' => '+37529' . mt_rand(1111111, 9999999),
		'last_login' => Date::now()->subDays(rand(0, 4)),
		'is_enabled' => true,
	];

	return $userData;
});
