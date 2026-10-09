<?php

namespace App\Filament\Resources\Catalogues\Pages;

use App\Filament\Resources\Catalogues\CatalogueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCatalogues extends ManageRecords
{
    protected static string $resource = CatalogueResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
