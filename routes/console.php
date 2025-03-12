<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:fetch-new-york-times-articles')
    ->hourly();
Schedule::command('app:fetch-guardian-articles')
    ->hourly();
Schedule::command('app:fetch-news-articles')
    ->hourly();
