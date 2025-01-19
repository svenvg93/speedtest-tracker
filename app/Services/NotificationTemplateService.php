<?php

namespace App\Services;

use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Blade;

class NotificationTemplateService
{
    public function render(string $templateName, array $data): string
    {
        $template = NotificationTemplate::where('name', $templateName)->firstOrFail();

        return Blade::render($template->content, $data);
    }
}
