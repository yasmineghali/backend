<?php

namespace App\Filament\Resources\Maprevo\Contacts;

use App\Filament\Resources\Maprevo\Contacts\Pages\EditContact;
use App\Filament\Resources\Maprevo\Contacts\Pages\ListContacts;
use App\Filament\Resources\Maprevo\Contacts\Pages\ViewContact;
use App\Filament\Resources\Maprevo\Contacts\Schemas\ContactForm;
use App\Filament\Resources\Maprevo\Contacts\Schemas\ContactInfolist;
use App\Filament\Resources\Maprevo\Contacts\Tables\ContactsTable;
use App\Models\Maprevo\Contact;
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
    protected static ?string $slug = 'maprevo/contacts';

    protected static string|UnitEnum|null $navigationGroup = 'MapRevo';

    protected static ?int $navigationSort = 40;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $recordTitleAttribute = 'name';

    // Page titles say MapRevo so they are not confused with GeoRevo's Contacts; the sidebar label stays short.
    protected static ?string $modelLabel = 'MapRevo contact';

    protected static ?string $pluralModelLabel = 'MapRevo contacts';

    protected static ?string $navigationLabel = 'Contacts';

    public static function getNavigationBadge(): ?string
    {
        $count = Contact::where('status', Contact::STATUS_NEW)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return ContactForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContactInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContacts::route('/'),
            'view' => ViewContact::route('/{record}'),
            'edit' => EditContact::route('/{record}/edit'),
        ];
    }
}
