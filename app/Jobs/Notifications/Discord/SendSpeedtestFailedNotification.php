<?php

namespace App\Jobs\Notifications\Discord;

use App\Models\Result;
use App\Settings\NotificationSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookServer\WebhookCall;

class SendSpeedtestFailedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Result $result;

    /**
     * Create a new job instance.
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Handle the job.
     */
    public function handle(): void
    {
        $notificationSettings = new NotificationSettings;

        if (! count($notificationSettings->discord_webhooks)) {
            Log::warning('Discord URLs not found. Check Discord notification channel settings.');

            return;
        }

        $payload = [
            'content' => view('discord.speedtest-failed', [
                'id' => $this->result->id,
                'message' => $this->result->data['message'] ?? 'No message available',
                'url' => url('/admin/results'),
            ])->render(),
        ];

        foreach ($notificationSettings->discord_webhooks as $url) {
            WebhookCall::create()
                ->url($url['url'])
                ->payload($payload)
                ->doNotSign()
                ->dispatch();
        }
    }
}
