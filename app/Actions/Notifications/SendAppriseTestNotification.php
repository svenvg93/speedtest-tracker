<?php

namespace App\Actions\Notifications;

use App\Notifications\Apprise\TestNotification;
use App\Settings\NotificationSettings;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class SendAppriseTestNotification
{
    use AsAction;

    public function handle(array $channel_urls): void
    {
        $settings = app(NotificationSettings::class);
        $hasChannelUrls = count($channel_urls) > 0;
        $hasTags = ! empty($settings->apprise_tags);
        $appriseUrl = rtrim($settings->apprise_server_url ?? '', '/');

        // Check if Apprise Server URL is configured
        if (empty($appriseUrl)) {
            Notification::make()
                ->title('Apprise Server URL is not configured')
                ->body('Please configure the Apprise Server URL in the settings above.')
                ->danger()
                ->send();

            return;
        }

        // Check if we have at least one method configured (mutually exclusive)
        if (! $hasChannelUrls && ! $hasTags) {
            Notification::make()
                ->title('You need to add Apprise channel URLs or tags!')
                ->warning()
                ->send();

            return;
        }

        try {
            // Use either tags OR channel URLs (mutually exclusive)
            if ($hasTags) {
                // Tag-based routing: send to server URL (may include config key) with tags
                FacadesNotification::route('apprise_urls', null)
                    ->notifyNow(new TestNotification);
            } elseif ($hasChannelUrls) {
                // Direct URL routing: send test notifications to each configured channel URL
                foreach ($channel_urls as $item) {
                    $channelUrl = is_array($item) ? ($item['channel_url'] ?? null) : $item;

                    if (empty($channelUrl)) {
                        Notification::make()
                            ->title('Skipping missing channel URL!')
                            ->warning()
                            ->send();

                        continue;
                    }

                    FacadesNotification::route('apprise_urls', $channelUrl)
                        ->notifyNow(new TestNotification);
                }
            }
        } catch (Throwable $e) {
            $errorMessage = $this->cleanErrorMessage($e);

            Notification::make()
                ->title('Failed to send Apprise test notification')
                ->body($errorMessage)
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title('Test Apprise notification sent.')
            ->success()
            ->send();
    }

    /**
     * Clean up error message for display in UI.
     */
    protected function cleanErrorMessage(Throwable $e): string
    {
        $message = $e->getMessage();

        // Get the full Apprise server URL for error messages
        $settings = app(NotificationSettings::class);
        $appriseUrl = rtrim($settings->apprise_server_url ?? '', '/');

        // Handle connection errors - extract just the important part
        if (str_contains($message, 'cURL error')) {
            if (str_contains($message, 'Could not resolve host')) {
                return "Could not connect to Apprise server at {$appriseUrl}";
            }

            if (str_contains($message, 'Connection refused')) {
                return "Connection refused by Apprise server at {$appriseUrl}";
            }

            if (str_contains($message, 'Operation timed out')) {
                return "Connection to Apprise server at {$appriseUrl} timed out";
            }

            return "Failed to connect to Apprise server at {$appriseUrl}";
        }

        return $message;
    }
}
