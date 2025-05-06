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
                ->description('Recorded in the system'),

            Stat::make('In Progress', $other)
                ->description("{$this->getPercentage($other, $total)}% of tests currently running")
                ->color('info'),

            Stat::make('Successful', $completed)
                ->description("{$this->getPercentage($completed, $total)}% completed without issues")
                ->color('success'),

            Stat::make('Failed', $failed)
                ->description("{$this->getPercentage($failed, $total)}% encountered errors")
                ->color('danger'),
        ];
    }

    protected function getPercentage(int $value, int $total): float
    {
        return $total > 0 ? round(($value / $total) * 100, 1) : 0;
    }
}
