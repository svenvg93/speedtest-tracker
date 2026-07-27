<?php

namespace App\Filament\Widgets;

use App\Helpers\Average;
use App\Helpers\Benchmark;
use App\Helpers\Number;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Collection;

class RecentDownloadChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected ?string $heading = null;

    public function getHeading(): ?string
    {
        return __('general.download');
    }

    public function getDescription(): ?string
    {
        $results = $this->getResults();

        $description = __('general.average').': '.number_format(Average::averageDownload($results), 2).' '.__('general.mbps');

        $failedPercent = Benchmark::failedPercentage($results, 'download');

        if ($failedPercent !== null) {
            $description .= ' · '.$failedPercent.'% '.__('general.failed_threshold');
        }

        return $description;
    }

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '250px';

    protected ?string $pollingInterval = '60s';

    protected ?Collection $results = null;

    protected function getResults(): Collection
    {
        return $this->results ??= Result::query()
            ->select(['id', 'download', 'benchmarks', 'created_at'])
            ->whereBetween('created_at', [$this->pageFilters['startDate'], $this->pageFilters['endDate']])
            ->orderBy('created_at')
            ->get();
    }

    protected function getData(): array
    {
        $results = $this->getResults();

        return [
            'datasets' => [
                [
                    'label' => __('general.download_mbps'),
                    'data' => $results->map(fn ($item) => ! blank($item->download) ? Number::bitsToMagnitude(bits: $item->download_bits, precision: 2, magnitude: 'mbit') : null),
                    'borderColor' => 'rgba(14, 165, 233)',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                    'pointBackgroundColor' => 'rgba(14, 165, 233)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => count($results) <= 24 ? 3 : 0,
                ],
            ],
            'labels' => $results->map(fn ($item) => $item->created_at->timezone(config('app.display_timezone'))->format(app(GeneralSettings::class)->chart_datetime_format)),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,

                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                    'position' => 'nearest',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => app(GeneralSettings::class)->chart_begin_at_zero,
                    'grace' => 2,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
