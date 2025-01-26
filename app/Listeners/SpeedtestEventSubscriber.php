<?php

namespace App\Listeners;

use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Jobs\Influxdb\v2\WriteResult;
use App\Jobs\Notifications\Discord\SendSpeedtestFailedNotification as DiscordFailed;
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

        $notificationSettings = app(NotificationSettings::class);

        // Send Apprise notification if the setting is enabled
        if ($notificationSettings->discord_on_speedtest_failure) {
            DiscordFailed::dispatch($event->result);
        }

    }

    /**
     * Handle speedtest completed events.
     */
    public function handleSpeedtestCompleted(SpeedtestCompleted $event): void
    {
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
