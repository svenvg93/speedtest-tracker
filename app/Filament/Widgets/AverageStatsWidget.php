<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Helpers\Average;
use App\Helpers\Threshold;
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

        $downloadEval = Threshold::evaluateMetric($averageDownload, 'download');
        $uploadEval = Threshold::evaluateMetric($averageUpload, 'upload');
        $pingEval = Threshold::evaluateMetric($averagePing, 'ping');

        $cards = [
            Stat::make('Average Download', $averageDownload !== null
                    ? number_format($averageDownload, 2).' Mbps'
                    : 'n/a'
            )
                ->description($downloadEval['description'])
                ->descriptionIcon(
                    $downloadEval['isPositive']
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->color($downloadEval['isPositive'] ? 'success' : 'danger')
                ->icon('heroicon-o-arrow-down-tray'),

            Stat::make('Average Upload', $averageUpload !== null
                    ? number_format($averageUpload, 2).' Mbps'
                    : 'n/a'
            )
                ->description($uploadEval['description'])
                ->descriptionIcon(
                    $uploadEval['isPositive']
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->color($uploadEval['isPositive'] ? 'success' : 'danger')
                ->icon('heroicon-o-arrow-up-tray'),

            Stat::make('Average Ping', $averagePing !== null
                    ? number_format($averagePing, 2).' ms'
                    : 'n/a'
            )
                ->description($pingEval['description'])
                ->descriptionIcon(
                    $pingEval['isPositive']
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->color($pingEval['isPositive'] ? 'success' : 'danger')
                ->icon('heroicon-o-clock'),
        ];

        // Add Threshold Breached Card based on 'healthy' status
        $breachPercentage = Threshold::getBreachedTestPercentage($results);

        if (! is_null($breachPercentage)) {
            $formatted = number_format($breachPercentage, 1);

            $cards[] = Stat::make('Failed Benchmarks %', "{$formatted}%")
                ->description('of benchmarked tests failed')
                ->color($breachPercentage > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-circle');
        }

        return $cards;
    }
}
