<?php

namespace App\Filament\Resources\Maprevo\Contacts\Pages;

use App\Filament\Resources\Maprevo\Contacts\ContactResource;
use Filament\Resources\Pages\ListRecords;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;
}
