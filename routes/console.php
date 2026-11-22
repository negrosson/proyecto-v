<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mantenimiento automático para servidor cPanel (Tecoinver)
Schedule::command('media:prune-orphans')
    ->dailyAt('03:30')
    ->withoutOverlapping();

Schedule::command('security:prune-audit-logs --days=180')
    ->weeklyOn(0, '04:00')
    ->withoutOverlapping();
