<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class Benchmark
{
    /**
     * Validate if the bitrate passes the benchmark.
     */
    public static function bitrate(float|int $bytes, array $benchmark): bool
    {
        $value = Arr::get($benchmark, 'value');

        $unit = Arr::get($benchmark, 'unit');

        // Pass the benchmark if the value or unit is empty.
        if (blank($value) || blank($unit)) {
            return true;
        }

        return Bitrate::bytesToBits($bytes) >= Bitrate::normalizeToBits($value.$unit);
    }

    /**
     * Validate if the ping passes the benchmark.
     */
    public static function ping(float|int $ping, array $benchmark): bool
    {
        $value = Arr::get($benchmark, 'value');

        // Pass the benchmark if the value is empty.
        if (blank($value)) {
            return true;
        }

        return $ping < $value;
    }

    /**
     * Calculate the percentage of results that failed a benchmarked metric (download, upload, or ping),
     * or null if none of the results have that metric measured (thresholds disabled or unconfigured).
     */
    public static function failedPercentage(Collection $results, string $metric, int $precision = 2): ?float
    {
        $measured = $results->filter(fn ($result) => isset($result->benchmarks[$metric]['passed']));

        if ($measured->isEmpty()) {
            return null;
        }

        $failed = $measured->filter(fn ($result) => $result->benchmarks[$metric]['passed'] === false)->count();

        return round($failed / $measured->count() * 100, $precision);
    }
}
