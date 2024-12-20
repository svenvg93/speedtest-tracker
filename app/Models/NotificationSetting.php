<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'type',
        'name',
        'every_run',
        'threshold',
        'apprise_webhook_url',
        'apprise_service_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
