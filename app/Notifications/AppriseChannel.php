<?php

namespace App\Notifications;

use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
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

        // Get channel URLs - can be string or array
        $urls = is_array($message->urls) ? $message->urls : [$message->urls];

        if (empty($urls)) {
            Log::warning('Apprise notification skipped: No channel URLs configured');

            return;
        }

        try {
            // Build the apprise CLI command
            $command = [
                'apprise',
                '-vv',
                '-i markdown',
                '-t', $message->title,
                '-b', $message->body,
            ];

            // Add all channel URLs
            foreach ($urls as $url) {
                if (! empty($url)) {
                    $command[] = $url;
                }
            }

            // Execute the apprise command
            $result = Process::timeout(30)->run($command);

            // Combine stdout and stderr as apprise outputs to both
            $fullOutput = trim($result->output()."\n".$result->errorOutput());

            if (! $result->successful()) {
                throw new Exception($fullOutput ?: 'Apprise CLI failed with no output');
            }

            Log::info('Apprise notification sent', [
                'channels' => $urls,
                'output' => $fullOutput,
            ]);
        } catch (Throwable $e) {
            Log::error('Apprise notification failed', [
                'channels' => $urls ?? 'unknown',
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            // Re-throw the exception so it can be handled by the queue
            throw $e;
        }
    }
}
