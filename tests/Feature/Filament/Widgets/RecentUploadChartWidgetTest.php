<?php

use App\Enums\UserRole;
use App\Filament\Widgets\RecentUploadChartWidget;
use App\Models\Result;
use App\Models\User;
use App\Settings\ThresholdSettings;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->pageFilters = [
        'startDate' => now()->subDay(),
        'endDate' => now(),
    ];
});

it('does not include a threshold dataset when absolute thresholds are disabled', function () {
    Result::factory()->count(3)->create(['upload' => 100000000]);

    tap(app(ThresholdSettings::class), function (ThresholdSettings $settings) {
        $settings->absolute_enabled = false;
        $settings->save();
    });

    actingAs($this->admin);

    $component = Livewire::test(RecentUploadChartWidget::class, ['pageFilters' => $this->pageFilters])->instance();

    $data = invade($component)->getData();

    expect(collect($data['datasets'])->pluck('label'))->not->toContain(__('general.threshold'));
});

it('includes a flat threshold dataset matching the configured absolute upload threshold', function () {
    Result::factory()->count(3)->create(['upload' => 100000000]);

    tap(app(ThresholdSettings::class), function (ThresholdSettings $settings) {
        $settings->absolute_enabled = true;
        $settings->absolute_upload = 20.0;
        $settings->save();
    });

    actingAs($this->admin);

    $component = Livewire::test(RecentUploadChartWidget::class, ['pageFilters' => $this->pageFilters])->instance();

    $data = invade($component)->getData();

    $threshold = collect($data['datasets'])->firstWhere('label', __('general.threshold'));

    expect($threshold)->not->toBeNull()
        ->and($threshold['data'])->toEqual(array_fill(0, 3, 20.0));
});
