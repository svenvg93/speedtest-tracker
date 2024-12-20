<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationSettingResource\Pages;
use App\Models\NotificationSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationSettingResource extends Resource
{
    protected static ?string $model = NotificationSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Notifications';

    protected static ?string $navigationLabel = 'Notifications';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2) // Main grid to place sections side by side
                    ->schema([
                        // Notification Section (Left side of the grid)
                        Forms\Components\Section::make('Notification')
                            ->description('Configure notification preferences.')
                            ->schema([
                                Forms\Components\Grid::make(2) // Inner grid for placing Radio, Card, and Apprise fields side by side
                                    ->schema([
                                        // Radio Component
                                        Forms\Components\Radio::make('type')
                                            ->label('Select Notification Channel')
                                            ->options([
                                                'database' => 'Database',
                                                'apprise' => 'Apprise',
                                            ])
                                            ->default(
                                                NotificationSetting::where('type', 'database')->exists() ? 'apprise' : 'database'
                                            )
                                            ->required()
                                            ->reactive()
                                            ->disableOptionWhen(fn (string $operation): bool => $operation === 'create' &&
                                                NotificationSetting::where('type', 'database')->exists()
                                            )
                                            ->dehydrated()
                                            ->columnSpan(1), // Take up one column

                                        // Card Component
                                        Forms\Components\Card::make()
                                            ->schema([
                                                Forms\Components\View::make('components.info-box')
                                                    ->view('filament.forms.components.info-alert')
                                                    ->viewData([
                                                        'content' => 'Database notifications will be stored locally and can be viewed in the notifications panel.',
                                                        'icon' => 'heroicon-o-information-circle',
                                                    ])
                                                    ->visible(fn (Forms\Get $get) => $get('type') === 'database'),

                                                Forms\Components\View::make('components.info-box')
                                                    ->view('filament.forms.components.info-alert')
                                                    ->viewData([
                                                        'content' => 'Apprise allows you to send notifications to multiple services including Discord, Slack, and Telegram.',
                                                        'icon' => 'heroicon-o-information-circle',
                                                    ])
                                                    ->visible(fn (Forms\Get $get) => $get('type') === 'apprise'),
                                            ])
                                            ->columnSpan(1), // Take up one column

                                        Forms\Components\Toggle::make('every_run')
                                            ->label('Notify on every speedtest run')
                                            ->default(false),

                                        Forms\Components\Toggle::make('threshold')
                                            ->label('Notify on threshold failures')
                                            ->default(false),

                                        // Apprise Configuration Fields (Next to Radio and Card)
                                        Forms\Components\Grid::make(2) // Inner grid for the Apprise configuration fields
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Notification Name')
                                                    ->placeholder('Discord')
                                                    ->required(fn (Forms\Get $get) => $get('type') === 'apprise')
                                                    ->visible(fn (Forms\Get $get) => $get('type') === 'apprise'),

                                                Forms\Components\TextInput::make('apprise_url')
                                                    ->label('Apprise URL')
                                                    ->placeholder('Enter the webhook URL for Apprise')
                                                    ->required(fn (Forms\Get $get) => $get('type') === 'apprise')
                                                    ->visible(fn (Forms\Get $get) => $get('type') === 'apprise'),

                                                Forms\Components\TextInput::make('apprise_service_url')
                                                    ->label('Apprise Service URL')
                                                    ->placeholder('Enter the service URL for Apprise')
                                                    ->required(fn (Forms\Get $get) => $get('type') === 'apprise')
                                                    ->visible(fn (Forms\Get $get) => $get('type') === 'apprise'),

                                                Forms\Components\TextInput::make('apprise_service_tag')
                                                    ->label('Apprise Service Tag')
                                                    ->placeholder('Enter the service tag for Apprise')
                                                    ->required(fn (Forms\Get $get) => $get('type') === 'apprise')
                                                    ->visible(fn (Forms\Get $get) => $get('type') === 'apprise'),
                                            ])
                                            ->columnSpan(2), // Take up both columns for the Apprise fields
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\IconColumn::make('every_run')
                    ->label('Notify Every Run')
                    ->toggleable()
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('threshold')
                    ->label('Notify on Threshold')
                    ->toggleable()
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('apprise_webhook_url')
                    ->label('Apprise URL')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->limit(30),
                Tables\Columns\TextColumn::make('apprise_service_url')
                    ->label('Service URL')
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->limit(30),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modal(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationSettings::route('/'),
            'create' => Pages\CreateNotificationSetting::route('/create'),
            'edit' => Pages\EditNotificationSetting::route('/{record}/edit'),
        ];
    }
}
