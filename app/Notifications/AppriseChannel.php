<?php

namespace App\Notifications;

use App\Settings\NotificationSettings;
use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

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

            // Build payload
            $payload = [
                'title' => $message->title,
                'body' => $message->body,
                'type' => $message->type ?? 'info',
                'format' => $message->format ?? 'text',
            ];

            // Determine if this is a direct URL notification or config-based notification
            $isDirectUrl = ! empty($message->urls);
            $tags = null;

            if ($isDirectUrl) {
                // Direct URL mode: send to configured Apprise URL with specific URLs
                $endpoint = $appriseUrl;
                $payload['urls'] = $message->urls;

                // Include message-specific tags if provided, but NOT settings tags
                if (! empty($message->tags) || ! empty($message->tag)) {
                    $tags = $message->tags ?? $message->tag;
                    $payload['tag'] = $tags;
                }
            } else {
                // Config-based mode: append config_key to Apprise URL with tags
                $endpoint = $appriseUrl;
                if (! empty($settings->apprise_config_key)) {
                    $endpoint .= '/'.trim($settings->apprise_config_key, '/');
                }

                // Add tags - priority: message tags > message tag > settings tags
                $tags = $message->tags ?? $message->tag ?? $settings->apprise_tags ?? null;
                if (! empty($tags)) {
                    $payload['tag'] = $tags;
                }
            }

            $response = $request->post($endpoint, $payload);

            // Build log context
            $logContext = [
                'instance' => $endpoint,
            ];

            if ($isDirectUrl) {
                $logContext['urls'] = $message->urls;
            }

            if (! empty($tags)) {
                $logContext['tags'] = $tags;
            }

            // Only accept 200 OK responses as successful
            if ($response->status() !== 200) {
                Log::error('Apprise notification failed', array_merge($logContext, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]));
                throw new Exception('Apprise returned an error, please check Apprise logs for details');
            }

            Log::info('Apprise notification sent', $logContext);
        } catch (Throwable $e) {
            $logContext = [
                'instance' => $endpoint ?? $appriseUrl,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ];

            if ($isDirectUrl ?? false) {
                $logContext['urls'] = $message->urls ?? null;
            }

            if (! empty($tags ?? null)) {
                $logContext['tags'] = $tags;
            }

            Log::error('Apprise notification exception', $logContext);

            // Re-throw the exception so it can be handled by the queue
            throw $e;
        }
    }
}
