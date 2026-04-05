<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('auth:clear-resets')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

Schedule::command('queue:prune-batches --hours=48')
    ->dailyAt('02:15')
    ->withoutOverlapping();

Schedule::command('queue:prune-failed --hours=168')
    ->dailyAt('02:30')
    ->withoutOverlapping();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
