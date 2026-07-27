<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.default_chart_range', '24h');
        $this->migrator->add('general.chart_begin_at_zero', config('app.chart_begin_at_zero'));
    }
};
