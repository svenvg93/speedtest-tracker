<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $default_chart_range;

    public bool $chart_begin_at_zero;

    public string $chart_datetime_format;

    public bool $chart_show_average;

    public bool $chart_show_failed_threshold;

    public static function group(): string
    {
        return 'general';
    }
}
