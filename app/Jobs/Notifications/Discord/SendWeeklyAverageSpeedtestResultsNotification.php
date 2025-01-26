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

class SendWeeklyAverageSpeedtestResultsNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Perform the job.
     */
    public function handle()
    {
        // Get all the results for the previous day
        $startOfLastWeek = now()->subWeek()->startOfWeek(); // Start of last week (Monday)
        $endOfLastWeek = now()->subWeek()->endOfWeek(); // End of last week (Sunday)

        $dailyResults = Result::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->get();

        // Get notification settings
        $notificationSettings = app(NotificationSettings::class);

        // Check if Discord notifications are enabled and if daily average notifications are enabled
        if ($notificationSettings->discord_enabled && $notificationSettings->discord_weekly_average) {
            // Prepare the payload for Discord notification
            $payload = [
                'content' => view('discord.weekly-average', [
                    'download' => Average::averageDownload($dailyResults),
                    'upload' => Average::averageUpload($dailyResults),
                    'ping' => Average::averagePing($dailyResults),
                    'date' => now()->subWeek()->weekOfYear, // Pass the previous week's number
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
