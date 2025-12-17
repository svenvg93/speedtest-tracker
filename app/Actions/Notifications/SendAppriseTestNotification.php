<?php

namespace App\Actions\Notifications;

use App\Notifications\Apprise\TestNotification;
use App\Settings\NotificationSettings;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Lorisleiva\Actions\Concerns\AsAction;

class SendAppriseTestNotification
{
    use AsAction;

    public function handle(array $channel_urls)
    {
        $settings = app(NotificationSettings::class);
        $hasChannelUrls = count($channel_urls) > 0;
        $hasConfigKey = ! empty($settings->apprise_config_key);

        // Check if we have at least one method configured
        if (! $hasChannelUrls && ! $hasConfigKey) {
            Notification::make()
                ->title('You need to add Apprise channel URLs or configure a config key with tags!')
                ->warning()
                ->send();

            return;
        }

        // If config key is set, send a test notification using config-based routing
        if ($hasConfigKey) {
            FacadesNotification::route('apprise_urls', null)
                ->notify(new TestNotification);
        }

        // If channel URLs are set, send test notifications to each
        if ($hasChannelUrls) {
            foreach ($channel_urls as $row) {
                $channelUrl = $row['channel_url'] ?? null;
                if (! $channelUrl) {
                    Notification::make()
                        ->title('Skipping missing channel URL!')
                        ->warning()
                        ->send();

                    continue;
                }

                FacadesNotification::route('apprise_urls', $channelUrl)
                    ->notify(new TestNotification);
            }
        }

        Notification::make()
            ->title('Test Apprise notification sent.')
            ->success()
            ->send();
    }
}
