<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('installments:check-overdue')->daily();

// SPP — generate invoices every 1st of the month at midnight
Schedule::command('spp:generate-invoices')->monthlyOn(1, '00:00');

// SPP — mark overdue invoices every day
Schedule::command('spp:check-overdue')->daily();
