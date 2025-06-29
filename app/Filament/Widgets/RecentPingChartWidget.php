<?php

namespace App\Filament\Widgets;

use App\Models\Result;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class RecentPingChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Ping / Jitter';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '250px';

    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        // Ensure that startDate and endDate are treated as Carbon instances
        $startDate = $this->filters['startDate'] ?? now()->subWeek();
        $endDate = $this->filters['endDate'] ?? now();

        // Convert dates to the correct timezone without resetting the time
        $startDate = Carbon::parse($startDate)->timezone(config('app.timezone'));
        $endDate = Carbon::parse($endDate)->timezone(config('app.timezone'));

        $results = Result::query()
            ->select(['id', 'data', 'ping', 'created_at'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($this->filters['server'] ?? null, function ($query, $serverName) {
                $query->where('data->server->name', $serverName);
            })
            ->orderBy('created_at')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Ping (ms)',
                    'data' => $results->map(fn ($item) => is_numeric($item->ping) ? round($item->ping, 2) : null),
                    'borderColor' => 'rgba(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'pointBackgroundColor' => 'rgba(16, 185, 129)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => count($results) <= 24 ? 3 : 0,
                ],
                [
                    'label' => 'Jitter (ms)',
                    'data' => $results->map(fn ($item) => is_numeric($item->ping_jitter) ? round($item->ping_jitter, 2) : null),
                    'borderColor' => 'rgb(139, 92, 246)',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'pointBackgroundColor' => 'rgb(139, 92, 246)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => 0,
                    'pointRadius' => count($results) <= 24 ? 3 : 0,
                ],
            ],
            'labels' => $results->map(fn ($item) => $item->created_at->timezone(config('app.display_timezone'))->format(config('app.chart_datetime_format'))),
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
                    'beginAtZero' => config('app.chart_begin_at_zero'),
                    'title' => [
                        'display' => true,
                        'text' => 'ms',
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
