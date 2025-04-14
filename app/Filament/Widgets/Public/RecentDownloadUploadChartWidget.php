<?php

namespace App\Filament\Widgets\Public;

use App\Enums\ResultStatus;
use App\Helpers\Average;
use App\Helpers\Number;
use App\Models\Result;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RecentDownloadUploadChartWidget extends ChartWidget
{

    protected static ?string $heading = 'Download / Upload';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '250px';

    protected static ?string $pollingInterval = '60s';

    public ?string $filter = 'week';

    protected function getFilters(): ?array
    {
        return [
            '24h' => 'Last 24h',
            'week' => 'Last week',
            'month' => 'Last month',
        ];
    }

    protected function getData(): array
    {
        $results = Result::query()
            ->select(['id', 'download', 'upload', 'created_at'])
            ->when($this->filter == '24h', function ($query) {
                $query->where('created_at', '>=', now()->subDay());
            })
            ->when($this->filter == 'week', function ($query) {
                $query->where('created_at', '>=', now()->subWeek());
            })
            ->when($this->filter == 'month', function ($query) {
                $query->where('created_at', '>=', now()->subMonth());
            })
            ->orderBy('created_at')
            ->get();
    
        return [
            'datasets' => [
                [
                    'label' => 'Download',
                    'data' => $results->map(fn ($item) =>
                        !blank($item->download) ? Number::bitsToMagnitude(bits: $item->download_bits, precision: 2, magnitude: 'mbit') : null
                    ),
                    'borderColor' => 'rgba(14, 165, 233)',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                    'pointBackgroundColor' => 'rgba(14, 165, 233)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => count($results) <= 24 ? 3 : 0,
                ],
                [
                    'label' => 'Upload',
                    'data' => $results->map(fn ($item) =>
                        !blank($item->upload) ? Number::bitsToMagnitude(bits: $item->upload_bits, precision: 2, magnitude: 'mbit') : null
                    ),
                    'borderColor' => 'rgba(139, 92, 246)',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'pointBackgroundColor' => 'rgba(139, 92, 246)',
                    'fill' => true,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => count($results) <= 24 ? 3 : 0,
                ],
            ],
            'labels' => $results->map(fn ($item) =>
                $item->created_at->timezone(config('app.display_timezone'))->format(config('app.chart_datetime_format'))
            ),
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
                        'text' => 'Mbps',
                    ],
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
