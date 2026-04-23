<?php

use App\Models\ActivityLog;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('activity-logs:cleanup {--weeks= : Number of weeks to keep}', function () {
    $weeks = (int) ($this->option('weeks') ?: env('ACTIVITY_LOG_RETENTION_WEEKS', 2));
    $weeks = max(1, $weeks);

    $deleted = ActivityLog::query()
        ->where('created_at', '<', now()->subWeeks($weeks))
        ->delete();

    $this->info("Deleted {$deleted} activity log rows older than {$weeks} week(s).");
})->purpose('Delete old activity logs to reduce table size');

Schedule::command('activity-logs:cleanup')
    ->weekly()
    ->at('03:00')
    ->withoutOverlapping();
