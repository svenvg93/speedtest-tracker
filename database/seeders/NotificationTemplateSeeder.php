<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTemplateSeeder extends Seeder
{
    public function run()
    {
        DB::table('notification_templates')->insert([
            [
                'name' => 'speedtest-completed',
                'description' => 'Template for completed speedtest notifications',
                'content' => <<<'TEMPLATE'
                **Speedtest Completed - #{{ $id }}**

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
            ],
            [
                'name' => 'speedtest-threshold',
                'description' => 'Template for threshold breached notifications',
                'content' => <<<'TEMPLATE'
                **Speedtest Threshold Breached - #{{ $id }}**

                A new speedtest on **{{ config('app.name') }}** was completed using **{{ $service }}** on **{{ $isp }}** but a threshold was breached.

                @foreach ($metrics as $item)
                - **{{ $item['name'] }}** {{ $item['threshold'] }}: {{ $item['value'] }}
                @endforeach
                - **Ookla Speedtest:** {{ $speedtest_url }}
                - **URL:** {{ $url }}
                TEMPLATE,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}