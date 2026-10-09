<?php

namespace App\Filament\Resources\CategoryPages\Pages;

use App\Filament\Resources\CategoryPages\CategoryPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCategoryPages extends ManageRecords
{
    protected static string $resource = CategoryPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
