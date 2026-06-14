<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\ReservationServiceInterface;
use App\Services\ReservationService;
use App\Services\Contracts\ServiceServiceInterface;
use App\Services\ServiceService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ReservationServiceInterface::class, ReservationService::class);
        $this->app->bind(ServiceServiceInterface::class, ServiceService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
