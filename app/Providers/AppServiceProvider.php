<?php

namespace App\Providers;

use App\Repositories\UserSignupRequest\UserSignupRepository;
use App\Repositories\UserSignupRequest\UserSignupRepositoryInterface;
use App\Services\Maintenance\MaintenanceService;
use App\Services\Maintenance\MaintenanceServiceInterface;
use App\Services\Responder\ResponderService;
use App\Services\Responder\ResponderServiceInterface;
use App\Services\Settings\SettingsService;
use App\Services\Settings\SettingsServiceInterface;
use App\Services\SignUp\SignUpService;
use App\Services\SignUp\SignUpServiceInterface;
use App\Services\Trip\TripServiceInterface;
use App\Services\UserService\UserService;
use App\Services\UserService\UserServiceInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Laravel\Dusk\DuskServiceProvider;
use App\Repositories\Setting\SettingRepositoryInterface;
use App\Repositories\Setting\SettingRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * Repositories
         */
        app()->singleton(UserRepositoryInterface::class, UserRepository::class);
        app()->singleton(UserSignupRepositoryInterface::class, UserSignupRepository::class);
        app()->singleton(SettingRepositoryInterface::class, SettingRepository::class);
        /*
         * Services
         */
        app()->singleton(ResponderServiceInterface::class, ResponderService::class);
        app()->singleton(SignUpServiceInterface::class, SignUpService::class);
        app()->singleton(UserServiceInterface::class, UserService::class);
        app()->singleton(MaintenanceServiceInterface::class, MaintenanceService::class);
        app()->singleton(SettingsServiceInterface::class, SettingsService::class);
        app()->singleton(TripServiceInterface::class, TripService::class);

        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

    }
}
