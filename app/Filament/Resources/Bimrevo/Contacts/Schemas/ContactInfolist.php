<?php

namespace App\Filament\Resources\Bimrevo\Contacts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sender')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')->label('Email address')->copyable(),
                        TextEntry::make('phone')->placeholder('-'),
                        TextEntry::make('company')->placeholder('-'),
                        TextEntry::make('plan')->placeholder('-'),
                        TextEntry::make('created_at')->label('Received')->dateTime('d.m.Y H:i'),
                    ]),
                Section::make('Message')
                    ->schema([
                        TextEntry::make('message')
                            ->hiddenLabel()
                            ->placeholder('No message')
                            ->columnSpanFull(),
                    ]),
                Section::make('Technical details')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('source_url')->label('Page')->placeholder('-'),
                        TextEntry::make('ip_address')->label('IP')->placeholder('-'),
                        TextEntry::make('user_agent')->placeholder('-')->columnSpanFull(),
                    ]),
            ]);
    }
}
