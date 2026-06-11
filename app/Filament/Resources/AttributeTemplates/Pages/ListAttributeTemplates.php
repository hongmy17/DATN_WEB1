<?php

namespace App\Filament\Resources\AttributeTemplates\Pages;

use App\Filament\Resources\AttributeTemplates\AttributeTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttributeTemplates extends ListRecords
{
    protected static string $resource = AttributeTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
