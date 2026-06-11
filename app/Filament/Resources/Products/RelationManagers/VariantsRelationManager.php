<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\AttributeValue;
use App\Models\ProductVariant;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $title = 'Biến thể sản phẩm';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sku')
                ->label('Mã SKU')
                ->required()
                ->unique(ignoreRecord: true)
                ->placeholder('VD: IPHONE-DEN-512GB'),

            TextInput::make('price')
                ->label('Giá bán (₫)')
                ->required()
                ->numeric()
                ->prefix('₫')
                ->minValue(0),

            TextInput::make('compare_price')
                ->label('Giá gốc (₫)')
                ->numeric()
                ->prefix('₫')
                ->nullable()
                ->helperText('Để trống nếu không muốn gạch ngang'),

            TextInput::make('stock_quantity')
                ->label('Tồn kho')
                ->required()
                ->numeric()
                ->default(0)
                ->minValue(0),

            // BUG 2 FIX: Chỉ hiển thị attribute values thuộc các attributes
            // đã được gán cho sản phẩm này, thay vì lấy toàn bộ hệ thống.
            Select::make('attributeValues')
                ->label('Thuộc tính')
                ->multiple()
                ->options(function () {
                    $product      = $this->getOwnerRecord();
                    $attributeIds = $product->attributes()->pluck('attributes.id');

                    return AttributeValue::whereIn('attribute_id', $attributeIds)
                        ->with('attribute')
                        ->get()
                        ->mapWithKeys(fn ($v) => [
                            $v->id => $v->attribute->name . ': ' . $v->value,
                        ]);
                })
                ->relationship('attributeValues', 'value')
                ->preload()
                ->live()
                ->rules([
                    function ($component, $get, $record) {
                        return function (string $attribute, $value, $fail) use ($record) {
                            if (empty($value)) return;

                            $product     = $this->getOwnerRecord();
                            $selectedIds = collect($value)
                                ->map(fn ($id) => (int) $id)
                                ->sort()->values()->toArray();

                            $duplicate = $product->variants()
                                ->with('attributeValues')
                                ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                ->get()
                                ->first(function ($variant) use ($selectedIds) {
                                    $existing = $variant->attributeValues
                                        ->pluck('id')
                                        ->map(fn ($id) => (int) $id)
                                        ->sort()->values()->toArray();
                                    return $existing === $selectedIds;
                                });

                            if ($duplicate) {
                                $fail('Tổ hợp thuộc tính này đã tồn tại ở biến thể SKU: ' . $duplicate->sku);
                            }
                        };
                    },
                ]),

            FileUpload::make('image')
                ->label('Ảnh riêng biến thể')
                ->image()
                ->directory('products/variants')
                ->imagePreviewHeight('120')
                ->nullable(),

            Toggle::make('status')
                ->label('Đang bán')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                ImageColumn::make('image')
                    ->label('Ảnh')
                    ->size(50)
                    ->defaultImageUrl(asset('images/no-image.png')),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('attributeValues')
                    ->label('Thuộc tính')
                    ->getStateUsing(function ($record) {
                        return $record->attributeValues
                            ->map(fn ($val) => $val->attribute->name . ': ' . $val->value)
                            ->join(' / ');
                    })
                    ->badge()
                    ->color('gray'),

                TextColumn::make('price')
                    ->label('Giá bán')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('compare_price')
                    ->label('Giá gốc')
                    ->money('VND')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('stock_quantity')
                    ->label('Tồn kho')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === 0 => 'danger',
                        $state < 5   => 'warning',
                        default      => 'success',
                    }),

                ToggleColumn::make('status')
                    ->label('Đang bán'),
            ])
            ->filters([])
            ->headerActions([
                Action::make('generateVariants')
                    ->label('Generate biến thể')
                    ->icon('heroicon-o-sparkles')
                    ->color('warning')
                    ->form(function () {
                        $product    = $this->getOwnerRecord();
                        $attributes = $product->attributes()->with('attributeValues')->get();

                        if ($attributes->isEmpty()) {
                            return [
                                Placeholder::make('notice')
                                    ->label('')
                                    ->content('Sản phẩm chưa có thuộc tính nào. Vui lòng thêm thuộc tính trong form sản phẩm trước.'),
                            ];
                        }

                        $fields = $attributes->map(fn ($attribute) =>
                            CheckboxList::make("attribute_{$attribute->id}")
                                ->label($attribute->name)
                                ->options($attribute->attributeValues->pluck('value', 'id')->toArray())
                                ->columns(3)
                        )->toArray();

                        $fields[] = TextInput::make('default_price')
                            ->label('Giá bán mặc định (₫)')
                            ->numeric()->prefix('₫')->default(0)->minValue(0)
                            ->helperText('Áp dụng cho tất cả biến thể được tạo ra');

                        $fields[] = TextInput::make('default_stock')
                            ->label('Tồn kho mặc định')
                            ->numeric()->default(0)->minValue(0)
                            ->helperText('Áp dụng cho tất cả biến thể được tạo ra');

                        return $fields;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate biến thể')
                    ->modalDescription(fn () => $this->getOwnerRecord()->variants()->count() > 0
                        ? 'Sản phẩm đã có ' . $this->getOwnerRecord()->variants()->count() . ' biến thể. Chỉ tổ hợp mới sẽ được tạo thêm.'
                        : 'Chọn các giá trị thuộc tính, hệ thống sẽ tự tạo tất cả tổ hợp.'
                    )
                    ->modalSubmitActionLabel('Tiếp tục generate')
                    ->action(function (array $data): void {
                        $product      = $this->getOwnerRecord();
                        $defaultPrice = (float) ($data['default_price'] ?? 0);
                        $defaultStock = (int) ($data['default_stock'] ?? 0);

                        $groups = collect($data)
                            ->except(['default_price', 'default_stock'])
                            ->filter(fn ($v) => !empty($v))
                            ->map(fn ($v) => array_map('intval', (array) $v))
                            ->values()->toArray();

                        if (empty($groups)) {
                            Notification::make()->title('Chưa chọn giá trị thuộc tính nào')->warning()->send();
                            return;
                        }

                        $combinations   = $this->cartesian($groups);
                        $created        = 0;
                        $skipped        = 0;
                        $existingCombos = $product->variants()->with('attributeValues')->get()
                            ->map(fn ($v) => $v->attributeValues->pluck('id')
                                ->map(fn ($id) => (int) $id)->sort()->values()->toArray())
                            ->toArray();

                        foreach ($combinations as $combo) {
                            $comboIds = collect($combo)->map(fn ($id) => (int) $id)->sort()->values()->toArray();

                            if (in_array($comboIds, $existingCombos)) { $skipped++; continue; }

                            $valueLabels = AttributeValue::whereIn('id', $comboIds)->orderBy('id')
                                ->pluck('value')->map(fn ($v) => Str::slug($v))->implode('-');

                            $sku     = strtoupper(Str::slug($product->code) . '-' . $valueLabels);
                            $variant = ProductVariant::create([
                                'product_id'     => $product->id,
                                'sku'            => $sku,
                                'price'          => $defaultPrice,
                                'stock_quantity' => $defaultStock,
                                'status'         => true,
                            ]);

                            $variant->attributeValues()->attach($comboIds);
                            $existingCombos[] = $comboIds;
                            $created++;
                        }

                        if ($created === 0) {
                            Notification::make()->title("Tất cả {$skipped} tổ hợp đã tồn tại")->warning()->send();
                            return;
                        }

                        $msg = "Đã tạo {$created} biến thể mới";
                        if ($skipped > 0) $msg .= ", bỏ qua {$skipped} tổ hợp đã tồn tại";
                        Notification::make()->title($msg)->success()->send();
                    }),

                CreateAction::make()->label('Thêm thủ công'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkEditPrice')
                        ->label('Sửa giá hàng loạt')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('info')
                        ->form([
                            TextInput::make('price')
                                ->label('Giá bán mới (₫)')
                                ->numeric()->prefix('₫')->minValue(0)
                                ->helperText('Để trống nếu không muốn thay đổi giá bán'),

                            TextInput::make('compare_price')
                                ->label('Giá gốc mới (₫)')
                                ->numeric()->prefix('₫')->minValue(0)
                                ->helperText('Để trống nếu không muốn thay đổi giá gốc'),

                            TextInput::make('stock_quantity')
                                ->label('Tồn kho mới')
                                ->numeric()->minValue(0)
                                ->helperText('Để trống nếu không muốn thay đổi tồn kho'),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $updateData = array_filter([
                                'price'          => isset($data['price']) && $data['price'] !== '' ? (float) $data['price'] : null,
                                'compare_price'  => isset($data['compare_price']) && $data['compare_price'] !== '' ? (float) $data['compare_price'] : null,
                                'stock_quantity' => isset($data['stock_quantity']) && $data['stock_quantity'] !== '' ? (int) $data['stock_quantity'] : null,
                            ], fn ($v) => $v !== null);

                            if (empty($updateData)) {
                                Notification::make()->title('Chưa nhập giá trị nào để cập nhật')->warning()->send();
                                return;
                            }

                            $records->each->update($updateData);

                            Notification::make()
                                ->title('Đã cập nhật ' . $records->count() . ' biến thể')
                                ->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private function cartesian(array $groups): array
    {
        $result = [[]];
        foreach ($groups as $group) {
            $append = [];
            foreach ($result as $combo) {
                foreach ($group as $item) {
                    $append[] = array_merge($combo, [$item]);
                }
            }
            $result = $append;
        }
        return $result;
    }
}