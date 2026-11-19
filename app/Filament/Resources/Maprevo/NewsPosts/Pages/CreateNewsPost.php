<?php

namespace App\Filament\Resources\Maprevo\NewsPosts\Pages;

use App\Filament\Resources\Maprevo\NewsPosts\NewsPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsPost extends CreateRecord
{
    protected static string $resource = NewsPostResource::class;
}
