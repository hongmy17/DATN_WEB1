<?php

namespace App\Filament\Resources\AttributeTemplates\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AttributeTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')
                ->label('Danh mục')
                ->relationship('category', 'name')  // ← dùng relationship như Product
                ->required()
                ->searchable()
                ->preload()
                ->placeholder('Chọn danh mục...'),

            TextInput::make('name')
                ->label('Tên mẫu')
                ->required()
                ->maxLength(100)
                ->placeholder('VD: Điện Thoại, Laptop...'),
        ]);
    }
}
