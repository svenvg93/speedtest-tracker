<?php

namespace App\Jobs\Prometheus;

use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;

class UpdatePrometheusMetrics implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected Result $result;

    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    public function handle()
    {
        // Persist the raw payload in Laravel cache
        Cache::forever('speedtest.last_payload', $this->result->toArray());
    }
}
