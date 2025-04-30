<?php

namespace App\Services\Notifications;

use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Blade;

class TemplateService
{
    public function get(string $templateName): NotificationTemplate
    {
        return NotificationTemplate::where('name', $templateName)->firstOrFail();
    }

    public function render(string $templateName, array $data): string
    {
        $template = NotificationTemplate::where('name', $templateName)->firstOrFail();

        return Blade::render($template->content, $data);
    }
}
