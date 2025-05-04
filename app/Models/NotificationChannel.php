<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationChannel extends Model
{
    protected $fillable = [
        'type',
        'enabled',
        'on_speedtest_run',
        'on_threshold_failure',
        'config',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'on_speedtest_run' => 'boolean',
        'on_threshold_failure' => 'boolean',
        'config' => 'array',
    ];
}
