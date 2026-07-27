<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentDownloadChartWidget;
use App\Filament\Widgets\RecentDownloadLatencyChartWidget;
use App\Filament\Widgets\RecentJitterChartWidget;
use App\Filament\Widgets\RecentPingChartWidget;
use App\Filament\Widgets\RecentUploadChartWidget;
use App\Filament\Widgets\RecentUploadLatencyChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Carbon\Carbon;
use Cron\CronExpression;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'tabler-layout-dashboard';

    public function getTitle(): string
    {
        return __('dashboard.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.title');
    }

    public function getSubheading(): ?string
    {
        $schedule = config('speedtest.schedule');

        if (blank($schedule) || $schedule === false) {
            return __('dashboard.no_speedtests_scheduled');
        }

        $cronExpression = new CronExpression($schedule);

        $nextRunDate = Carbon::parse($cronExpression->getNextRunDate(timeZone: config('app.display_timezone')))->format(config('app.datetime_format'));

        return __('dashboard.next_speedtest_at').': '.$nextRunDate;
    }

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            RecentDownloadChartWidget::class,
            RecentUploadChartWidget::class,
            RecentPingChartWidget::class,
            RecentJitterChartWidget::class,
            RecentDownloadLatencyChartWidget::class,
            RecentUploadLatencyChartWidget::class,
        ];
    }
}
