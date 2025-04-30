<?php

namespace App\Mail;

use App\Models\Result;
use App\Services\Notifications\SpeedtestNotificationData;
use App\Services\Notifications\TemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class SpeedtestThresholdMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Result $result,
        public array $metrics,
    ) {}

    public function envelope(): Envelope
    {
        $template = (new TemplateService)->get('speedtest-threshold-mail');

        $title = Blade::render($template->title, SpeedtestNotificationData::make($this->result));

        return new Envelope(
            subject: $title,
        );
    }

    public function content(): Content
    {
        $template = (new TemplateService)->get('speedtest-threshold-mail');

        return new Content(
            markdown: 'emails.template-wrapper',
            with: [
                'body' => $template->content,
                'data' => array_merge(
                    SpeedtestNotificationData::make($this->result),
                    ['metrics' => $this->metrics]
                ),
            ],
        );
    }
}
