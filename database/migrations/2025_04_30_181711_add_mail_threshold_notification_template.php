<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('notification_templates')->insert([
            'name' => 'speedtest-threshold-mail',
            'description' => 'Default template for threshold breached e-mail notifications',
            'title' => 'Speedtest Breached - #{{ $id }}',
            'content' => <<<'BLADE'
<x-mail::message>
# Speedtest Threshold Breached - #{{ $id }}

A new speedtest was completed using **{{ $service }}** on **{{ $isp }}** but a threshold was breached.

<x-mail::table>
| **Metric** | **Threshold** | **Value** |
|:-----------|:--------------|----------:|
@foreach ($metrics as $item)
| {{ $item['name'] }} | {{ $item['threshold'] }} | {{ $item['value'] }} |
@endforeach
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
