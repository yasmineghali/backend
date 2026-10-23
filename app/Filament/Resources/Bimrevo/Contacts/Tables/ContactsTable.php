<?php

namespace App\Filament\Resources\Bimrevo\Contacts\Tables;

use App\Models\Bimrevo\Contact;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('phone')
                    ->label('Phone')
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
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Contact::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        Contact::STATUS_NEW => 'warning',
                        Contact::STATUS_IN_PROGRESS => 'info',
                        Contact::STATUS_HANDLED => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('company')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('source_url')
                    ->label('Page')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('handled_at')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->options(Contact::STATUSES),
                SelectFilter::make('plan')->options(array_combine(Contact::PLANS, Contact::PLANS)),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('markHandled')
                    ->label('Mark handled')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Contact $record) => $record->status !== Contact::STATUS_HANDLED)
                    ->action(fn (Contact $record) => $record->update([
                        'status' => Contact::STATUS_HANDLED,
                        'handled_at' => now(),
                    ])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markHandled')
                        ->label('Mark as handled')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn (Collection $records) => $records->each->update([
                            'status' => Contact::STATUS_HANDLED,
                            'handled_at' => now(),
                        ]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
