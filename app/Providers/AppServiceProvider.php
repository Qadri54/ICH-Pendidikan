<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
    }

    public function boot(): void
    {
        Carbon::setLocale('id');

        // <x-app-layout> → resources/views/layouts/main.blade.php
        Blade::component('layouts.main', 'app-layout');
    }
}
