<?php

namespace App\Filament\Resources\AttributeTemplates;

use App\Filament\Resources\AttributeTemplates\Pages\CreateAttributeTemplate;
use App\Filament\Resources\AttributeTemplates\Pages\EditAttributeTemplate;
use App\Filament\Resources\AttributeTemplates\Pages\ListAttributeTemplates;
use App\Filament\Resources\AttributeTemplates\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\AttributeTemplates\Schemas\AttributeTemplateForm;
use App\Filament\Resources\AttributeTemplates\Tables\AttributeTemplatesTable;
use App\Models\AttributeTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AttributeTemplateResource extends Resource
{
    protected static ?string $model = AttributeTemplate::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static ?string $navigationLabel = 'Thông số kỹ thuật';
    protected static ?string $modelLabel = 'Thông số kỹ thuật';
    protected static ?string $pluralModelLabel = 'Danh sách thông số kỹ thuật';
    protected static ?string $recordTitleAttribute = 'name';
    protected static UnitEnum|string|null $navigationGroup = 'Quản lý thuộc tính';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return AttributeTemplateForm::configure($schema);
    }
    public static function table(Table $table): Table
    {
        return AttributeTemplatesTable::configure($table);
    }
    public static function getRelations(): array
    {
        return [ItemsRelationManager::class];
    }
    public static function getPages(): array
    {
        return [
            'index'  => ListAttributeTemplates::route('/'),
            'create' => CreateAttributeTemplate::route('/create'),
            'edit'   => EditAttributeTemplate::route('/{record}/edit'),
        ];
    }
}
