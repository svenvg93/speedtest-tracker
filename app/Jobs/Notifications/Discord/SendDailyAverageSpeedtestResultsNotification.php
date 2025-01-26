<?php

namespace App\Jobs\Notifications\Discord;

use App\Helpers\Average;
use App\Models\Result;
use App\Settings\NotificationSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookServer\WebhookCall;

class SendDailyAverageSpeedtestResultsNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Perform the job.
     */
    public function handle()
    {
        // Get all the results for the previous day
        $dailyResults = Result::whereDate('created_at', now()->subDay()->toDateString())->get();

        // Get notification settings
        $notificationSettings = app(NotificationSettings::class);

        // Check if Discord notifications are enabled and if daily average notifications are enabled
        if ($notificationSettings->discord_enabled && $notificationSettings->discord_daily_average) {
            // Prepare the payload for Discord notification
            $payload = [
                'content' => view('discord.daily-average', [
                    'download' => Average::averageDownload($dailyResults),
                    'upload' => Average::averageUpload($dailyResults),
                    'ping' => Average::averagePing($dailyResults),
                    'date' => now()->subDay(), // Pass the previous day as the date
                ])->render(),
            ];

            // Send to each Discord webhook
            foreach ($notificationSettings->discord_webhooks as $url) {
                WebhookCall::create()
                    ->url($url['url']) // Assuming $url is an array with 'url' key
                    ->payload($payload)
                    ->doNotSign()
                    ->dispatch();
            }
        }
    }
}
