<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Helpers\Average;
use App\Models\Result;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AverageStatsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '60s';

    protected function getCards(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->subWeek();
        $endDate = $this->filters['endDate'] ?? now();
        $selectedServers = $this->filters['server'] ?? [];

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $query = Result::query()
            ->where('status', ResultStatus::Completed)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when(! empty($selectedServers), function ($query) use ($selectedServers) {
                $query->where('data->server->name', $selectedServers);
            });

        $results = $query->get();

        $averageDownload = Average::averageDownload($results);
        $averageUpload = Average::averageUpload($results);
        $averagePing = Average::averagePing($results);

        $cards = [
            Stat::make('Download Speed (Average)', $averageDownload !== null
                ? number_format($averageDownload, 2).' Mbps'
                : 'n/a'
            ),
            Stat::make('Upload Speed (Average)', $averageUpload !== null
                ? number_format($averageUpload, 2).' Mbps'
                : 'n/a'
            ),
            Stat::make('Ping Time (Average)', $averagePing !== null
                ? number_format($averagePing, 2).' ms'
                : 'n/a'
            ),
        ];

        // Add Threshold Breached Card based on 'healthy' status
        $validResults = $results->whereNotNull('healthy');

        $totalResults = $validResults->count();
        $failedResults = $validResults->where('healthy', false)->count();

        if ($totalResults > 0) {
            $percentageBreached = ($failedResults / $totalResults) * 100;
            $formattedPercentage = number_format($percentageBreached, 1);

            $cards[] = Stat::make('Threshold Breached Tests', $formattedPercentage.'%')
                ->description($formattedPercentage.'% of tests breached')
                ->color($percentageBreached > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-circle');
        }

        return $cards;
    }
}
