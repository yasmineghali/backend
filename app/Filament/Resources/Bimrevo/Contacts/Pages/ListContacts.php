<?php

namespace App\Filament\Resources\Bimrevo\Contacts\Pages;

use App\Filament\Resources\Bimrevo\Contacts\ContactResource;
use Filament\Resources\Pages\ListRecords;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;
}
