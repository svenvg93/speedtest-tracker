<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Influxdb\v2\WriteResult;
use App\Jobs\Notifications\Apprise\SendSpeedtestCompletedNotification as AppriseCompleted;
use App\Jobs\Notifications\Apprise\SendSpeedtestThresholdNotification as AppriseThresholds;
use App\Settings\DataIntegrationSettings;
use App\Settings\NotificationSettings;
use Illuminate\Events\Dispatcher;

class SpeedtestEventSubscriber
{
    /**
     * Handle speedtest failed events.
     */
    public function handleSpeedtestFailed(SpeedtestFailed $event): void
    {
        // Handle failed event if necessary
    }

    /**
     * Handle speedtest completed events.
     */
    public function handleSpeedtestCompleted(SpeedtestCompleted $event): void
    {
        $settings = app(DataIntegrationSettings::class);

        // Write to InfluxDB if enabled
        if ($settings->influxdb_v2_enabled) {
            WriteResult::dispatch($event->result);
        }

        $notificationSettings = app(NotificationSettings::class);

        // Send Apprise notification if the setting is enabled
        if ($notificationSettings->apprise_on_speedtest_run) {
            AppriseCompleted::dispatch($event->result);
        }

        // Send threshold failure notification if the setting is enabled
        if ($notificationSettings->apprise_on_threshold_failure) {
            AppriseThresholds::dispatch($event->result);
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            SpeedtestFailed::class,
            [SpeedtestEventSubscriber::class, 'handleSpeedtestFailed']
        );

        $events->listen(
            SpeedtestCompleted::class,
            [SpeedtestEventSubscriber::class, 'handleSpeedtestCompleted']
        );
    }
}
