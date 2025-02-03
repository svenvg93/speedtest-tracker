<?php

use App\Actions\CheckForScheduledSpeedtests;
use App\Actions\Notifications\SendDailyAverageNotifications;
use App\Actions\Notifications\SendWeeklyAverageNotifications;
use Illuminate\Support\Facades\Schedule;

/**
 * Checks if Result model records should be pruned.
 */
Schedule::command('model:prune')
    ->daily()
    ->when(function () {
        return config('speedtest.prune_speedtests_older_than') > 0;
    });

/**
 * Checked for new versions weekly on Thursday because
 * I usually do releases on Thursday or Friday.
 */
Schedule::command('app:version')
    ->weeklyOn(5);

/**
 * Nightly maintenance
 */
Schedule::daily()
    ->group(function () {
        Schedule::command('queue:prune-batches --hours=48');
        Schedule::command('queue:prune-failed --hours=48');
    });

/**
 * Check for scheduled speedtests.
 */
Schedule::everyMinute()
    ->group(function () {
        Schedule::call(fn () => CheckForScheduledSpeedtests::run());
    });

Schedule::daily()
    ->group(function () {
        Schedule::call(fn () => SendDailyAverageNotifications::run());
    });

Schedule::weeklyOn(1, '00:00')
    ->group(function () {
        Schedule::call(fn () => SendWeeklyAverageNotifications::run());
    });
