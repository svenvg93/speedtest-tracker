<?php

namespace App\Filament\Resources;

use App\Actions\Notifications\SendAppriseTestNotification;
use App\Actions\Notifications\SendDatabaseTestNotification;
use App\Actions\Notifications\SendMailTestNotification;
use App\Actions\Notifications\SendWebhookTestNotification;
use App\Filament\Resources\NotificationChannelResource\Pages;
use App\Models\NotificationChannel;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class NotificationChannelResource extends Resource
{
    protected static ?string $model = NotificationChannel::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $label = 'Notification Channel';

    protected static ?string $navigationLabel = 'Notification Channels';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('type')
                                ->label('Channel Type')
                                ->options(self::getChannelTypes())
                                ->required()
                                ->reactive()
                                ->searchable()
                                ->preload()
                                ->disabledOn('edit'),

                            Toggle::make('enabled')
                                ->label('Enabled')
                                ->inline(false),

                            TextInput::make('description')
                                ->label('Description'),
                        ]),

                    Fieldset::make('Options')
                        ->schema([
                            Toggle::make('on_speedtest_run')
                                ->label('Notify on Speedtest Run'),

                            Toggle::make('on_threshold_failure')
                                ->label('Notify on Threshold Failure'),
                        ]),
                ]),

            Forms\Components\Section::make('Database')
                ->description('Notifications sent to this channel will show up under the 🔔 icon in the header.')
                ->schema([
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('test_database')
                            ->label('Test Database Channel')
                            ->action(fn () => SendDatabaseTestNotification::run(user: auth()->user())),
                    ]),
                ])
                ->hidden(fn (Forms\Get $get) => $get('type') !== 'Database'),

            Forms\Components\Section::make('Mail')
                ->description('Send email notifications to one or more recipients.')
                ->schema([
                    Repeater::make('mail_recipients')
                        ->label('Mail Recipients')
                        ->schema([
                            TextInput::make('email_address')
                                ->email()
                                ->required()
                                ->maxLength(255),
                        ])
                        ->statePath('config.mail_recipients')
                        ->columnSpanFull(),

                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('test_mail')
                            ->label('Test Mail Channel')
                            ->action(fn (Forms\Get $get) => SendMailTestNotification::run(
                                recipients: $get('config.mail_recipients') ?? []
                            )
                            )
                            ->hidden(fn (Forms\Get $get) => empty($get('config.mail_recipients'))),
                    ]),
                ])
                ->hidden(fn (Forms\Get $get) => $get('type') !== 'Mail'),

            Forms\Components\Section::make('Webhook')
                ->description('Sends a POST request to the listed URLs.')
                ->schema([
                    Repeater::make('webhook_urls')
                        ->label('Webhook URLs')
                        ->schema([
                            TextInput::make('url')
                                ->url()
                                ->required()
                                ->maxLength(2000),
                        ])
                        ->statePath('config.webhook_urls')
                        ->columnSpanFull(),

                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('test_webhook')
                            ->label('Test Webhook Channel')
                            ->action(fn (Forms\Get $get) => SendWebhookTestNotification::run(
                                webhooks: $get('config.webhook_urls') ?? []
                            )
                            )
                            ->hidden(fn (Forms\Get $get) => empty($get('config.webhook_urls'))),
                    ]),
                ])
                ->hidden(fn (Forms\Get $get) => $get('type') !== 'Webhook'),

            Forms\Components\Section::make('Apprise')
                ->description('The Apprise Notification Library enables sending notifications to a wide range of services.')
                ->schema([
                    Repeater::make('apprise_webhooks')
                        ->label('Apprise Webhooks')
                        ->hint(new HtmlString('<a href="https://github.com/caronc/apprise-api" target="_blank">Apprise Documentation</a>'))
                        ->schema([
                            TextInput::make('url')
                                ->label('Apprise URL')
                                ->placeholder('http://apprise:8000/notify')
                                ->url()
                                ->required()
                                ->maxLength(2000),

                            TextInput::make('service_url')
                                ->label('Service URL')
                                ->placeholder('discord://WebhookID/WebhookToken')
                                ->required()
                                ->maxLength(200),
                        ])
                        ->statePath('config.apprise_webhooks')
                        ->columnSpanFull(),

                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('test_apprise')
                            ->label('Test Apprise Channel')
                            ->action(fn (Forms\Get $get) => SendAppriseTestNotification::run(
                                webhooks: $get('config.apprise_webhooks') ?? []
                            )
                            )
                            ->hidden(fn (Forms\Get $get) => empty($get('config.apprise_webhooks'))),
                    ]),
                ])
                ->hidden(fn (Forms\Get $get) => $get('type') !== 'Apprise'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Channel')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description'),
                BooleanColumn::make('enabled')
                    ->label('Enabled'),
                BooleanColumn::make('on_speedtest_run')
                    ->label('Speedtest Run'),
                BooleanColumn::make('on_threshold_failure')
                    ->label('Threshold Failure'),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime(),
            ])
            ->filters([
                TernaryFilter::make('enabled')
                    ->label('Enabled')
                    ->nullable()
                    ->native()
                    ->trueLabel('Only enabled')
                    ->falseLabel('Only disabled')
                    ->queries(
                        true: fn (Builder $query) => $query->where('enabled', true),
                        false: fn (Builder $query) => $query->where('enabled', false),
                        blank: fn (Builder $query) => $query,
                    ),

                TernaryFilter::make('on_speedtest_run')
                    ->label('Speedtest Run Trigger')
                    ->native()
                    ->trueLabel('Only with speedtest run trigger')
                    ->falseLabel('Without speedtest run trigger')
                    ->queries(
                        true: fn (Builder $query) => $query->where('on_speedtest_run', true),
                        false: fn (Builder $query) => $query->where('on_speedtest_run', false),
                        blank: fn (Builder $query) => $query,
                    ),

                TernaryFilter::make('on_threshold_failure')
                    ->label('Threshold Failure Trigger')
                    ->nullable()
                    ->native()
                    ->trueLabel('Only with threshold trigger')
                    ->falseLabel('Without threshold trigger')
                    ->queries(
                        true: fn (Builder $query) => $query->where('on_threshold_failure', true),
                        false: fn (Builder $query) => $query->where('on_threshold_failure', false),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil'),
                    Action::make('toggleEnabled')
                        ->label(fn ($record) => $record->enabled ? 'Disable Notification' : 'Enable Notification')
                        ->icon(fn ($record) => $record->enabled ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn ($record) => $record->enabled ? 'danger' : 'success')
                        ->action(function ($record) {
                            $record->update(['enabled' => ! $record->enabled]);
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function getChannelTypes(): array
    {
        return [
            'Database' => 'Database',
            'Mail' => 'Mail',
            'Webhook' => 'Webhook',
            'Apprise' => 'Apprise',
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationChannels::route('/'),
        ];
    }
}
