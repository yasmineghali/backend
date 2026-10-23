<?php

namespace App\Filament\Resources\Bimrevo\NewsPosts\Pages;

use App\Filament\Resources\Bimrevo\NewsPosts\NewsPostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNewsPost extends EditRecord
{
    protected static string $resource = NewsPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
