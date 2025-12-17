<?php

namespace App\Notifications;

use App\Settings\NotificationSettings;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppriseChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        // Get the Apprise message from the notification
        $message = $notification->toApprise($notifiable);

        if (! $message) {
            return;
        }

        $settings = app(NotificationSettings::class);
        $appriseUrl = rtrim($settings->apprise_server_url ?? '', '/');

        if (empty($appriseUrl)) {
            Log::warning('Apprise notification skipped: No Server URL configured');

            return;
        }

        try {
            $request = Http::timeout(5)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ]);

            // If SSL verification is disabled in settings, skip it
            if (! $settings->apprise_verify_ssl) {
                $request = $request->withoutVerifying();
            }

            // Build endpoint URL with optional config key
            $endpoint = $appriseUrl.'/notify';
            if (! empty($settings->apprise_config_key)) {
                $endpoint .= '/'.trim($settings->apprise_config_key, '/');
            }

            // Build payload
            $payload = [
                'title' => $message->title,
                'body' => $message->body,
                'type' => $message->type ?? 'info',
                'format' => $message->format ?? 'text',
            ];

            // Add URLs if direct channel URLs are configured
            if (! empty($message->urls)) {
                $payload['urls'] = $message->urls;
            }

            // Add tags - priority: message tags > message tag > settings tags
            $tags = $message->tags ?? $message->tag ?? $settings->apprise_tags ?? null;
            if (! empty($tags)) {
                $payload['tag'] = $tags;
            }

            $response = $request->post($endpoint, $payload);

            if ($response->failed()) {
                Log::error('Apprise notification failed', [
                    'channel' => $message->urls ?? 'config-based',
                    'tags' => $tags ?? null,
                    'instance' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } else {
                Log::info('Apprise notification sent', [
                    'channel' => $message->urls ?? 'config-based',
                    'tags' => $tags ?? null,
                    'instance' => $endpoint,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Apprise notification exception', [
                'channel' => $message->urls ?? 'config-based',
                'tags' => $tags ?? null,
                'instance' => $endpoint ?? $appriseUrl,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
        }
    }
}
