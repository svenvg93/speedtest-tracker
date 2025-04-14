<?php

namespace App\Forms\Components;

use App\Models\Result;
use App\Enums\ResultStatus;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;

class ChartFilter
{
    public static function make(Form $form): Form
    {
        $defaultRangeDays = config('app.chart_default_date_range');

        // Calculate the start and end dates based on the configuration value
        $defaultEndDate = now(); // Today
        $defaultStartDate = now()->subDays($defaultRangeDays); // Start date for the range

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        DateTimePicker::make('startDate')
                            ->label('Start Date')
                            ->default($defaultStartDate->startOfDay())
                            ->reactive()
                            ->seconds(false)
                            ->native(false),
                        DateTimePicker::make('endDate')
                            ->label('End Date')
                            ->default($defaultEndDate->endOfDay())
                            ->reactive()
                            ->seconds(false)
                            ->native(false),
                        Select::make('server')
                            ->label('Server')
                            ->options(function () {
                                $serverOptions = Result::query()
                                    ->pluck('data')
                                    ->map(fn ($data) => $data['server']['name'] ?? null)
                                    ->filter()
                                    ->unique()
                                    ->sort()
                                    ->mapWithKeys(fn ($name) => [$name => $name])
                                    ->toArray();
                        
                                return ['' => 'All Servers'] + $serverOptions;
                            })
                            ->searchable()
                            ->placeholder('All Servers')
                            ->native(false)
                            ->reactive(),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 3,
                    ]),
            ]);
    }
}
