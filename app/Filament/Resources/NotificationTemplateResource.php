<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationTemplateResource\Pages;
use App\Models\NotificationTemplate;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

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
                Grid::make(2)
                    ->schema([
                        Section::make('Template Details')
                            ->schema([
                                TextInput::make('name')
                                    ->readonly()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('description')
                                    ->readonly()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Fieldset::make('Notification Content')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Notification Title')
                                            ->maxLength(255)
                                            ->required()
                                            ->helperText('You can use placeholders like id, serverName, etc.'),

                                        MarkdownEditor::make('content')
                                            ->required()
                                            ->columnSpanFull()
                                            ->toolbarButtons([
                                                'bold',
                                                'bulletList',
                                                'italic',
                                                'redo',
                                                'undo',
                                            ])
                                            ->hint(new HtmlString('Refer to the markdown syntax guide: <a href="https://your-docs-url.com" target="_blank">Markdown Guide</a>')),
                                    ]),
                            ])
                            ->columnSpan(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
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
        ];
    }
}
