<?php

namespace App\Filament\Resources\Bimrevo\NewsPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class NewsPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (blank($get('slug')) && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        Textarea::make('excerpt')
                            ->label('Short summary')
                            ->rows(2)
                            ->maxLength(300),
                        RichEditor::make('content')
                            ->label('Content')
                            ->required()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('bimrevo/news/content')
                            ->fileAttachmentsVisibility('public'),
                    ]),
                Section::make('Publication')
                    ->columns(2)
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->helperText('Used in the article URL. Generated from the title.'),
                        TextInput::make('author')
                            ->required()
                            ->maxLength(255)
                            ->default('Équipe BIMREVO'),
                        DateTimePicker::make('published_at')
                            ->label('Publication date')
                            ->default(now())
                            ->seconds(false)
                            ->required(),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->helperText('Only published news with a date in the past appear on the website.')
                            ->default(true),
                    ]),
                Section::make('Image')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('bimrevo/news')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(4096),
                        TextInput::make('image_alt')
                            ->label('Image description (alt text)'),
                    ]),
            ]);
    }
}
