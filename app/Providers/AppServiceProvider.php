<?php

namespace App\Providers;

use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Payment\PaymentRepository;
use App\Repositories\Payment\PaymentRepositoryInterface;
use App\Repositories\Trip\TripRepository;
use App\Repositories\Trip\TripRepositoryInterface;
use App\Repositories\UserSignupRequest\UserSignupRepository;
use App\Repositories\UserSignupRequest\UserSignupRepositoryInterface;
use App\Services\Maintenance\MaintenanceService;
use App\Services\Maintenance\MaintenanceServiceInterface;
use App\Services\Order\OrderService;
use App\Services\Order\OrderServiceInterface;
use App\Services\Payment\PaymentService;
use App\Services\Payment\PaymentServiceInterface;
use App\Services\Responder\ResponderService;
use App\Services\Responder\ResponderServiceInterface;
use App\Services\Settings\SettingsService;
use App\Services\Settings\SettingsServiceInterface;
use App\Services\SignUp\SignUpService;
use App\Services\SignUp\SignUpServiceInterface;
use App\Services\Trip\TripService;
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
        app()->singleton(TripRepositoryInterface::class, TripRepository::class);
        app()->singleton(ShipmentRepositoryInterface::class, ShipmentRepository::class);
        app()->singleton(OrderRepositoryInterface::class, OrderRepository::class);
        app()->singleton(PaymentRepositoryInterface::class, PaymentRepository::class);
        /*
         * Services
         */
        app()->singleton(ResponderServiceInterface::class, ResponderService::class);
        app()->singleton(SignUpServiceInterface::class, SignUpService::class);
        app()->singleton(UserServiceInterface::class, UserService::class);
        app()->singleton(MaintenanceServiceInterface::class, MaintenanceService::class);
        app()->singleton(SettingsServiceInterface::class, SettingsService::class);
        app()->singleton(TripServiceInterface::class, TripService::class);
        app()->singleton(ShipmentServiceInterface::class, ShipmentService::class);
        app()->singleton(CustomerServiceInterface::class, CustomerService::class);
        app()->singleton(OrderServiceInterface::class, OrderService::class);
        app()->singleton(PaymentServiceInterface::class, PaymentService::class);

        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

    }
}
