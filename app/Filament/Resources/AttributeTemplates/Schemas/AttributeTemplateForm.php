<?php

namespace App\Filament\Resources\AttributeTemplates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AttributeTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')
                ->label('Danh mục áp dụng')
                ->relationship('category', 'name')
                ->required()->searchable()->preload()
                ->placeholder('Chọn danh mục...')
                ->helperText('Mỗi danh mục chỉ nên có 1 mẫu để auto-fill hoạt động đúng'),

            TextInput::make('name')
                ->label('Tên mẫu')
                ->required()->maxLength(100)
                ->placeholder('VD: Điện Thoại, Laptop Gaming...'),
        ]);
    }
}
