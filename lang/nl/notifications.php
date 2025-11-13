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

    // Database Notifications
    'database' => [
        'received' => 'Test melding ontvangen',
        'pong' => 'Pong!',
        'sent' => 'Test melding verzonden',
        'ping' => 'Ping!',
    ],

    // Discord Notifications
    'discord' => [
        'add' => 'Voeg ten minste één Discord webhook toe.',
        'payload' => 'Dit is een test melding van Snel Testen Tracker.',
        'sent' => 'Testmelding verzonden naar Discord.',
    ],

    // Gotify Notifications (Note: typo in code - 'gotfy' instead of 'gotify')
    'gotfy' => [
        'add' => 'Voeg ten minste één Gotify webhook toe.',
        'payload' => 'Dit is een test melding van Snel Testen Tracker.',
        'sent' => 'Test melding verzonden naar Gotify.',
    ],

    // Health Check Notifications
    'health_check' => [
        'add' => 'Voeg ten minste één Healthchecks.io webhook toe.',
        'payload' => 'Dit is een test melding van Snel Testen Tracker.',
        'sent' => 'Test melding verzonden naar Healthchecks.io.',
    ],

    // Mail Notifications
    'mail' => [
        'add' => 'Voeg ten minste één e-mailontvanger toe.',
        'sent' => 'Test e-mail succesvol verzonden.',
    ],

    // Ntfy Notifications
    'ntfy' => [
        'add' => 'Voeg ten minste één Ntfy webhook toe.',
        'payload' => 'Dit is een test melding van Snel Testen Tracker.',
        'sent' => 'Testmelding verzonden naar Ntfy.',
    ],

    // Pushover Notifications
    'pushover' => [
        'add' => 'Voeg ten minste één Pushover webhook toe.',
        'payload' => 'Dit is een test melding van Snel Testen Tracker.',
        'sent' => 'Test melding verzonden naar Pushover.',
    ],

    // Slack Notifications
    'slack' => [
        'add' => 'Voeg ten minste één Slack webhook toe.',
        'payload' => 'Dit is een test melding van Snel Testen Tracker.',
        'sent' => 'Testmelding verzonden naar Slack.',
    ],

    // Telegram Notifications
    'telegram' => [
        'add' => 'Voeg ten minste één ontvangers voor Telegram toe.',
        'sent' => 'Test melding verzonden naar Telegram.',
        'test_message' => '👋 Telegram meldingskanaal testen.',
    ],

    // Webhook Notifications
    'webhook' => [
        'add' => 'Voeg ten minste één webhook toe.',
        'payload' => 'Snelheidstest Tracker Test',
        'sent' => 'Test webhook succesvol verzonden.',
    ],

];
