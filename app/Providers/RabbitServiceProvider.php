<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Classes\RabbitMQ2;

class RabbitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * @return void
     *
     */
    public function register()
    {
        $this->app->singleton(RabbitMQ2::class, function ($app) {
           return new RabbitMQ2('192.168.65.2', 5672, 'guest', 'guest', 'react');
        });

    }
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
