<?php

namespace App\Filament\Resources\Maprevo\NewsPosts;

use App\Filament\Resources\Maprevo\NewsPosts\Pages\CreateNewsPost;
use App\Filament\Resources\Maprevo\NewsPosts\Pages\EditNewsPost;
use App\Filament\Resources\Maprevo\NewsPosts\Pages\ListNewsPosts;
use App\Filament\Resources\Maprevo\NewsPosts\Schemas\NewsPostForm;
use App\Filament\Resources\Maprevo\NewsPosts\Tables\NewsPostsTable;
use App\Models\Maprevo\NewsPost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NewsPostResource extends Resource
{
    protected static ?string $model = NewsPost::class;

    // Explicit slug: the model basename would otherwise collide with GeoRevo's /admin/news-posts.
    protected static ?string $slug = 'maprevo/news-posts';

    protected static string|UnitEnum|null $navigationGroup = 'MapRevo';

    protected static ?int $navigationSort = 50;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    // Page titles say MapRevo so they are not confused with GeoRevo's News; the sidebar label stays short.
    protected static ?string $modelLabel = 'MapRevo news';

    protected static ?string $pluralModelLabel = 'MapRevo news';

    protected static ?string $navigationLabel = 'News';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return NewsPostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsPostsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsPosts::route('/'),
            'create' => CreateNewsPost::route('/create'),
            'edit' => EditNewsPost::route('/{record}/edit'),
        ];
    }
}
