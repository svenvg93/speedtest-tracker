<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;

class SpeedtestServers extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-server';

    protected static string $view = 'filament.pages.speedtest-servers';

    protected static ?string $title = 'Speedtest Server List';

    protected static ?string $navigationGroup = 'Extra';

    public ?string $jsonContent = '';

    public function getSubheading(): string
    {
        return 'This list is generated daily, and can be uploaded to the Speedtest Tracker Server list application';
    }

    public function mount(): void
    {
        $this->loadSpeedtestData();
    }

    public function loadSpeedtestData(): void
    {
        $filePath = storage_path('app/private/speedtest_servers.json');  // Update to 'private' directory

        if (file_exists($filePath)) {
            $this->jsonContent = file_get_contents($filePath);
        } else {
            $this->jsonContent = 'Speedtest servers file not found.';
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Textarea::make('jsonContent')
                ->label('Speedtest Servers JSON')
                ->rows(20)
                ->disabled(),
        ];
    }
}
