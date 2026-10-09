<?php

namespace App\Filament\Resources\NewsPosts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NewsPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->square(),
                TextColumn::make('title_fr')
                    ->label('Title')
                    ->searchable()
                    ->limit(60)
                    ->weight('bold'),
                TextColumn::make('published_at')
                    ->label('Date')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                IconColumn::make('title_en')
                    ->label('EN')
                    ->boolean()
                    ->state(fn ($record) => filled($record->title_en)),
                IconColumn::make('title_de')
                    ->label('DE')
                    ->boolean()
                    ->state(fn ($record) => filled($record->title_de)),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Published'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
