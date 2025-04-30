<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Inserting the "threshold" notification template
        DB::table('notification_templates')->insert([
            'name' => 'speedtest-threshold',
            'description' => 'Default template for threshold breached Apprise notifications',
            'title' => 'Speedtest Breached - #{{ $id }}',
            'content' => <<<'TEMPLATE'
            A new speedtest on **{{ config('app.name') }}** was completed using **{{ $service }}** on **{{ $isp }}** but a threshold was breached.

            @foreach ($metrics as $item)
            - **{{ $item['name'] }}** {{ $item['threshold'] }}: {{ $item['value'] }}
            @endforeach
            - **Ookla Speedtest:** {{ $speedtest_url }}
            - **URL:** {{ $url }}
            TEMPLATE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Optionally, remove the template when rolling back the migration
        DB::table('notification_templates')->where('name', 'speedtest-threshold')->delete();
    }
};
