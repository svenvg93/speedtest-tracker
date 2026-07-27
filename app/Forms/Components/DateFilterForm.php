<?php

namespace App\Forms\Components;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DateFilterForm
{
    public static function make(Schema $schema): Schema
    {
        $defaultEndDate = now();

        $defaultStartDate = match (app(GeneralSettings::class)->default_chart_range) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            default => now()->subDay(),
        };

        return $schema
            ->components([
                Section::make()
                    ->schema([
                        DateTimePicker::make('startDate')
                            ->label('Start Date')
                            ->default($defaultStartDate)
                            ->reactive()
                            ->seconds(false)
                            ->native(false),
                        DateTimePicker::make('endDate')
                            ->label('End Date')
                            ->default($defaultEndDate)
                            ->reactive()
                            ->seconds(false)
                            ->native(false),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
