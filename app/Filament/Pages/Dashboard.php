<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentDownloadChartWidget;
use App\Filament\Widgets\RecentDownloadLatencyChartWidget;
use App\Filament\Widgets\RecentJitterChartWidget;
use App\Filament\Widgets\RecentPingChartWidget;
use App\Filament\Widgets\RecentUploadChartWidget;
use App\Filament\Widgets\RecentUploadLatencyChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Forms\Components\DateFilterForm;
use App\Settings\GeneralSettings;
use Carbon\Carbon;
use Cron\CronExpression;
use Filament\Pages\Dashboard as BasePage;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

class Dashboard extends BasePage
{
    use HasFiltersForm;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-layout-dashboard';

    public function persistsFiltersInSession(): bool
    {
        return false;
    }

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

        $settings = app(GeneralSettings::class);

        $nextRunDate = Carbon::parse($cronExpression->getNextRunDate(timeZone: $settings->display_timezone))->format($settings->datetime_format);

        return __('dashboard.next_speedtest_at').': '.$nextRunDate;
    }

    public function filtersForm(Schema $schema): Schema
    {
        return DateFilterForm::make($schema);
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
            RecentDownloadChartWidget::make(),
            RecentUploadChartWidget::make(),
            RecentPingChartWidget::make(),
            RecentJitterChartWidget::make(),
            RecentDownloadLatencyChartWidget::make(),
            RecentUploadLatencyChartWidget::make(),
        ];
    }
}
