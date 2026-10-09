<?php

namespace App\Filament\Resources\BrandPages\Pages;

use App\Filament\Resources\BrandPages\BrandPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBrandPages extends ManageRecords
{
    protected static string $resource = BrandPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
