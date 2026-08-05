<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class Benchmark
{
    private const FAILED_COLOR = 'rgba(239, 68, 68)';

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

    /**
     * Get Chart.js point style overrides that highlight results which failed a benchmarked metric's threshold.
     *
     * @return array{pointBackgroundColor: array<int, string>|string, pointBorderColor: array<int, string>|string, pointRadius: array<int, int>|int}
     */
    public static function pointStyles(Collection $results, string $metric, string $color, bool $showFailedThreshold, int $radius = 3, int $maxVisiblePoints = 24): array
    {
        $defaultRadius = $results->count() <= $maxVisiblePoints ? $radius : 0;

        if (! $showFailedThreshold) {
            return [
                'pointBackgroundColor' => $color,
                'pointBorderColor' => $color,
                'pointRadius' => $defaultRadius,
            ];
        }

        $failed = fn ($result): bool => ($result->benchmarks[$metric]['passed'] ?? true) === false;

        return [
            'pointBackgroundColor' => $results->map(fn ($result) => $failed($result) ? static::FAILED_COLOR : $color)->all(),
            'pointBorderColor' => $results->map(fn ($result) => $failed($result) ? static::FAILED_COLOR : $color)->all(),
            'pointRadius' => $results->map(fn ($result) => $failed($result) ? 2 : $defaultRadius)->all(),
        ];
    }
}
