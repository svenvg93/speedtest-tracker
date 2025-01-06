<?php

namespace App\Jobs\Notifications\Apprise;

use App\Helpers\Number;
use App\Models\Result;
use App\Settings\NotificationSettings;
use App\Settings\ThresholdSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SendSpeedtestThresholdNotification implements ShouldQueue
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

        $thresholdSettings = app(ThresholdSettings::class);

        // Check if threshold notifications are enabled
        if (! $thresholdSettings->absolute_enabled) {

            return;
        }

        $failed = [];

        // Check for threshold breaches
        if ($thresholdSettings->absolute_download > 0) {
            array_push($failed, $this->absoluteDownloadThreshold($thresholdSettings));
        }

        if ($thresholdSettings->absolute_upload > 0) {
            array_push($failed, $this->absoluteUploadThreshold($thresholdSettings));
        }

        if ($thresholdSettings->absolute_ping > 0) {
            array_push($failed, $this->absolutePingThreshold($thresholdSettings));
        }

        $failed = array_filter($failed);

        // If no thresholds are breached, return early
        if (! count($failed)) {
            Log::warning('Failed apprise thresholds not found, won\'t send notification.');

            return;
        }

        // Prepare the payload using the view
        $payload = view('apprise.speedtest-threshold', [
            'id' => $this->result->id,
            'service' => Str::title($this->result->service->getLabel()),
            'serverName' => $this->result->server_name,
            'serverId' => $this->result->server_id,
            'isp' => $this->result->isp,
            'metrics' => $failed,
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

    /**
     * Build apprise notification if absolute download threshold is breached.
     */
    protected function absoluteDownloadThreshold(ThresholdSettings $thresholdSettings): bool|array
    {
        if (! absoluteDownloadThresholdFailed($thresholdSettings->absolute_download, $this->result->download)) {
            return false;
        }

        return [
            'name' => 'Download',
            'threshold' => $thresholdSettings->absolute_download.' Mbps',
            'value' => Number::toBitRate(bits: $this->result->download_bits, precision: 2),
        ];
    }

    /**
     * Build apprise notification if absolute upload threshold is breached.
     */
    protected function absoluteUploadThreshold(ThresholdSettings $thresholdSettings): bool|array
    {
        if (! absoluteUploadThresholdFailed($thresholdSettings->absolute_upload, $this->result->upload)) {
            return false;
        }

        return [
            'name' => 'Upload',
            'threshold' => $thresholdSettings->absolute_upload.' Mbps',
            'value' => Number::toBitRate(bits: $this->result->upload_bits, precision: 2),
        ];
    }

    /**
     * Build apprise notification if absolute ping threshold is breached.
     */
    protected function absolutePingThreshold(ThresholdSettings $thresholdSettings): bool|array
    {
        if (! absolutePingThresholdFailed($thresholdSettings->absolute_ping, $this->result->ping)) {
            return false;
        }

        return [
            'name' => 'Ping',
            'threshold' => $thresholdSettings->absolute_ping.' ms',
            'value' => round($this->result->ping, 2).' ms',
        ];
    }
}
