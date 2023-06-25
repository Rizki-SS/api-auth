<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laminas\Diactoros\ResponseFactory;

class ApiResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $res = app(ResponseFactory::class);

        $res::macro("success", function () use ($res) {
            
        });
    }
}
