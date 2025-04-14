<?php

namespace App\Helpers;

class Threshold
{
    /**
     * Calculate the percentage difference from a threshold.
     *
     * @param float|null $value The value to compare.
     * @param float|null $threshold The threshold value.
     * @param bool $isPing Whether the metric is for ping (lower is better).
     * @return float|null The percentage difference.
     */
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
}
