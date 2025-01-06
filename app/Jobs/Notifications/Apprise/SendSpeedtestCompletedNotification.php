<?php

namespace App\Jobs\Notifications\Apprise;

use App\Helpers\Number;
use App\Models\Result;
use App\Settings\NotificationSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SendSpeedtestCompletedNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    public Result $result;

    /**
     * Create a new job instance.
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Handle the event.
     */
    public function handle(): void
    {
        // Resolve NotificationSettings from the service container
        $notificationSettings = app(NotificationSettings::class);

        // Ensure we have at least one Apprise webhook URL
        if (! count($notificationSettings->apprise_webhooks)) {
            Log::warning('Apprise URLs not found, check Apprise notification channel settings.');

            return;
        }

        // Prepare the notification message using the view
        $payload = view('apprise.speedtest-completed', [
            'id' => $this->result->id,
            'service' => Str::title($this->result->service->getLabel()),
            'serverName' => $this->result->server_name,
            'serverId' => $this->result->server_id,
            'isp' => $this->result->isp,
            'ping' => round($this->result->ping).' ms',
            'download' => Number::toBitRate(bits: $this->result->download_bits, precision: 2),
            'upload' => Number::toBitRate(bits: $this->result->upload_bits, precision: 2),
            'packetLoss' => $this->result->packet_loss,
            'speedtest_url' => $this->result->result_url,
            'url' => url('/admin/results'),
        ])->render();

        // Loop through the webhooks and send the notifications
        foreach ($notificationSettings->apprise_webhooks as $webhook) {
            if (empty($webhook['service_url'])) {
                Log::warning('Webhook is missing a service URL.');

                continue;
            }

            // Build the command as an array
            $command = [
                'apprise',
                '-b',
                $payload,
                $webhook['service_url'],
            ];

            // Execute the command using Symfony Process
            $process = new Process($command);

            try {
                $process->mustRun();
            } catch (\Exception $e) {
                Log::error('Failed to send Apprise notification to '.$webhook['service_url'].': '.$e->getMessage(), [
                    'output' => $process->getOutput(),
                    'errorOutput' => $process->getErrorOutput(),
                ]);
            }
        }
    }
}
