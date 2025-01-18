<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationTemplateResource\Pages;
use App\Models\NotificationTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;


class NotificationTemplateResource extends Resource
{
    protected static ?string $model = NotificationTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Notifications';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Templates';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->readonly()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                            
                        TextInput::make('description')
                            ->readonly()
                            ->maxLength(255)
                            ->columnSpanFull(),
                            
                        MarkdownEditor::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->helperText(new HtmlString('
                                Available variables:<br>
                                <code>{{ $id }}</code> - Result ID<br>
                                <code>{{ $service }}</code> - Service name<br>
                                <code>{{ $serverName }}</code> - Server name<br>
                                <code>{{ $serverId }}</code> - Server ID<br>
                                <code>{{ $isp }}</code> - ISP name<br>
                                <code>{{ $ping }}</code> - Ping result<br>
                                <code>{{ $download }}</code> - Download speed<br>
                                <code>{{ $upload }}</code> - Upload speed<br>
                                <code>{{ $packetLoss }}</code> - Packet loss percentage<br>
                                <code>{{ $speedtest_url }}</code> - Speedtest URL<br>
                                <code>{{ $url }}</code> - App URL
                            '))
                    ])
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Updated'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationTemplates::route('/'),
            'edit' => Pages\EditNotificationTemplate::route('/{record}/edit'),
        ];
    }
}
