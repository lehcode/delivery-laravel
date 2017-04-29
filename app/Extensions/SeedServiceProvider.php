<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 14:54
 */

namespace App\Extensions;


/**
 * Class SeedServiceProvider
 * @package App\Extensions
 */
class SeedServiceProvider extends \Illuminate\Database\SeedServiceProvider {
    /**
     * Register the seed console command.
     *
     * @return void
     */
    protected function registerSeedCommand()
    {
        $this->app->singleton('command.seed', function ($app) {
            return new SeedCommand($app['db']);
        });
    }
}
