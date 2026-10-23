<?php

namespace App\Filament\Resources\Bimrevo\Contacts\Schemas;

use App\Models\Bimrevo\Contact;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Request')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->disabled(),
                        TextInput::make('email')->label('Email address')->disabled(),
                        TextInput::make('phone')->disabled(),
                        TextInput::make('company')->disabled(),
                        Textarea::make('message')->disabled()->rows(6)->columnSpanFull(),
                    ]),
                Section::make('Follow-up')
                    ->schema([
                        Select::make('status')
                            ->options(Contact::STATUSES)
                            ->required()
                            ->native(false),
                        Textarea::make('admin_notes')
                            ->label('Internal notes')
                            ->rows(4),
                    ]),
            ]);
    }
}
