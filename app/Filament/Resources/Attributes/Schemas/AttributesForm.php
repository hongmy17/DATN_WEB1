<?php

namespace App\Filament\Resources\Attributes\Schemas;

use App\Models\AttributeValue;
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
                ->placeholder('VD: Màu sắc, Dung lượng, Kích cỡ...'),

            Select::make('display_type')
                ->label('Kiểu hiển thị')
                ->options([
                    0 => 'Text (chip) — S / M / L / XL',
                    1 => 'Màu sắc (color swatch)',
                ])
                ->default(0)
                ->required()
                ->live()
                ->helperText('Chọn "Màu sắc" để hiện ô chọn mã màu bên dưới'),

            Repeater::make('attributeValues')
                ->label('Danh sách giá trị')
                ->relationship()
                ->schema([
                    TextInput::make('value')
                        ->label('Giá trị')
                        ->required()
                        ->placeholder('VD: Đỏ, 512GB, XL...'),

                    ColorPicker::make('color_code')
                        ->label('Mã màu')
                        ->visible(fn (callable $get) => (int) $get('../../display_type') === 1),

                    TextInput::make('sort_order')
                        ->hiddenLabel()
                        ->numeric()
                        ->default(0)
                        ->hidden()
                        ->dehydrated(),
                ])
                ->columns(fn (callable $get) => (int) $get('display_type') === 1 ? 2 : 1)
                ->defaultItems(0)
                ->addActionLabel('+ Thêm giá trị')
                ->reorderable('sort_order')
                ->collapsible()
                ->mutateDehydratedStateUsing(fn (array $state): array =>
                    collect($state)
                        ->values()
                        ->map(fn ($item, $i) => array_merge($item, ['sort_order' => $i + 1]))
                        ->toArray()
                )
                ->deleteAction(
                    fn ($action) => $action->before(function ($item, $action) {
                        $valueId = $item['id'] ?? null;
                        if (! $valueId) { return; }
                        $inUse = AttributeValue::where('id', $valueId)
                            ->whereHas('variantAttributeValues')
                            ->exists();
                        if ($inUse) {
                            \Filament\Notifications\Notification::make()
                                ->title('Không thể xóa giá trị này')
                                ->body('Đang được dùng bởi biến thể sản phẩm.')
                                ->danger()->send();
                            $action->cancel();
                        }
                    })
                ),
        ]);
    }
}
