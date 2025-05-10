<?php

namespace App\Filament\Resources\ResultResource\Widgets;

use App\Enums\ResultStatus;
use App\Models\Result;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ResultStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $counts = Result::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Calculate statistics
        $completed = $counts[ResultStatus::Completed->value] ?? 0;
        $failed = $counts[ResultStatus::Failed->value] ?? 0;
        $total = array_sum($counts);
        $other = $total - ($completed + $failed);

        return [
            Stat::make('All Tests', $total)
                ->description('Total number of test attempts recorded'),

            Stat::make('In Progress', $other)
                ->description("{$other} tests ({$this->getPercentage($other, $total)}%) are not yet completed")
                ->color('info'),

            Stat::make('Successful', $completed)
                ->description("{$completed} tests ({$this->getPercentage($completed, $total)}%) completed successfully")
                ->color('success'),

            Stat::make('Failed', $failed)
                ->description("{$failed} tests ({$this->getPercentage($failed, $total)}%) failed to complete")
                ->color('danger'),
        ];
    }

    protected function getPercentage(int $value, int $total): float
    {
        return $total > 0 ? round(($value / $total) * 100, 1) : 0;
    }
}
