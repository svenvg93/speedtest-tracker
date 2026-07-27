<?php

namespace App\Filament\Widgets;

use App\Helpers\Average;
use App\Helpers\Benchmark;
use App\Models\Result;
use App\Settings\GeneralSettings;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Collection;

class RecentPingChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected ?string $heading = null;

    public function getHeading(): ?string
    {
        return __('general.ping');
    }

    public function getDescription(): ?string
    {
        $results = $this->getResults();
        $settings = app(GeneralSettings::class);

        $segments = [];

        if ($settings->chart_show_average) {
            $segments[] = __('general.average').': '.number_format(Average::averagePing($results), 2).' '.__('general.ms');
        }

        if ($settings->chart_show_failed_threshold) {
            $failedPercent = Benchmark::failedPercentage($results, 'ping');

            if ($failedPercent !== null) {
                $segments[] = $failedPercent.'% '.__('general.failed_threshold');
            }
        }

        return $segments !== [] ? implode(' · ', $segments) : null;
    }

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '250px';

    protected ?string $pollingInterval = '60s';

    protected ?Collection $results = null;

    protected function getResults(): Collection
    {
        return $this->results ??= Result::query()
            ->select(['id', 'ping', 'benchmarks', 'created_at'])
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
                    'label' => __('general.ping_ms'),
                    'data' => $results->map(fn ($item) => ! blank($item->ping) ? round($item->ping, 2) : null),
                    'borderColor' => 'rgba(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'pointBackgroundColor' => 'rgba(16, 185, 129)',
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
