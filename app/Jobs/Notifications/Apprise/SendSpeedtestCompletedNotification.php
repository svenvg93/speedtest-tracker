<?php

namespace App\Jobs\Notifications\Apprise;

use App\Helpers\Number;
use App\Models\NotificationChannel;
use App\Models\Result;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendSpeedtestCompletedNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    public Result $result;

    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    public function handle(): void
    {
        $channels = NotificationChannel::query()
            ->where('type', 'Apprise')
            ->where('enabled', true)
            ->where('on_speedtest_run', true)
            ->get();

        if ($channels->isEmpty()) {
            Log::info('No enabled Apprise channels for speedtest run.');

            return;
        }

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

        $client = new Client;

        foreach ($channels as $channel) {
            $webhooks = $channel->config['apprise_webhooks'] ?? [];

            foreach ($webhooks as $webhook) {
                if (empty($webhook['service_url']) || empty($webhook['url'])) {
                    Log::warning('Apprise webhook missing service URL or base URL, skipping.');

                    continue;
                }

                $webhookPayload = [
                    'body' => $payload,
                    'title' => "Speedtest Completed - #{$this->result->id}",
                    'type' => 'info',
                    'urls' => $webhook['service_url'],
                ];

                try {
                    $response = $client->post($webhook['url'], [
                        'json' => $webhookPayload,
                        'headers' => ['Content-Type' => 'application/json'],
                    ]);

                    Log::info("Apprise notification sent to {$webhook['url']} (channel ID: {$channel->id})");
                } catch (RequestException $e) {
                    Log::error("Apprise notification failed for {$webhook['url']}: {$e->getMessage()}");
                }
            }
        }
    }
}
