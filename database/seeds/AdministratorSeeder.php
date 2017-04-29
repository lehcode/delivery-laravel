<?php

/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 16:07
 */

use Illuminate\Database\Seeder;
use App\Repositories\User\UserRepositoryInterface;
use App\Models\User;

/**
 * Class AdministratorSeeder
 */
class AdministratorSeeder extends Seeder
{
    /**
     *
     */
    public function run()
    {

        $faker = Faker\Factory::create('en_GB');

        app()->make(UserRepositoryInterface::class)->create([
            'name' => $faker->name,
            'email' => 'admin@madina.com',
            'phone' => '+375296000000',
            'password' => 'madina',
            //'role' => User::ROLE_ADMIN,
            'is_enabled' => true
        ]);
    }
}
