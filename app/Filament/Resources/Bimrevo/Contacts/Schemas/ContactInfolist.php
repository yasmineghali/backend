<?php

namespace App\Filament\Resources\Bimrevo\Contacts\Schemas;

use App\Models\Bimrevo\Contact;
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
                        TextEntry::make('created_at')->label('Received')->dateTime('d.m.Y H:i'),
                    ]),
                Section::make('Message')
                    ->schema([
                        TextEntry::make('message')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ]),
                Section::make('Follow-up')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => Contact::STATUSES[$state] ?? $state),
                        TextEntry::make('handled_at')->dateTime('d.m.Y H:i')->placeholder('-'),
                        TextEntry::make('admin_notes')->label('Internal notes')->placeholder('-')->columnSpanFull(),
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
