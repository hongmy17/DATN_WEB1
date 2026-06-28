<?php
namespace App\Filament\Resources\AttributeTemplates\Pages;
use App\Filament\Resources\AttributeTemplates\AttributeTemplateResource;
use Filament\Actions\DeleteAction; use Filament\Resources\Pages\EditRecord;
class EditAttributeTemplate extends EditRecord {
    protected static string $resource = AttributeTemplateResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
