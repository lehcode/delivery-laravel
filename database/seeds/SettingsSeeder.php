<?php

/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 16:11
 */

use Illuminate\Database\Seeder;

/**
 * Class SettingsSeeder
 */
class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Settings::create([
            'key' => \App\Models\Settings::KEY_MAINTENANCE_MODE,
            'value' => false,
            'name' => 'Maintenance mode',
            'description' => 'Turn on or off whole system',
            'is_public' => true
        ]);
    }
}
