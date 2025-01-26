<?php

namespace App\Actions\Notifications;

use App\Jobs\Notifications\Discord\SendWeeklyAverageSpeedtestResultsNotification;
use App\Settings\NotificationSettings;

class CheckAndSendDailyAverageNotifications
{
    public static function run()
    {
        // Logic to check if notifications are enabled
        $notificationSettings = new NotificationSettings;

        if ($notificationSettings->discord_weekly_average) {
            // Dispatch the job to send the daily average notification
            SendWeeklyAverageSpeedtestResultsNotification::dispatch();
        }
    }
}
