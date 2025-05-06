<?php

namespace App\Filament\Resources\ResultResource\Widgets;

use App\Enums\ResultStatus;
use App\Models\Result;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ResultStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total = Result::count();
        $completed = Result::where('status', ResultStatus::Completed)->count();
        $failed = Result::where('status', ResultStatus::Failed)->count();

        $completedPercentage = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
        $failedPercentage = $total > 0 ? round(($failed / $total) * 100, 1) : 0;

        return [
            Stat::make('Total Tests', $total)
                ->description('All recorded tests'),

            Stat::make('Completed Tests', $completed)
                ->description("{$completedPercentage}% of total")
                ->color('success'),

            Stat::make('Failed Tests', $failed)
                ->description("{$failedPercentage}% of total")
                ->color('danger'),
        ];
    }
}
