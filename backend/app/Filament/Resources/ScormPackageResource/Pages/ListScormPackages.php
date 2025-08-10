<?php

namespace App\Filament\Resources\ScormPackageResource\Pages;

use App\Filament\Resources\ScormPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScormPackages extends ListRecords
{
    protected static string $resource = ScormPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
