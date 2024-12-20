<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Notifications\Database\SendSpeedtestCompletedNotification as DatabaseCompleted;
use App\Jobs\Notifications\Database\SendSpeedtestThresholdNotification as DatabaseThresholds;
use App\Models\NotificationSetting;
use App\Settings\DataIntegrationSettings;
use Illuminate\Events\Dispatcher;

class SpeedtestEventSubscriber
{
    /**
     * Handle speedtest failed events.
     */
    public function handleSpeedtestFailed(SpeedtestFailed $event): void
    {
        // Logic for handling failed events (if needed)
    }

    /**
     * Handle speedtest completed events.
     */
    public function handleSpeedtestCompleted(SpeedtestCompleted $event): void
    {
        // Check if there's a database notification setting
        $notificationSetting = NotificationSetting::where('type', 'database')->first();

        // If the setting exists and every_run is true, dispatch the job
        if ($notificationSetting && $notificationSetting->every_run) {
            // Dispatch the job to send the notification
            DatabaseCompleted::dispatch();
        }

        // If the setting exists and threshold is true, dispatch the job
        if ($notificationSetting && $notificationSetting->threshold) {
            // Dispatch the job to send the notification
            DatabaseThresholds::dispatch($event);
        }

        // Additional logic for other integrations (e.g., InfluxDB)
        $settings = app(DataIntegrationSettings::class);

        if ($settings->influxdb_v2_enabled) {
            WriteResult::dispatch($event->result);
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
