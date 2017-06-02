<?php

use Jenssegers\Date\Date;
use App\Models\Recipient;
use Webpatser\Uuid\Uuid;

/**
 * Created by Antony Repin
 * Date: 02.06.2017
 * Time: 16:46
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(Recipient::class, function (Faker\Generator $faker) {

	return [
		'id' => Uuid::generate(4),
		'name' => $faker->name,
		'phone' => '+37529' . mt_rand(1111111, 9999999),
		'notes' => ''
	];

});
