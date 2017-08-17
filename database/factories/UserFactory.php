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
use Webpatser\Uuid\Uuid;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker\Generator $faker) {

	$userData = [
		'id' => Uuid::generate(4)->string,
		'email' => null,
		'username' => null,
		'password' => null,
		'phone' => null,
		'last_login' => null,
		'is_enabled' => false,
	];

	return $userData;
});
