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
use Illuminate\Support\Facades\DB;
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
                ->placeholder('VD: IPHONE-DEN-512GB')
                ->default(function () {
                    $product = $this->getOwnerRecord();
                    return $product->base_sku ? strtoupper($product->base_sku) . '-' : null;
                }),

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
                ->gte('price')
                ->helperText('Để trống nếu không muốn gạch ngang. Phải lớn hơn hoặc bằng giá bán.'),

            TextInput::make('stock_quantity')
                ->label('Tồn kho')
                ->required()
                ->numeric()
                ->default(0)
                ->minValue(0),

            Select::make('attributeValues')
                ->label('Thuộc tính')
                ->multiple()
                ->options(function () {
                    $product      = $this->getOwnerRecord();
                    $attributeIds = $product->attributes()->pluck('attributes.id');

                    // ── FIX 2: sort theo sort_order đúng thứ tự ──────────
                    return AttributeValue::whereIn('attribute_id', $attributeIds)
                        ->with('attribute')
                        ->orderBy('attribute_id')
                        ->orderBy('sort_order') // ← đảm bảo thứ tự S→M→L, Đỏ→Xanh→Đen
                        ->get()
                        ->mapWithKeys(fn ($v) => [
                            $v->id => $v->attribute->name . ': ' . $v->value,
                        ]);
                })
                ->relationship('attributeValues', 'value')
                ->preload()
                ->live()
                ->required(fn () => $this->getOwnerRecord()->attributes()->exists())
                ->rules([
                    function ($component, $get, $record) {
                        return function (string $attribute, $value, $fail) use ($record) {
                            if (empty($value)) {
                                return;
                            }

                            // ── FIX 5: không được chọn 2 value cùng 1 attribute ──
                            $selectedValues  = AttributeValue::whereIn('id', $value)->get();
                            $attributeGroups = $selectedValues->groupBy('attribute_id');

                            foreach ($attributeGroups as $attrId => $values) {
                                if ($values->count() > 1) {
                                    $attrName = $values->first()->attribute->name;
                                    $fail("Không được chọn 2 giá trị của cùng 1 thuộc tính \"{$attrName}\".");
                                    return;
                                }
                            }

                            // Kiểm tra trùng tổ hợp
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
                ->nullable()
                ->live()
                ->helperText(function (callable $get) {
                    $product  = $this->getOwnerRecord();
                    $valueIds = $get('attributeValues') ?? [];
                    $colorImg = $this->findColorLinkedImage($product, $valueIds);

                    if ($colorImg) {
                        return new \Illuminate\Support\HtmlString(
                            '💡 Tìm thấy ảnh của biến thể khác cùng màu này — '
                            . 'nếu bạn không upload, hệ thống sẽ tự dùng ảnh đó khi lưu.'
                        );
                    }

                    return 'Để trống sẽ dùng ảnh đại diện sản phẩm khi hiển thị ra client.';
                }),

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
                    ->html()
                    ->getStateUsing(function ($record) {
                        // ── FIX 2: sort theo sort_order khi hiển thị ──────
                        return $record->attributeValues
                            ->sortBy(fn ($v) => [$v->attribute_id, $v->sort_order])
                            ->map(function ($val) {
                                $label = e($val->attribute->name . ': ' . $val->value);

                                if ($val->attribute->display_type === 1 && $val->color_code) {
                                    $swatch = '<span style="display:inline-block;width:10px;height:10px;'
                                        . 'border-radius:50%;background:' . e($val->color_code) . ';'
                                        . 'margin-right:4px;vertical-align:middle;border:1px solid #ccc"></span>';
                                    return $swatch . $label;
                                }

                                return $label;
                            })
                            ->map(fn ($html) => '<span style="display:inline-block;background:#f3f4f6;'
                                . 'padding:2px 8px;border-radius:12px;font-size:12px;margin:1px">' . $html . '</span>')
                            ->join(' ');
                    }),

                TextColumn::make('price')
                    ->label('Giá bán')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('compare_price')
                    ->label('Giá gốc')
                    ->money('VND')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('discount_percent')
                    ->label('Giảm')
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn ($state) => $state ? "-{$state}%" : '—'),

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

                        // ── FIX 2: sort theo sort_order trong CheckboxList ──
                        $fields = $attributes->map(fn ($attribute) =>
                            CheckboxList::make("attribute_{$attribute->id}")
                                ->label($attribute->name)
                                ->options(
                                    $attribute->attributeValues
                                        ->sortBy('sort_order') // ← thứ tự đúng
                                        ->pluck('value', 'id')
                                        ->toArray()
                                )
                                ->columns(3)
                                ->live()
                        )->toArray();

                        $fields[] = TextInput::make('default_price')
                            ->label('Giá bán mặc định (₫)')
                            ->numeric()->prefix('₫')->default(0)->minValue(0)
                            ->live()
                            ->helperText('Áp dụng cho tất cả biến thể được tạo ra');

                        $fields[] = TextInput::make('default_stock')
                            ->label('Tồn kho mặc định')
                            ->numeric()->default(0)->minValue(0)
                            ->live()
                            ->helperText('Áp dụng cho tất cả biến thể được tạo ra');

                        // Preview bảng tổ hợp
                        $fields[] = Placeholder::make('preview')
                            ->label('Xem trước tổ hợp sẽ tạo')
                            ->content(function ($get) use ($product) {
                                $preview = $this->buildVariantPreview($product, $get);

                                if ($preview['groups'] === []) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div style="font-size:13px;color:var(--gray-500)">Chọn ít nhất 1 giá trị thuộc tính để xem trước tổ hợp.</div>'
                                    );
                                }

                                $rows = collect($preview['rows'])->map(function ($row) {
                                    $statusBadge = $row['exists']
                                        ? '<span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:10px;font-size:11px">Đã tồn tại — bỏ qua</span>'
                                        : '<span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:10px;font-size:11px">Sẽ tạo mới</span>';

                                    return '<tr style="border-bottom:1px solid var(--gray-200)">'
                                        . '<td style="padding:6px 10px;font-family:monospace;font-size:12px">' . e($row['sku']) . '</td>'
                                        . '<td style="padding:6px 10px;font-size:12px">' . e($row['label']) . '</td>'
                                        . '<td style="padding:6px 10px">' . $statusBadge . '</td>'
                                        . '</tr>';
                                })->join('');

                                $summary = "Tổng {$preview['total']} tổ hợp — "
                                    . "<b style='color:#166534'>{$preview['willCreate']} sẽ tạo mới</b>, "
                                    . "<span style='color:#92400e'>{$preview['willSkip']} đã tồn tại</span>";

                                $html = '<div style="font-size:13px;margin-bottom:8px">' . $summary . '</div>'
                                    . '<div style="max-height:260px;overflow-y:auto;border:1px solid var(--gray-200);border-radius:8px">'
                                    . '<table style="width:100%;border-collapse:collapse">'
                                    . '<thead><tr style="background:var(--gray-50)">'
                                    . '<th style="padding:6px 10px;text-align:left;font-size:11px">SKU dự kiến</th>'
                                    . '<th style="padding:6px 10px;text-align:left;font-size:11px">Tổ hợp</th>'
                                    . '<th style="padding:6px 10px;text-align:left;font-size:11px">Trạng thái</th>'
                                    . '</tr></thead><tbody>' . $rows . '</tbody></table></div>';

                                return new \Illuminate\Support\HtmlString($html);
                            })
                            ->columnSpanFull();

                        return $fields;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate biến thể')
                    ->modalDescription(fn () => $this->getOwnerRecord()->variants()->count() > 0
                        ? 'Sản phẩm đã có ' . $this->getOwnerRecord()->variants()->count() . ' biến thể. Chỉ tổ hợp mới sẽ được tạo thêm.'
                        : 'Chọn các giá trị thuộc tính, xem trước bảng tổ hợp bên dưới rồi xác nhận tạo.'
                    )
                    ->modalWidth('3xl')
                    ->modalSubmitActionLabel('Xác nhận tạo')
                    ->action(function (array $data): void {
                        $product      = $this->getOwnerRecord();
                        $defaultPrice = (float) ($data['default_price'] ?? 0);
                        $defaultStock = (int) ($data['default_stock'] ?? 0);
                        $skuPrefix    = $product->base_sku
                            ? strtoupper(Str::slug($product->base_sku))
                            : strtoupper(Str::slug($product->code));

                        $groups = collect($data)
                            ->except(['default_price', 'default_stock'])
                            ->filter(fn ($v) => ! empty($v))
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

                        $existingSkus = ProductVariant::pluck('sku')
                            ->map(fn ($s) => strtoupper($s))
                            ->flip()->toArray();

                        // ── FIX 4: collect variants vừa tạo để mở inline-edit sau ──
                        $newVariantIds = [];

                        try {
                            DB::transaction(function () use (
                                $combinations, $existingCombos, &$existingSkus,
                                $skuPrefix, $defaultPrice, $defaultStock, $product,
                                &$created, &$skipped, &$newVariantIds
                            ) {
                                foreach ($combinations as $combo) {
                                    $comboIds = collect($combo)->map(fn ($id) => (int) $id)
                                        ->sort()->values()->toArray();

                                    if (in_array($comboIds, $existingCombos)) {
                                        $skipped++;
                                        continue;
                                    }

                                    // ── FIX 2: sort theo attribute_id TRƯỚC, rồi sort_order SAU
                                    // Đảm bảo Màu sắc luôn đứng trước Dung lượng trong SKU
                                    // (vì attribute_id Màu sắc = 1, Dung lượng = 2)
                                    $valueLabels = AttributeValue::whereIn('id', $comboIds)
                                        ->orderBy('attribute_id')  // ← nhóm theo attribute trước
                                        ->orderBy('sort_order')    // ← rồi mới sort trong nhóm
                                        ->pluck('value')
                                        ->map(fn ($v) => Str::slug($v))
                                        ->implode('-');

                                    $baseSku = strtoupper($skuPrefix . '-' . $valueLabels);
                                    $sku     = $baseSku;
                                    $i       = 2;
                                    while (isset($existingSkus[$sku])) {
                                        $sku = $baseSku . '-' . $i;
                                        $i++;
                                    }

                                    $variant = ProductVariant::create([
                                        'product_id'     => $product->id,
                                        'sku'            => $sku,
                                        'price'          => $defaultPrice,
                                        'stock_quantity' => $defaultStock,
                                        // FIX: tự link ảnh theo màu nếu đã có variant khác cùng màu có ảnh
                                        'image'          => $this->findColorLinkedImage($product, $comboIds),
                                        'status'         => true,
                                    ]);

                                    $variant->attributeValues()->attach($comboIds);
                                    $existingCombos[]   = $comboIds;
                                    $existingSkus[$sku] = true;
                                    $newVariantIds[]    = $variant->id;
                                    $created++;
                                }
                            });
                        } catch (\Throwable $e) {
                            report($e);
                            Notification::make()
                                ->title('Generate biến thể thất bại, đã hủy toàn bộ thay đổi')
                                ->body('Vui lòng kiểm tra lại dữ liệu và thử lại. Lỗi: ' . $e->getMessage())
                                ->danger()
                                ->send();
                            return;
                        }

                        if ($created === 0) {
                            Notification::make()->title("Tất cả {$skipped} tổ hợp đã tồn tại")->warning()->send();
                            return;
                        }

                        $msg = "Đã tạo {$created} biến thể mới";
                        if ($skipped > 0) {
                            $msg .= ", bỏ qua {$skipped} tổ hợp đã tồn tại";
                        }
                        Notification::make()->title($msg)->success()->send();
                    }),

                // ── FIX 4: Inline edit giá/tồn kho cho tất cả variant sau generate ──
                Action::make('bulkEditAll')
                    ->label('Sửa giá & tồn kho')
                    ->icon('heroicon-o-pencil-square')
                    ->color('info')
                    ->visible(fn () => $this->getOwnerRecord()->variants()->exists())
                    ->form(function (): array {
                        $product  = $this->getOwnerRecord();
                        // FIX 3: eager-load 'attribute' để getAttributeLabelAttribute
                        // không bắn N+1 query khi render label từng variant
                        $variants = $product->variants()
                            ->with('attributeValues.attribute')
                            ->orderBy('id')
                            ->get();

                        $fields = [
                            Placeholder::make('hint')
                                ->label('')
                                ->content(new \Illuminate\Support\HtmlString(
                                    '<div style="font-size:13px;color:var(--gray-500);margin-bottom:4px">'
                                    . '💡 Sửa giá và tồn kho cho từng biến thể bên dưới.</div>'
                                )),
                        ];

                        foreach ($variants as $v) {
                            $label = $v->attribute_label ?: $v->sku;

                            $fields[] = Placeholder::make("header_{$v->id}")
                                ->label('')
                                ->content(new \Illuminate\Support\HtmlString(
                                    '<div style="font-weight:600;font-size:13px;padding:6px 0 2px;border-top:1px solid var(--gray-200);margin-top:4px">'
                                    . e($label)
                                    . ' <span style="font-weight:400;color:var(--gray-400);font-size:11px">(' . e($v->sku) . ')</span></div>'
                                ))
                                ->columnSpanFull();

                            $fields[] = TextInput::make("rows.{$v->id}.price")
                                ->label('Giá bán (₫)')
                                ->numeric()
                                ->prefix('₫')
                                ->minValue(0)
                                ->default($v->price);

                            $fields[] = TextInput::make("rows.{$v->id}.compare_price")
                                ->label('Giá gốc (₫)')
                                ->numeric()
                                ->prefix('₫')
                                ->minValue(0)
                                ->nullable()
                                ->default($v->compare_price);

                            $fields[] = TextInput::make("rows.{$v->id}.stock_quantity")
                                ->label('Tồn kho')
                                ->numeric()
                                ->minValue(0)
                                ->default($v->stock_quantity);
                        }

                        return $fields;
                    })
                    ->modalHeading('Sửa giá & tồn kho biến thể')
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel('Lưu tất cả')
                    ->action(function (array $data): void {
                        $rows    = $data['rows'] ?? [];
                        $updated = 0;
                        $errors  = [];

                        foreach ($rows as $variantId => $row) {
                            $variant = ProductVariant::find($variantId);
                            if (! $variant) {
                                continue;
                            }

                            $price        = isset($row['price']) && $row['price'] !== '' ? (float) $row['price'] : $variant->price;
                            $comparePrice = isset($row['compare_price']) && $row['compare_price'] !== '' ? (float) $row['compare_price'] : null;
                            $stock        = isset($row['stock_quantity']) && $row['stock_quantity'] !== '' ? (int) $row['stock_quantity'] : $variant->stock_quantity;

                            if ($comparePrice && $comparePrice < $price) {
                                $errors[] = "SKU {$variant->sku}: giá gốc nhỏ hơn giá bán — bỏ qua.";
                                continue;
                            }

                            $variant->update([
                                'price'          => $price,
                                'compare_price'  => $comparePrice ?: null,
                                'stock_quantity' => $stock,
                            ]);

                            $updated++;
                        }

                        if (! empty($errors)) {
                            Notification::make()
                                ->title("Đã lưu {$updated} biến thể, bỏ qua " . count($errors))
                                ->body(implode("\n", $errors))
                                ->warning()->send();
                            return;
                        }

                        Notification::make()
                            ->title("Đã cập nhật {$updated} biến thể")
                            ->success()->send();
                    }),

                Action::make('deleteAllVariants')
                    ->label('Xóa tất cả & tạo lại')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Xóa toàn bộ biến thể?')
                    ->modalDescription('Hành động này sẽ xóa TẤT CẢ biến thể hiện tại của sản phẩm. Không thể hoàn tác. Dùng khi muốn đổi lại toàn bộ cấu trúc thuộc tính.')
                    ->modalSubmitActionLabel('Xóa tất cả')
                    ->visible(fn () => $this->getOwnerRecord()->variants()->exists())
                    ->disabled(fn () => $this->getOwnerRecord()->status)
                    ->tooltip(fn () => $this->getOwnerRecord()->status
                        ? 'Tắt hiển thị sản phẩm trước khi xóa toàn bộ biến thể'
                        : null
                    )
                    ->action(function (): void {
                        $product = $this->getOwnerRecord();

                        if ($product->status) {
                            Notification::make()
                                ->title('Không thể xóa toàn bộ biến thể')
                                ->body('Sản phẩm đang hiển thị cho khách. Hãy tắt hiển thị trước.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $count = DB::transaction(function () use ($product) {
                            $count = $product->variants()->count();
                            $product->variants()->each(function ($variant) {
                                $variant->attributeValues()->detach();
                                $variant->delete();
                            });
                            return $count;
                        });

                        Notification::make()
                            ->title("Đã xóa {$count} biến thể")
                            ->body('Dùng nút "Generate biến thể" để tạo lại.')
                            ->success()->send();
                    }),

                CreateAction::make()
                    ->label('Thêm thủ công')
                    ->mutateFormDataUsing(function (array $data): array {
                        return $this->applyColorLinkedImage($data);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        return $this->applyColorLinkedImage($data);
                    }),
                DeleteAction::make()
                    ->before(function ($record, DeleteAction $action) {
                        $product = $record->product;

                        if ($product->status && $product->variants()->count() <= 1) {
                            Notification::make()
                                ->title('Không thể xóa biến thể cuối cùng')
                                ->body('Sản phẩm đang hiển thị cho khách. Hãy tắt hiển thị trước hoặc thêm biến thể khác.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
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

                            foreach ($records as $record) {
                                $newPrice   = $updateData['price'] ?? $record->price;
                                $newCompare = $updateData['compare_price'] ?? $record->compare_price;

                                if ($newCompare && $newCompare < $newPrice) {
                                    Notification::make()
                                        ->title("Bỏ qua SKU {$record->sku}: giá gốc nhỏ hơn giá bán")
                                        ->warning()->send();
                                    continue;
                                }

                                $record->update($updateData);
                            }

                            Notification::make()
                                ->title('Đã cập nhật ' . $records->count() . ' biến thể')
                                ->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    /**
     * Tìm ảnh từ 1 variant khác (cùng sản phẩm) đã có ảnh, chia sẻ chung
     * 1 attribute_value thuộc nhóm "màu sắc" (display_type = 1) với
     * danh sách $valueIds đang được chọn trong form hiện tại.
     *
     * VD: variant "Đen / 512GB" đã có ảnh → variant "Đen / 1TB" mới tạo,
     * chưa upload ảnh, sẽ tự mượn đúng ảnh đó vì cùng giá trị "Đen".
     *
     * Trả về path ảnh (string) hoặc null nếu không tìm thấy.
     */
    private function findColorLinkedImage($product, array $valueIds, ?int $excludeVariantId = null): ?string
    {
        if (empty($valueIds)) {
            return null;
        }

        // Lấy đúng các attribute_value thuộc nhóm màu (display_type = 1)
        // trong danh sách đang chọn — bỏ qua các thuộc tính khác (size, dung lượng...)
        $colorValueIds = AttributeValue::whereIn('id', $valueIds)
            ->whereHas('attribute', fn ($q) => $q->where('display_type', 1))
            ->pluck('id');

        if ($colorValueIds->isEmpty()) {
            return null;
        }

        $variant = $product->variants()
            ->whereNotNull('image')
            ->when($excludeVariantId, fn ($q) => $q->where('id', '!=', $excludeVariantId))
            ->whereHas('attributeValues', fn ($q) => $q->whereIn('attribute_values.id', $colorValueIds))
            ->first();

        return $variant?->image;
    }

    /**
     * Nếu admin để trống ô ảnh nhưng đã chọn 1 màu đã có ảnh ở variant khác
     * cùng sản phẩm, tự gán ảnh đó vào $data trước khi lưu.
     * Admin upload ảnh riêng thì ưu tiên ảnh admin upload, không override.
     */
    private function applyColorLinkedImage(array $data): array
    {
        if (! empty($data['image'])) {
            return $data; // admin đã tự upload, không can thiệp
        }

        $product  = $this->getOwnerRecord();
        $valueIds = $data['attributeValues'] ?? [];

        $linkedImage = $this->findColorLinkedImage($product, $valueIds);

        if ($linkedImage) {
            $data['image'] = $linkedImage;
        }

        return $data;
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

    private function extractGroupsFromFormState(callable $get, \Illuminate\Database\Eloquent\Collection $attributes): array
    {
        return $attributes
            ->map(fn ($attribute) => $get("attribute_{$attribute->id}") ?? [])
            ->filter(fn ($v) => ! empty($v))
            ->map(fn ($v) => array_map('intval', (array) $v))
            ->values()->toArray();
    }

    private function buildVariantPreview($product, callable $get): array
    {
        $attributes = $product->attributes()->with('attributeValues')->get();
        $groups     = $this->extractGroupsFromFormState($get, $attributes);

        if (empty($groups)) {
            return ['groups' => [], 'rows' => [], 'total' => 0, 'willCreate' => 0, 'willSkip' => 0];
        }

        $skuPrefix = $product->base_sku
            ? strtoupper(Str::slug($product->base_sku))
            : strtoupper(Str::slug($product->code));

        $combinations   = $this->cartesian($groups);
        $existingCombos = $product->variants()->with('attributeValues')->get()
            ->map(fn ($v) => $v->attributeValues->pluck('id')
                ->map(fn ($id) => (int) $id)->sort()->values()->toArray())
            ->toArray();

        $existingSkus = ProductVariant::pluck('sku')
            ->map(fn ($s) => strtoupper($s))
            ->flip()->toArray();

        $rows       = [];
        $willCreate = 0;
        $willSkip   = 0;

        foreach ($combinations as $combo) {
            $comboIds = collect($combo)->map(fn ($id) => (int) $id)->sort()->values()->toArray();
            $exists   = in_array($comboIds, $existingCombos);

            // ── FIX 2: sort theo sort_order khi preview cũng nhất quán ──
            $values = AttributeValue::whereIn('id', $comboIds)
                ->with('attribute')
                ->get()
                ->sortBy(fn ($v) => [$v->attribute_id, $v->sort_order]);

            $label       = $values->map(fn ($v) => $v->attribute->name . ': ' . $v->value)->join(' / ');
            $valueLabels = $values->map(fn ($v) => Str::slug($v->value))->implode('-');
            $baseSku     = strtoupper($skuPrefix . '-' . $valueLabels);

            if ($exists) {
                $willSkip++;
            } else {
                $sku = $baseSku;
                $i   = 2;
                while (isset($existingSkus[$sku])) {
                    $sku = $baseSku . '-' . $i;
                    $i++;
                }
                $existingSkus[$sku] = true;
                $existingCombos[]   = $comboIds;
                $baseSku            = $sku;
                $willCreate++;
            }

            $rows[] = [
                'sku'    => $baseSku,
                'label'  => $label,
                'exists' => $exists,
            ];
        }

        return [
            'groups'     => $groups,
            'rows'       => $rows,
            'total'      => count($rows),
            'willCreate' => $willCreate,
            'willSkip'   => $willSkip,
        ];
    }
}