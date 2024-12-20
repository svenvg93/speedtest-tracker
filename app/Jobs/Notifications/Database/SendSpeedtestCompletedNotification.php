<?php

namespace App\Jobs\Notifications\Database;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendSpeedtestCompletedNotification implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * Handle the job.
     */
    public function handle(): void
    {

        // Send notification to all users
        foreach (User::all() as $user) {
            Notification::make()
                ->title('Speedtest completed')
                ->success()
                ->sendToDatabase($user);
        }
    }
}
