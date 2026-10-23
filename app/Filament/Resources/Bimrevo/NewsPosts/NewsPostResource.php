<?php

namespace App\Filament\Resources\Bimrevo\NewsPosts;

use App\Filament\Resources\Bimrevo\NewsPosts\Pages\CreateNewsPost;
use App\Filament\Resources\Bimrevo\NewsPosts\Pages\EditNewsPost;
use App\Filament\Resources\Bimrevo\NewsPosts\Pages\ListNewsPosts;
use App\Filament\Resources\Bimrevo\NewsPosts\Schemas\NewsPostForm;
use App\Filament\Resources\Bimrevo\NewsPosts\Tables\NewsPostsTable;
use App\Models\Bimrevo\NewsPost;
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
    protected static ?string $slug = 'bimrevo/news-posts';

    protected static string|UnitEnum|null $navigationGroup = 'BIMRevo';

    protected static ?int $navigationSort = 20;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    // Page titles say BIMRevo so they are not confused with GeoRevo's News; the sidebar label stays short.
    protected static ?string $modelLabel = 'BIMRevo news';

    protected static ?string $pluralModelLabel = 'BIMRevo news';

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
