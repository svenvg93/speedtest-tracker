<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('notification_templates')->insert([
            'name' => 'speedtest-completed-mail',
            'description' => 'Default template for completed speedtest e-mail notifications',
            'title' => 'Speedtest Completed - #{{ $id }}',
            'content' => <<<'BLADE'
<x-mail::message>
# Speedtest Completed - #{{ $id }}

A new speedtest was completed using **{{ $service }}**.

<x-mail::table>
| **Metric**  | **Value**                  |
|:------------|---------------------------:|
| Server name | {{ $serverName }}          |
| Server ID   | {{ $serverId }}            |
| ISP         | {{ $isp }}                 |
| Ping        | {{ $ping }}                |
| Download    | {{ $download }}            |
| Upload      | {{ $upload }}              |
| Packet Loss | {{ $packetLoss }}          |
</x-mail::table>

<x-mail::button :url="$url">
View Results
</x-mail::button>

<x-mail::button :url="$speedtest_url">
View Results on Ookla
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
BLADE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
