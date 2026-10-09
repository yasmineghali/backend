<?php

namespace App\Filament\Resources\NewsPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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
                Tabs::make('Content')
                    ->columnSpanFull()
                    ->tabs([
                        self::languageTab('Français', 'fr', required: true),
                        self::languageTab('English', 'en'),
                        self::languageTab('Deutsch', 'de'),
                    ]),
                Section::make('Publication')
                    ->columns(2)
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->helperText('Used in the article URL. Generated from the French title.'),
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
                            ->directory('news')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(4096),
                        TextInput::make('image_alt')
                            ->label('Image description (alt text)'),
                    ]),
            ]);
    }

    private static function languageTab(string $label, string $locale, bool $required = false): Tab
    {
        $isFr = $locale === 'fr';

        return Tab::make($label)->schema([
            TextInput::make("title_{$locale}")
                ->label('Title')
                ->required($required)
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) use ($isFr) {
                    if ($isFr && blank($get('slug')) && filled($state)) {
                        $set('slug', Str::slug($state));
                    }
                }),
            Textarea::make("excerpt_{$locale}")
                ->label('Short summary')
                ->rows(2)
                ->maxLength(300),
            RichEditor::make("content_{$locale}")
                ->label('Content')
                ->required($required)
                ->fileAttachmentsDisk('public')
                ->fileAttachmentsDirectory('news/content')
                ->fileAttachmentsVisibility('public'),
        ]);
    }
}
