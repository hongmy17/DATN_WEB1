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

                    // BUG FIX: Thêm ->live() để color_code phản ứng ngay khi display_type thay đổi.
                    // Path '../../display_type' đúng: lên 1 cấp (Repeater item) → lên 1 cấp (Schema) → lấy display_type.
                    ColorPicker::make('color_code')
                        ->label('Mã màu')
                        ->live()
                        ->visible(fn (callable $get) => (int) $get('../../display_type') === 1),

                    TextInput::make('sort_order')
                        ->label('Thứ tự')
                        ->numeric()
                        ->default(0)
                        ->disabled()
                        ->dehydrated(),
                ])
                ->columns(3)
                ->defaultItems(0)
                ->addActionLabel('Thêm giá trị')
                ->reorderable('sort_order')
                ->collapsible()
                ->afterStateUpdated(function ($state, callable $set) {
                    $sorted = collect($state)
                        ->values()
                        ->map(function ($item, $index) {
                            $item['sort_order'] = $index + 1;
                            return $item;
                        })
                        ->toArray();

                    $set('attributeValues', $sorted);
                })
                ->afterStateHydrated(function ($component, $state) {
                    if (!empty($state)) {
                        $sorted = collect($state)
                            ->sortBy('sort_order')
                            ->values()
                            ->map(function ($item, $index) {
                                $item['sort_order'] = $index + 1;
                                return $item;
                            })
                            ->toArray();
                        $component->state($sorted);
                    }
                })
                ->mutateDehydratedStateUsing(function (array $state): array {
                    return collect($state)
                        ->values()
                        ->map(function ($item, $index) {
                            $item['sort_order'] = $index + 1;
                            return $item;
                        })
                        ->toArray();
                }),
        ]);
    }
}