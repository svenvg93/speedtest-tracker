<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentDownloadChartWidget;
use App\Filament\Widgets\RecentDownloadLatencyChartWidget;
use App\Filament\Widgets\RecentJitterChartWidget;
use App\Filament\Widgets\RecentPingChartWidget;
use App\Filament\Widgets\RecentUploadChartWidget;
use App\Filament\Widgets\RecentUploadLatencyChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\RecentDownloadUploadChartWidget;
use App\Filament\Widgets\RecentLatencyChartWidget;
use App\Filament\Widgets\AverageStatsWidget;
use App\Forms\Components\ChartFilter;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Cron\CronExpression;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Support\Enums\IconPosition;


class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public function getSubheading(): ?string
    {
        $schedule = config('speedtest.schedule');

        if (blank($schedule) || $schedule === false) {
            return __('No speedtests scheduled.');
        }

        $cronExpression = new CronExpression($schedule);

        $nextRunDate = Carbon::parse($cronExpression->getNextRunDate(timeZone: config('app.display_timezone')))->format(config('app.datetime_format'));

        return 'Next speedtest at: '.$nextRunDate;
    }

    public function filtersForm(Form $form): Form
    {
        return ChartFilter::make($form);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::make(),
        ];
    }

    public function getWidgets(): array
    {
        return [
            AverageStatsWidget::make(),
            RecentDownloadUploadChartWidget::make(),
            RecentPingChartWidget::make(),
            RecentLatencyChartWidget::make(),
        ];
    }
}
