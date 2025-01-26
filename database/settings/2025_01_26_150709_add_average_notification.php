<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.discord_daily_average', false);
        $this->migrator->add('notification.discord_weekly_average', false);

    }
};
