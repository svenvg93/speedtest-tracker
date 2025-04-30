<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Inserting the "complete" notification template
        DB::table('notification_templates')->insert([
            'name' => 'speedtest-completed',
            'description' => 'Template for completed speedtest notifications',
            'title' => 'Speedtest Completed - #{{ $id }}',
            'content' => <<<'TEMPLATE'
            A new speedtest on **{{ config('app.name') }}** was completed using **{{ $service }}**.

            - **Server name:** {{ $serverName }}
            - **Server ID:** {{ $serverId }}
            - **ISP:** {{ $isp }}
            - **Ping:** {{ $ping }}
            - **Download:** {{ $download }}
            - **Upload:** {{ $upload }}
            - **Packet Loss:** {{ $packetLoss }} **%**
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
        DB::table('notification_templates')->where('name', 'speedtest-completed')->delete();
    }
};
