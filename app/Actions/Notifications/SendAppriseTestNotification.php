<?php

namespace App\Actions\Notifications;

use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Process\Process;

class SendAppriseTestNotification
{
    use AsAction;

    public function handle(array $webhooks)
    {
        if (! count($webhooks)) {
            Notification::make()->title('You need to add Apprise webhooks!')->warning()->send();

            return;
        }

        foreach ($webhooks as $webhook) {
            if (empty($webhook['service_url'])) {
                Notification::make()->title('There is no Service URL set!')->warning()->send();

                continue;
            }

            // Build the command as an array
            $command = array_filter([
                'apprise',
                '-b',
                '👋 Testing the Apprise notification channel.',
                $webhook['service_url'],
            ]);

            // Create and run the process
            $process = new Process($command);

            try {
                $process->mustRun();
                Notification::make()->title('Apprise notification sent successfully.')->success()->send();
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Failed to send Apprise notification.')
                    ->warning()
                    ->body('Error: '.$e->getMessage())
                    ->send();
            }
        }
    }
}
