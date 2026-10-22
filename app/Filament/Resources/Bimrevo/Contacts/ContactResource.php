<?php

namespace App\Filament\Resources\Bimrevo\Contacts;

use App\Filament\Resources\Bimrevo\Contacts\Pages\ListContacts;
use App\Filament\Resources\Bimrevo\Contacts\Pages\ViewContact;
use App\Filament\Resources\Bimrevo\Contacts\Schemas\ContactInfolist;
use App\Filament\Resources\Bimrevo\Contacts\Tables\ContactsTable;
use App\Models\Bimrevo\Contact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    // Explicit slug: the model basename would otherwise collide with GeoRevo's /admin/contacts.
    protected static ?string $slug = 'bimrevo/contacts';

    protected static string|UnitEnum|null $navigationGroup = 'BIMRevo';

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $recordTitleAttribute = 'name';

    // Page titles say BIMRevo so they are not confused with GeoRevo's Contacts; the sidebar label stays short.
    protected static ?string $modelLabel = 'BIMRevo contact';

    protected static ?string $pluralModelLabel = 'BIMRevo contacts';

    protected static ?string $navigationLabel = 'Contacts';

    public static function getNavigationBadge(): ?string
    {
        $count = Contact::unread()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContactInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContacts::route('/'),
            'view' => ViewContact::route('/{record}'),
        ];
    }
}
