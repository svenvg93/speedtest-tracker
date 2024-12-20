<?php

namespace App\Jobs\Notifications\Database;

use App\Events\SpeedtestCompleted;
use App\Helpers\Number;
use App\Models\User;
use App\Settings\ThresholdSettings;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendSpeedtestThresholdNotification implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public SpeedtestCompleted $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SpeedtestCompleted $event)
    {
        $this->event = $event;
    }

    /**
     * Handle the event.
     */
    public function handle(): void
    {
        $thresholdSettings = new ThresholdSettings;

        if (! $thresholdSettings->absolute_enabled) {
            return;
        }

        // Check if the thresholds are set and call the corresponding methods
        if ($thresholdSettings->absolute_download > 0) {
            $this->absoluteDownloadThreshold($thresholdSettings);
        }

        if ($thresholdSettings->absolute_upload > 0) {
            $this->absoluteUploadThreshold($thresholdSettings);
        }

        if ($thresholdSettings->absolute_ping > 0) {
            $this->absolutePingThreshold($thresholdSettings);
        }
    }

    /**
     * Send database notification if absolute download threshold is breached.
     */
    protected function absoluteDownloadThreshold(ThresholdSettings $thresholdSettings): void
    {
        if (! absoluteDownloadThresholdFailed($thresholdSettings->absolute_download, $this->event->result->download)) {
            return;
        }

        foreach (User::all() as $user) {
            Notification::make()
                ->title('Download threshold breached!')
                ->body('Speedtest #'.$this->event->result->id.' breached the download threshold of '.$thresholdSettings->absolute_download.' Mbps at '.Number::toBitRate($this->event->result->download_bits).'.')
                ->warning()
                ->sendToDatabase($user);
        }
    }

    /**
     * Send database notification if absolute upload threshold is breached.
     */
    protected function absoluteUploadThreshold(ThresholdSettings $thresholdSettings): void
    {
        if (! absoluteUploadThresholdFailed($thresholdSettings->absolute_upload, $this->event->result->upload)) {
            return;
        }

        foreach (User::all() as $user) {
            Notification::make()
                ->title('Upload threshold breached!')
                ->body('Speedtest #'.$this->event->result->id.' breached the upload threshold of '.$thresholdSettings->absolute_upload.' Mbps at '.Number::toBitRate($this->event->result->upload_bits).'.')
                ->warning()
                ->sendToDatabase($user);
        }
    }

    /**
     * Send database notification if absolute ping threshold is breached.
     */
    protected function absolutePingThreshold(ThresholdSettings $thresholdSettings): void
    {
        if (! absolutePingThresholdFailed($thresholdSettings->absolute_ping, $this->event->result->ping)) {
            return;
        }

        foreach (User::all() as $user) {
            Notification::make()
                ->title('Ping threshold breached!')
                ->body('Speedtest #'.$this->event->result->id.' breached the ping threshold of '.$thresholdSettings->absolute_ping.'ms at '.$this->event->result->ping.'ms.')
                ->warning()
                ->sendToDatabase($user);
        }
    }
}
