<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notification Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for various notification messages
    | that we need to display to the user. You are free to modify these
    | language lines according to your application's requirements.
    |
    */

    // Notification Settings
    'label' => 'Notifications',
    'triggers' => 'Triggers',
    'notify_on_every_speedtest_run' => 'Notify on every speedtest run',
    'notify_on_threshold_failures' => 'Notify on threshold failures',
    'threshold_helper_text' => 'Set thresholds for speedtest results. Leave empty to disable.',

    // Database Notifications
    'database' => 'Database',
    'database_description' => 'Configure database notification settings.',
    'enable_database_notifications' => 'Enable Database Notifications',
    'test_database_channel' => 'Test Database Channel',

    // Email Notifications
    'mail' => 'Mail',
    'enable_mail_notifications' => 'Enable Mail Notifications',
    'recipients' => 'Recipients',
    'email_address' => 'Email Address',
    'test_mail_channel' => 'Test Mail Channel',

    // Webhook Notifications
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'enable_webhook_notifications' => 'Enable Webhook Notifications',
    'test_webhook_channel' => 'Test Webhook Channel',

    // Discord
    'discord' => 'Discord',
    'enable_discord_webhook_notifications' => 'Enable Discord Webhook Notifications',
    'test_discord_webhook' => 'Test Discord Webhook',

    // Gotify
    'gotify' => 'Gotify',
    'gotify_enabled' => 'Enable Gotify Notifications',
    'test_gotify_webhook' => 'Test Gotify Webhook',

    // Healthchecks.io
    'healthcheck_io' => 'Healthchecks.io',
    'healthcheck_enabled' => 'Enable Healthchecks.io Notifications',
    'test_healthcheck_webhook' => 'Test Healthchecks.io Webhook',

    // Ntfy
    'ntfy' => 'Ntfy',
    'ntfy_enabled' => 'Enable Ntfy Notifications',
    'test_ntfy_webhook' => 'Test Ntfy Webhook',
    'your_ntfy_server_url' => 'Your Ntfy Server URL',
    'your_ntfy_topic' => 'Your Ntfy Topic',
    'topic' => 'Topic',
    'username' => 'Username',
    'username_placeholder' => 'Username (optional)',
    'password' => 'Password',
    'password_placeholder' => 'Password (optional)',

    // Pushover
    'pushover' => 'Pushover',
    'pushover_webhooks' => 'Pushover Webhooks',
    'enable_pushover_webhook_notifications' => 'Enable Pushover Notifications',
    'test_pushover_webhook' => 'Test Pushover Webhook',
    'your_pushover_api_token' => 'Your Pushover API Token',
    'your_pushover_user_key' => 'Your Pushover User Key',
    'user_key' => 'User Key',

    // Slack
    'slack' => 'Slack',
    'slack_enabled' => 'Enable Slack Notifications',
    'test_slack_webhook' => 'Test Slack Webhook',

    // Telegram
    'telegram' => 'Telegram',
    'enable_telegram' => 'Enable Telegram Notifications',
    'telegram_disable_notification' => 'Disable Notification',
    'test_telegram_webhook' => 'Test Telegram',


    // Database Notifications
    'database' => [
        'title' => 'Database',
        'received' => 'Test melding ontvangen',
        'pong' => 'Pong!',
        'sent' => 'Test melding verzonden',
        'ping' => 'Ping!',
    ],

    // Discord Notifications
    'discord' => [
        'title' => 'Discord',
        'add' => 'Voeg ten minste één Discord webhook toe.',
        'payload' => 'Dit is een test melding van Snel Testen Tracker.',
        'sent' => 'Testmelding verzonden naar Discord.',
    ],

    // Gotify Notifications (Note: typo in code - 'gotfy' instead of 'gotify')
    'gotfy' => [
        'title' => 'Gotify',
        'add' => 'Voeg ten minste één Gotify webhook toe.',
        'payload' => 'Dit is een test melding van Snel Testen Tracker.',
        'sent' => 'Test melding verzonden naar Gotify.',
    ],

    // Health Check Notifications
    'health_check' => [
        'title' => 'Healthchecks.io',
        'helper_text' => 'Threshold notifications will be sent to the /fail path of the URL.',
        'add' => 'Voeg ten minste één Healthchecks.io webhook toe.',
        'payload' => 'Dit is een test melding van Snel Testen Tracker.',
        'sent' => 'Test melding verzonden naar Healthchecks.io.',
    ],

    // Mail Notifications
    'mail' => [
        'title' => 'Mail',
        'add' => 'Voeg ten minste één e-mailontvanger toe.',
        'sent' => 'Test e-mail succesvol verzonden.',
    ],

    // Ntfy Notifications
    'ntfy' => [
        'title' => 'Ntfy',
        'add' => 'Voeg ten minste één Ntfy webhook toe.',
        'payload' => 'Dit is een test melding van Snel Testen Tracker.',
        'sent' => 'Testmelding verzonden naar Ntfy.',
    ],

    // Pushover Notifications
    'pushover' => [
        'title' => 'Pushover',
        'add' => 'Voeg ten minste één Pushover webhook toe.',
        'payload' => 'Dit is een test melding van Snel Testen Tracker.',
        'sent' => 'Test melding verzonden naar Pushover.',
    ],

    // Slack Notifications
    'slack' => [
        'title' => 'Slack',
        'add' => 'Voeg ten minste één Slack webhook toe.',
        'payload' => 'Dit is een test melding van Snel Testen Tracker.',
        'sent' => 'Testmelding verzonden naar Slack.',
    ],

    // Telegram Notifications
    'telegram' => [
        'title' => 'Telegram',
        'add' => 'Voeg ten minste één ontvangers voor Telegram toe.',
        'sent' => 'Test melding verzonden naar Telegram.',
        'test_message' => '👋 Telegram meldingskanaal testen.',
    ],

    // Webhook Notifications
    'webhook' => [
        'title' => 'Webhook',
        'add' => 'Voeg ten minste één webhook toe.',
        'payload' => 'Snelheidstest Tracker Test',
        'sent' => 'Test webhook succesvol verzonden.',
    ],

];
