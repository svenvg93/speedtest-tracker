<?php

namespace App\Listeners;

use App\Events\SpeedtestBenchmarkFailed;
use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Influxdb\v2\WriteResult;
use App\Jobs\Notifications\Apprise\SendSpeedtestCompletedNotification as AppriseCompleted;
use App\Jobs\Notifications\Apprise\SendSpeedtestThresholdNotification as AppriseThresholds;
use App\Jobs\Notifications\Database\SendSpeedtestCompletedNotification as DatabaseCompleted;
use App\Jobs\Notifications\Database\SendSpeedtestThresholdNotification as DatabaseThresholds;
use App\Jobs\Notifications\Mail\SendSpeedtestCompletedNotification as MailCompleted;
use App\Jobs\Notifications\Mail\SendSpeedtestThresholdNotification as MailThresholds;
use App\Jobs\Notifications\Webhook\SendSpeedtestCompletedNotification as WebhookCompleted;
use App\Jobs\Notifications\Webhook\SendSpeedtestThresholdNotification as WebhookThresholds;
use App\Models\NotificationChannel;
use App\Settings\DataIntegrationSettings;
use Illuminate\Events\Dispatcher;

class SpeedtestEventSubscriber
{
    public function handleSpeedtestFailed(SpeedtestFailed $event): void {}

    public function handleSpeedtestCompleted(SpeedtestCompleted $event): void
    {
        $settings = app(DataIntegrationSettings::class);

        if ($settings->influxdb_v2_enabled) {
            WriteResult::dispatch($event->result);
        }

        $channels = NotificationChannel::query()
            ->where('enabled', true)
            ->where('on_speedtest_run', true)
            ->get()
            ->groupBy('type');

        if ($channels->has('Apprise')) {
            AppriseCompleted::dispatch($event->result);
        }

        if ($channels->has('Database')) {
            DatabaseCompleted::dispatch($event->result);
        }

        if ($channels->has('Webhook')) {
            WebhookCompleted::dispatch($event->result);
        }

        if ($channels->has('Mail')) {
            MailCompleted::dispatch($event->result);
        }
    }

    public function handleSpeedtestBenchmarkFailed(SpeedtestBenchmarkFailed $event): void
    {
        $channels = NotificationChannel::query()
            ->where('enabled', true)
            ->where('on_threshold_failure', true)
            ->get()
            ->groupBy('type');

        if ($channels->has('Apprise')) {
            AppriseThresholds::dispatch($event->result);
        }

        if ($channels->has('Database')) {
            DatabaseThresholds::dispatch($event->result);
        }

        if ($channels->has('Webhook')) {
            WebhookThresholds::dispatch($event->result);
        }

        if ($channels->has('Mail')) {
            MailThresholds::dispatch($event->result);
        }
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            SpeedtestFailed::class,
            [self::class, 'handleSpeedtestFailed']
        );

        $events->listen(
            SpeedtestCompleted::class,
            [self::class, 'handleSpeedtestCompleted']
        );

        $events->listen(
            SpeedtestBenchmarkFailed::class,
            [self::class, 'handleSpeedtestBenchmarkFailed']
        );
    }
}
