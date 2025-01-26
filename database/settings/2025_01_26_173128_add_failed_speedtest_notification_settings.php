<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddFailedSpeedtestNotificationSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.discord_on_speedtest_failure', false);

    }
}
