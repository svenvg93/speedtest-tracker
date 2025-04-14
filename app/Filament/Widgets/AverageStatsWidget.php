<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Helpers\Average;
use App\Helpers\Threshold; // Import the new helper
use App\Models\Result;
use App\Settings\ThresholdSettings;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class AverageStatsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '60s';

    protected function getCards(): array
    {
        // Use filter data from the page
        $startDate = $this->filters['startDate'] ?? now()->subWeek();
        $endDate = $this->filters['endDate'] ?? now();
        $selectedServers = $this->filters['server'] ?? [];

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $query = Result::query()
            ->where('status', ResultStatus::Completed)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when(!empty($selectedServers), function ($query) use ($selectedServers) {
                $query->where('data->server->name', $selectedServers);
            });

        $results = $query->get();
        
        $averageDownload = Average::averageDownload($results);
        $averageUpload = Average::averageUpload($results);
        $averagePing = Average::averagePing($results);
        
        // Get thresholds
        $thresholds = app(ThresholdSettings::class);
        
        // Calculate percentages from thresholds if they exist and if absolute_enabled is true
        $downloadPercentage = null;
        $uploadPercentage = null;
        $pingPercentage = null;
        
        if ($thresholds->absolute_enabled) {
            if ($thresholds->absolute_download && $averageDownload) {
                $downloadPercentage = Threshold::calculatePercentage($averageDownload, $thresholds->absolute_download);
            }
            
            if ($thresholds->absolute_upload && $averageUpload) {
                $uploadPercentage = Threshold::calculatePercentage($averageUpload, $thresholds->absolute_upload);
            }
            
            if ($thresholds->absolute_ping && $averagePing) {
                $pingPercentage = Threshold::calculatePercentage($averagePing, $thresholds->absolute_ping, true);
            }
        }
        
        // Format the descriptions with percentages
        $downloadDescription = $downloadPercentage !== null 
            ? ($downloadPercentage >= 0 
                ? number_format(abs($downloadPercentage), 1) . '% above threshold' 
                : number_format(abs($downloadPercentage), 1) . '% below threshold')
            : null;
            
        $uploadDescription = $uploadPercentage !== null 
            ? ($uploadPercentage >= 0 
                ? number_format(abs($uploadPercentage), 1) . '% above threshold' 
                : number_format(abs($uploadPercentage), 1) . '% below threshold')
            : null;
            
        $pingDescription = $pingPercentage !== null 
            ? ($pingPercentage >= 0 
                ? number_format(abs($pingPercentage), 1) . '% better than threshold' 
                : number_format(abs($pingPercentage), 1) . '% worse than threshold')
            : null;

        return [
            Stat::make('Avg Download', $averageDownload !== null
                ? number_format($averageDownload, 2) . ' Mbps'
                : 'n/a'
            )
                ->description($downloadDescription)
                ->descriptionIcon($downloadPercentage > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($downloadPercentage > 0 ? 'success' : ($downloadPercentage < 0 ? 'danger' : 'primary'))
                ->icon('heroicon-o-arrow-down-tray'),

            Stat::make('Avg Upload', $averageUpload !== null
                ? number_format($averageUpload, 2) . ' Mbps'
                : 'n/a'
            )
                ->description($uploadDescription)
                ->descriptionIcon($uploadPercentage > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($uploadPercentage > 0 ? 'success' : ($uploadPercentage < 0 ? 'danger' : 'primary'))
                ->icon('heroicon-o-arrow-up-tray'),

            Stat::make('Avg Ping', $averagePing !== null
                ? number_format($averagePing, 2) . ' ms'
                : 'n/a'
            )
                ->description($pingDescription)
                ->descriptionIcon($pingPercentage > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pingPercentage > 0 ? 'success' : ($pingPercentage < 0 ? 'danger' : 'primary'))
                ->icon('heroicon-o-clock'),
        ];
    }
}
