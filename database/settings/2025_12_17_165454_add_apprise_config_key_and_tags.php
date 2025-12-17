<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.apprise_config_key', null);
        $this->migrator->add('notification.apprise_tags', []);
    }
};
