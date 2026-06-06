<?php

namespace App\Filament\Resources\Attributes\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AttributesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Tên thuộc tính')
                ->required()
                ->maxLength(255)
                ->placeholder('VD: Màu sắc, Dung lượng...'),

            Select::make('display_type')
                ->label('Kiểu hiển thị')
                ->options([
                    0 => 'Text (chip)',
                    1 => 'Màu sắc (color swatch)',
                ])
                ->default(0)
                ->required()
                ->live(),

            Repeater::make('attributeValues')
                ->label('Danh sách giá trị')
                ->relationship()
                ->schema([
                    TextInput::make('value')
                        ->label('Giá trị')
                        ->required()
                        ->placeholder('VD: Đỏ, 512GB...'),

                    ColorPicker::make('color_code')
                        ->label('Mã màu')
                        ->visible(fn (callable $get) => $get('../../display_type') == 1)
                        ->dehydrated(fn (callable $get) => $get('../../display_type') == 1),

                    TextInput::make('sort_order')
                        ->label('Thứ tự')
                        ->numeric()
                        ->default(0),
                ])
                ->columns(3)
                ->defaultItems(0)
                ->addActionLabel('Thêm giá trị')
                ->reorderable()
                ->collapsible(),
        ]);
    }
}