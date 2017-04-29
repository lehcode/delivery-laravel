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

use Jenssegers\Date;
use App\Models\User;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker $faker) {

    static $password = 'Qrab17';

    $userData = [
        'password' => bcrypt($password),
        'remember_token' => str_random(10),
        'phone' => $faker->phoneNumber,
        'last_login' => Date::now()->subDays(rand(0, 4)),
    ];

    var_dump($userData);

    return $userData;
});
