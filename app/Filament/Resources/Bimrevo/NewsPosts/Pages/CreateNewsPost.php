<?php

namespace App\Filament\Resources\Bimrevo\NewsPosts\Pages;

use App\Filament\Resources\Bimrevo\NewsPosts\NewsPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsPost extends CreateRecord
{
    protected static string $resource = NewsPostResource::class;
}
