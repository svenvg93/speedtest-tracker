<?php

namespace App\Helpers;

use App\Settings\ThresholdSettings;
use Illuminate\Support\Collection;

class Threshold
{
    public static function calculatePercentage($value, $threshold, $isPing = false)
    {
        if ($value === null || $threshold === null) {
            return null;
        }

        if ($isPing) {
            // For ping, lower is better, so we invert the percentage calculation.
            return (($threshold - $value) / $threshold) * 100;
        }

        // For download/upload, higher is better.
        return (($value - $threshold) / $threshold) * 100;
    }

    public static function getBreachedTestPercentage(Collection $results): ?float
    {
        // Get the threshold settings
        $thresholds = app(ThresholdSettings::class);

        if (! $thresholds->absolute_enabled) {
            return null;
        }

        $thresholdResults = $results->filter(fn ($result) => ! is_null($result->healthy));

        $totalTests = $thresholdResults->count();
        $thresholdBreachedTests = $thresholdResults->where('healthy', false)->count();

        return $totalTests > 0 ? ($thresholdBreachedTests / $totalTests) * 100 : 0;
    }

    public static function evaluateMetric(?float $value, string $metricType): array
    {
        $settings = app(ThresholdSettings::class);

        // If thresholds globally disabled, or no value, skip
        if (! $settings->absolute_enabled || $value === null) {
            return [
                'percentage' => null,
                'description' => null,
                'isPositive' => null,
            ];
        }

        // Pick the right threshold and ping‐flag
        $threshold = match ($metricType) {
            'download' => $settings->absolute_download,
            'upload' => $settings->absolute_upload,
            'ping' => $settings->absolute_ping,
            default => null,
        };

        if (! $threshold) {
            return [
                'percentage' => null,
                'description' => null,
                'isPositive' => null,
            ];
        }

        $isPing = $metricType === 'ping';
        $percent = static::calculatePercentage($value, $threshold, $isPing);

        if ($percent === null) {
            return [
                'percentage' => null,
                'description' => null,
                'isPositive' => null,
            ];
        }

        // Build a human‐friendly description
        $abs = number_format(abs($percent), 1);
        if ($metricType === 'ping') {
            $desc = $percent >= 0
                ? "{$abs}% better than threshold"
                : "{$abs}% worse than threshold";
        } else {
            $desc = $percent >= 0
                ? "{$abs}% above threshold"
                : "{$abs}% below threshold";
        }

        return [
            'percentage' => $percent,
            'description' => $desc,
            'isPositive' => $percent >= 0,
        ];
    }
}
