<?php

namespace App\Actions\Notifications;

use App\Notifications\Apprise\TestNotification;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class SendAppriseTestNotification
{
    use AsAction;

    public function handle(array $channel_urls): void
    {
        if (! count($channel_urls)) {
            Notification::make()
                ->title('You need to add Apprise channel URLs!')
                ->warning()
                ->send();

            return;
        }

        try {
            foreach ($channel_urls as $row) {
                $channelUrl = $row['channel_url'] ?? null;
                if (! $channelUrl) {
                    continue;
                }

                // Use notifyNow() to send synchronously even though notification implements ShouldQueue
                // This allows us to catch exceptions and show them in the UI immediately
                FacadesNotification::route('apprise_urls', $channelUrl)
                    ->notifyNow(new TestNotification);
            }
        } catch (Throwable $e) {
            Notification::make()
                ->title('Failed to send Apprise test notification')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title('Test Apprise notification sent.')
            ->success()
            ->send();
    }
}
