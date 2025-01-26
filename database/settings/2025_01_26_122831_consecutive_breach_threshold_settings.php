<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class ConsecutiveBreachThresholdSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('threshold.consecutive_breach_threshold', 0);
    }
}
