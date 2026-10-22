<?php

namespace App\Filament\Resources\Bimrevo\Contacts\Tables;

use App\Models\Bimrevo\Contact;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('is_read')
                    ->label('')
                    ->state(fn (Contact $record) => $record->isRead())
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-s-envelope')
                    ->trueColor('gray')
                    ->falseColor('primary')
                    ->tooltip(fn (Contact $record) => $record->isRead() ? 'Read' : 'Unread'),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->weight(fn (Contact $record) => $record->isRead() ? 'normal' : 'bold'),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('plan')
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('message')
                    ->limit(80)
                    ->wrap()
                    ->tooltip(fn (Contact $record) => $record->message)
                    ->searchable(),
                TextColumn::make('company')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('read')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Read')
                    ->falseLabel('Unread')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('read_at'),
                        false: fn (Builder $query) => $query->whereNull('read_at'),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                ViewAction::make()->label('Open'),
                Action::make('toggleRead')
                    ->label(fn (Contact $record) => $record->isRead() ? 'Mark as unread' : 'Mark as read')
                    ->icon(fn (Contact $record) => $record->isRead() ? 'heroicon-o-envelope' : 'heroicon-o-envelope-open')
                    ->color('gray')
                    ->action(fn (Contact $record) => $record->isRead() ? $record->markAsUnread() : $record->markAsRead()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markRead')
                        ->label('Mark as read')
                        ->icon('heroicon-o-envelope-open')
                        ->action(fn (Collection $records) => $records->each->markAsRead())
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('markUnread')
                        ->label('Mark as unread')
                        ->icon('heroicon-o-envelope')
                        ->action(fn (Collection $records) => $records->each->markAsUnread())
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
