<?php

namespace App\Filament\Resources\Products\RelationManagers;
use Filament\Forms\Components\Radio;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $title = 'Biến thể sản phẩm';

    // ── Validation helper (dùng chung cho form, bulkEdit, generate) ──────────

    /**
     * compare_price phải lớn hơn price (bằng nhau không hợp lệ vì % giảm = 0).
     */
    private static function isValidComparePrice(?float $compare, float $price): bool
    {
        return $compare === null || $compare > $price;
    }

    // ── Form thêm / sửa 1 biến thể ──────────────────────────────────────────

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            // ── SKU ───────────────────────────────────────────────────────────
            TextInput::make('sku')
                ->label('Mã SKU')
                ->required()
                ->unique(ignoreRecord: true)
                ->placeholder('VD: IPHONE-DEN-512GB')
                ->default(function () {
                    $product = $this->getOwnerRecord();
                    return $product->base_sku
                        ? strtoupper($product->base_sku) . '-'
                        : null;
                }),

            // ── Giá ───────────────────────────────────────────────────────────
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
                ->helperText('Phải lớn hơn giá bán để % giảm có ý nghĩa. Để trống nếu không cần gạch ngang.')
                ->rules([
                    fn(callable $get) => function (string $attribute, $value, $fail) use ($get) {
                        if ($value === null || $value === '') {
                            return;
                        }
                        if (! self::isValidComparePrice((float) $value, (float) ($get('price') ?? 0))) {
                            $fail('Giá gốc phải lớn hơn giá bán.');
                        }
                    },
                ]),

            // ── Flash sale ────────────────────────────────────────────────────
            TextInput::make('sale_price')
                ->label('Giá khuyến mãi (₫)')
                ->numeric()
                ->prefix('₫')
                ->nullable()
                ->helperText('Giá tạm thời áp dụng trong khoảng thời gian bên dưới. Để trống nếu không có.')
                ->rules([
                    fn(callable $get) => function (string $attribute, $value, $fail) use ($get) {
                        if ($value === null || $value === '') {
                            return;
                        }
                        if ((float) $value >= (float) ($get('price') ?? 0)) {
                            $fail('Giá khuyến mãi phải nhỏ hơn giá bán.');
                        }
                    },
                ]),

            DateTimePicker::make('sale_starts_at')
                ->label('Bắt đầu khuyến mãi')
                ->nullable()
                ->native(false),

            DateTimePicker::make('sale_ends_at')
                ->label('Kết thúc khuyến mãi')
                ->nullable()
                ->native(false)
                ->afterOrEqual('sale_starts_at')
                ->rules([
                    fn(callable $get) => function (string $attribute, $value, $fail) use ($get) {
                        if ($value && $get('sale_price') && now()->greaterThan($value)) {
                            $fail('Thời gian kết thúc đã qua — khuyến mãi sẽ không có hiệu lực.');
                        }
                    },
                ]),

            // ── Kho ───────────────────────────────────────────────────────────
            Toggle::make('manage_stock')
                ->label('Quản lý tồn kho')
                ->default(true)
                ->live()
                ->helperText('Tắt với hàng đặt trước / dịch vụ — luôn coi như còn hàng'),

            TextInput::make('stock_quantity')
                ->label('Tồn kho')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->visible(fn(callable $get) => (bool) $get('manage_stock'))
                ->required(fn(callable $get) => (bool) $get('manage_stock')),

            // ── Thuộc tính ────────────────────────────────────────────────────
            Select::make('attributeValues')
                ->label('Thuộc tính (màu / size...)')
                ->multiple()
                ->options(function () {
                    $product      = $this->getOwnerRecord();
                    $attributeIds = $product->attributes()->pluck('attributes.id');

                    return AttributeValue::whereIn('attribute_id', $attributeIds)
                        ->with('attribute')
                        ->orderBy('attribute_id')
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn($v) => [
                            $v->id => $v->attribute->name . ': ' . $v->value,
                        ]);
                })
                // ── FIX: dùng afterStateHydrated thay vì default() ─────────────
                // ->default() chỉ chạy khi tạo mới (Create). Khi mở Edit modal,
                // Filament fill form từ $record->toArray() — không bao gồm BelongsToMany
                // → Select attributeValues hiển thị TRỐNG khi sửa biến thể.
                // ->afterStateHydrated() chạy cho cả Create lẫn Edit, sau khi Filament
                // fill xong, nên luôn đọc đúng giá trị hiện có từ DB.
                ->afterStateHydrated(function ($component, $record) {
                    if ($record) {
                        $component->state(
                            $record->attributeValues()->pluck('attribute_values.id')->toArray()
                        );
                    }
                })
                ->saveRelationshipsUsing(fn($record, $state) => $record->attributeValues()->sync($state ?? []))
                ->live()
                ->required(fn() => $this->getOwnerRecord()->attributes()->exists())
                ->rules([
                    function ($record) {
                        return function (string $attribute, $value, $fail) use ($record) {
                            if (empty($value)) {
                                return;
                            }

                            // Không cho chọn 2 value cùng 1 attribute
                            $selectedValues = AttributeValue::whereIn('id', $value)->get();
                            foreach ($selectedValues->groupBy('attribute_id') as $attrId => $vals) {
                                if ($vals->count() > 1) {
                                    $fail('Không được chọn 2 giá trị của cùng thuộc tính "' . $vals->first()->attribute->name . '".');
                                    return;
                                }
                            }

                            // Không cho trùng tổ hợp
                            $selectedIds = collect($value)->map(fn($id) => (int) $id)->sort()->values()->toArray();
                            $product     = $this->getOwnerRecord();

                            $duplicate = $product->variants()
                                ->with('attributeValues')
                                ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                ->get()
                                ->first(function ($variant) use ($selectedIds) {
                                    $existing = $variant->attributeValues
                                        ->pluck('id')
                                        ->map(fn($id) => (int) $id)
                                        ->sort()->values()->toArray();
                                    return $existing === $selectedIds;
                                });

                            if ($duplicate) {
                                $fail('Tổ hợp thuộc tính này đã tồn tại ở SKU: ' . $duplicate->sku);
                            }
                        };
                    },
                ]),

            // ── Ảnh ───────────────────────────────────────────────────────────
            FileUpload::make('image')
                ->label('Ảnh chính biến thể')
                ->image()
                ->directory('products/variants')
                ->imagePreviewHeight('120')
                ->nullable()
                ->live()
                ->helperText(function (callable $get) {
                    $colorImg = $this->findColorLinkedImage(
                        $this->getOwnerRecord(),
                        $get('attributeValues') ?? []
                    );
                    return $colorImg
                        ? new \Illuminate\Support\HtmlString('💡 Biến thể cùng màu đã có ảnh — sẽ tự kế thừa nếu bạn không upload.')
                        : 'Để trống sẽ dùng ảnh đại diện sản phẩm khi hiển thị.';
                }),


            // ── Mô tả & trạng thái ────────────────────────────────────────────
            Textarea::make('description')
                ->label('Mô tả ngắn riêng biến thể')
                ->rows(2)
                ->nullable()
                ->placeholder('VD: Bản 512GB tặng thêm ốp bảo vệ'),

            Toggle::make('is_default')
                ->label('Biến thể mặc định')
                ->default(false)
                ->helperText('Hiển thị đầu tiên khi khách vào trang sản phẩm'),

            Toggle::make('status')
                ->label('Đang bán')
                ->default(true),
        ]);
    }

    // ── Bảng danh sách biến thể ──────────────────────────────────────────────

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            // ── FIX N+1: eager-load attributeValues.attribute trong 1 query ──
            // Cột "Thuộc tính" gọi $record->attributeValues->map(fn($v) => $v->attribute->name)
            // Nếu không eager-load: 10 variant × 2 attribute = 20 query phụ mỗi lần render.
            ->modifyQueryUsing(fn($query) => $query->with('attributeValues.attribute'))
            ->columns([
                ImageColumn::make('image')
                    ->label('Ảnh')
                    ->size(48)
                    ->defaultImageUrl(asset('images/no-image.png'))
                    ->extraImgAttributes(['class' => 'rounded']),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight('medium'),

                TextColumn::make('attributeValues')
                    ->label('Thuộc tính')
                    ->html()
                    ->getStateUsing(function ($record) {
                        return $record->attributeValues
                            ->sortBy(fn($v) => [$v->attribute_id, $v->sort_order])
                            ->map(function ($val) {
                                $label = e($val->attribute->name . ': ' . $val->value);
                                if ($val->attribute->display_type === 1 && $val->color_code) {
                                    $swatch = '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:'
                                        . e($val->color_code) . ';margin-right:4px;vertical-align:middle;border:1px solid #ccc"></span>';
                                    $label  = $swatch . $label;
                                }
                                return '<span style="display:inline-block;background:var(--gray-100,#f3f4f6);padding:2px 8px;border-radius:12px;font-size:12px;margin:1px">'
                                    . $label . '</span>';
                            })
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
                    ->placeholder('—')
                    ->color('gray'),

                TextColumn::make('discount_percent')
                    ->label('Giảm')
                    ->badge()
                    // ── FIX: null → xám thay vì đỏ, chỉ đỏ khi thực sự có giảm giá ──
                    ->color(fn($state) => $state ? 'danger' : 'gray')
                    ->formatStateUsing(fn($state) => $state ? "-{$state}%" : '—')
                    ->placeholder('—'),

                TextColumn::make('stock_quantity')
                    ->label('Kho')
                    ->badge()
                    ->formatStateUsing(fn($state, $record) => $record->manage_stock ? $state : '∞')
                    ->color(fn($state, $record) => match (true) {
                        ! $record->manage_stock => 'gray',
                        $state === 0            => 'danger',
                        $state < 5              => 'warning',
                        default                 => 'success',
                    }),

                ToggleColumn::make('is_default')
                    ->label('Mặc định')
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            Notification::make()
                                ->title("Đã đặt SKU {$record->sku} làm mặc định")
                                ->success()
                                ->send();
                        }
                        $this->dispatch('$refresh');
                    }),

                ToggleColumn::make('status')
                    ->label('Bán'),
            ])
            ->filters([])
            ->headerActions([
                // ── Generate tự động ─────────────────────────────────────────
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
                                    ->content('Sản phẩm chưa có thuộc tính. Thêm thuộc tính trong form sản phẩm trước.'),
                            ];
                        }

                        $fields = $attributes->map(
                            fn($attr) =>
                            CheckboxList::make("attribute_{$attr->id}")
                                ->label($attr->name)
                                ->options(
                                    $attr->attributeValues
                                        ->sortBy('sort_order')
                                        ->pluck('value', 'id')
                                        ->toArray()
                                )
                                ->columns(3)
                                ->live()
                        )->toArray();

                        $fields[] = TextInput::make('default_price')
                            ->label('Giá bán mặc định (₫)')
                            ->numeric()->prefix('₫')->default(0)->minValue(0)->live();

                        $fields[] = TextInput::make('default_stock')
                            ->label('Tồn kho mặc định')
                            ->numeric()->default(0)->minValue(0)->live();

                        $fields[] = Placeholder::make('preview')
                            ->label('Xem trước tổ hợp')
                            ->content(function ($get) use ($product) {
                                $preview = $this->buildVariantPreview($product, $get);

                                if (empty($preview['groups'])) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div style="font-size:13px;color:var(--gray-500)">Chọn ít nhất 1 giá trị để xem trước.</div>'
                                    );
                                }

                                $rows = collect($preview['rows'])->map(function ($row) {
                                    $badge = $row['exists']
                                        ? '<span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:10px;font-size:11px">Đã tồn tại</span>'
                                        : '<span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:10px;font-size:11px">Sẽ tạo mới</span>';
                                    return '<tr style="border-bottom:1px solid var(--gray-200)">'
                                        . '<td style="padding:5px 10px;font-family:monospace;font-size:12px">' . e($row['sku']) . '</td>'
                                        . '<td style="padding:5px 10px;font-size:12px">' . e($row['label']) . '</td>'
                                        . '<td style="padding:5px 10px">' . $badge . '</td>'
                                        . '</tr>';
                                })->join('');

                                $summary = "Tổng {$preview['total']} tổ hợp — "
                                    . "<b style='color:#166534'>{$preview['willCreate']} sẽ tạo</b>, "
                                    . "<span style='color:#92400e'>{$preview['willSkip']} đã có</span>";

                                return new \Illuminate\Support\HtmlString(
                                    '<div style="font-size:13px;margin-bottom:8px">' . $summary . '</div>'
                                        . '<div style="max-height:240px;overflow-y:auto;border:1px solid var(--gray-200);border-radius:8px">'
                                        . '<table style="width:100%;border-collapse:collapse">'
                                        . '<thead><tr style="background:var(--gray-50)">'
                                        . '<th style="padding:5px 10px;text-align:left;font-size:11px">SKU dự kiến</th>'
                                        . '<th style="padding:5px 10px;text-align:left;font-size:11px">Tổ hợp</th>'
                                        . '<th style="padding:5px 10px;text-align:left;font-size:11px">Trạng thái</th>'
                                        . '</tr></thead><tbody>' . $rows . '</tbody></table></div>'
                                );
                            })
                            ->columnSpanFull();

                        return $fields;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate biến thể')
                    ->modalDescription(
                        fn() =>
                        $this->getOwnerRecord()->variants()->count() > 0
                            ? 'Sản phẩm đã có ' . $this->getOwnerRecord()->variants()->count() . ' biến thể — chỉ tổ hợp mới sẽ được thêm vào.'
                            : 'Chọn giá trị thuộc tính, xem trước bảng tổ hợp rồi xác nhận.'
                    )
                    ->modalWidth('3xl')
                    ->modalSubmitActionLabel('Xác nhận tạo')
                    ->action(function (array $data): void {
                        $product      = $this->getOwnerRecord();
                        $defaultPrice = (float) ($data['default_price'] ?? 0);
                        $defaultStock = (int) ($data['default_stock'] ?? 0);
                        $skuPrefix    = strtoupper(Str::slug(
                            $product->base_sku ?: $product->code
                        ));

                        $groups = collect($data)
                            ->except(['default_price', 'default_stock'])
                            ->filter(fn($v) => ! empty($v))
                            ->map(fn($v) => array_map('intval', (array) $v))
                            ->values()->toArray();

                        if (empty($groups)) {
                            Notification::make()->title('Chưa chọn giá trị thuộc tính nào')->warning()->send();
                            return;
                        }

                        $combinations   = $this->cartesian($groups);
                        $existingCombos = $this->getExistingCombos($product);
                        $existingSkus   = ProductVariant::pluck('sku')
                            ->map(fn($s) => strtoupper($s))
                            ->flip()->toArray();

                        $created = 0;
                        $skipped = 0;

                        try {
                            DB::transaction(function () use (
                                $combinations,
                                &$existingCombos,
                                &$existingSkus,
                                $skuPrefix,
                                $defaultPrice,
                                $defaultStock,
                                $product,
                                &$created,
                                &$skipped
                            ) {
                                // Load tất cả AttributeValue cần thiết 1 lần
                                $allIds   = collect($combinations)->flatten()->unique()->values();
                                $valueMap = AttributeValue::whereIn('id', $allIds)
                                    ->get()
                                    ->keyBy('id');

                                foreach ($combinations as $combo) {
                                    $comboIds = collect($combo)->map(fn($id) => (int) $id)
                                        ->sort()->values()->toArray();

                                    if (in_array($comboIds, $existingCombos)) {
                                        $skipped++;
                                        continue;
                                    }

                                    // Sort theo attribute_id rồi sort_order → SKU nhất quán
                                    $sortedValues = collect($comboIds)
                                        ->map(fn($id) => $valueMap->get($id))
                                        ->filter()
                                        ->sortBy(fn($v) => [$v->attribute_id, $v->sort_order]);

                                    $valueLabels = $sortedValues->map(fn($v) => Str::slug($v->value))->implode('-');
                                    $baseSku     = strtoupper($skuPrefix . '-' . $valueLabels);
                                    $sku         = $baseSku;
                                    $i           = 2;
                                    while (isset($existingSkus[$sku])) {
                                        $sku = $baseSku . '-' . $i++;
                                    }

                                    $variant = ProductVariant::create([
                                        'product_id'     => $product->id,
                                        'sku'            => $sku,
                                        'price'          => $defaultPrice,
                                        'stock_quantity' => $defaultStock,
                                        'image'          => $this->findColorLinkedImage($product, $comboIds),
                                        'status'         => true,
                                        // ── FIX: variant đầu tiên được tạo → set làm default ──
                                        // Flatsome cũng tự chọn variation đầu tiên làm mặc định.
                                        // $created === 0 nghĩa là đây là variant đầu tiên trong batch này.
                                        // Chỉ set khi SP chưa có variant nào trước đó (existingCombos rỗng).
                                        'is_default'     => $created === 0 && empty($existingCombos),
                                    ]);

                                    $variant->attributeValues()->attach($comboIds);
                                    $existingCombos[] = $comboIds;
                                    $existingSkus[$sku] = true;
                                    $created++;
                                }
                            });
                        } catch (\Throwable $e) {
                            report($e);
                            Notification::make()
                                ->title('Generate thất bại — đã hủy toàn bộ thay đổi')
                                ->body($e->getMessage())
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
                            $msg .= ", bỏ qua {$skipped} tổ hợp đã có";
                        }
                        Notification::make()->title($msg)->success()->send();
                    }),

                // ── Sửa giá & kho hàng loạt ──────────────────────────────────
                Action::make('bulkEditAll')
                    ->label('Sửa giá & kho')
                    ->icon('heroicon-o-pencil-square')
                    ->color('info')
                    ->visible(fn() => $this->getOwnerRecord()->variants()->exists())
                    ->form(function (): array {
                        $product  = $this->getOwnerRecord();
                        $variants = $product->variants()
                            ->with('attributeValues.attribute')
                            ->orderBy('id')
                            ->get();

                        $fields = [
                            Placeholder::make('hint')
                                ->hiddenLabel()
                                ->content(new \Illuminate\Support\HtmlString(
                                    '<div style="font-size:13px;color:var(--gray-500)">Chỉnh giá và tồn kho cho từng biến thể — bấm "Lưu tất cả" 1 lần.</div>'
                                )),
                        ];

                        foreach ($variants as $v) {
                            $label = $v->attribute_label ?: $v->sku;

                            // ── FIX: hiển thị badge "Đặt trước" nếu manage_stock = false ──
                            // Admin cần biết variant nào không cần nhập tồn kho.
                            $manageBadge = ! $v->manage_stock
                                ? ' <span style="background:#f3f4f6;color:#6b7280;padding:1px 7px;border-radius:10px;font-size:11px;font-weight:400">Đặt trước</span>'
                                : '';

                            $fields[] = Placeholder::make("header_{$v->id}")
                                ->hiddenLabel()
                                ->content(new \Illuminate\Support\HtmlString(
                                    '<div style="font-weight:600;font-size:13px;padding:6px 0 2px;border-top:1px solid var(--gray-200);margin-top:4px">'
                                        . e($label)
                                        . ' <span style="font-weight:400;color:var(--gray-400);font-size:11px">(' . e($v->sku) . ')</span>'
                                        . $manageBadge
                                        . '</div>'
                                ))
                                ->columnSpanFull();

                            $fields[] = TextInput::make("rows.{$v->id}.price")
                                ->label('Giá bán (₫)')->numeric()->prefix('₫')->minValue(0)
                                ->default((string) $v->price);

                            $fields[] = TextInput::make("rows.{$v->id}.compare_price")
                                ->label('Giá gốc (₫)')->numeric()->prefix('₫')->minValue(0)->nullable()
                                ->default($v->compare_price !== null ? (string) $v->compare_price : null);

                            $fields[] = TextInput::make("rows.{$v->id}.stock_quantity")
                                ->label('Tồn kho')->numeric()->minValue(0)
                                ->default((string) $v->stock_quantity);
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

                            $price        = $row['price'] !== '' ? (float) $row['price'] : $variant->price;
                            $comparePrice = ($row['compare_price'] ?? '') !== '' ? (float) $row['compare_price'] : null;
                            $stock        = $row['stock_quantity'] !== '' ? (int) $row['stock_quantity'] : $variant->stock_quantity;

                            if (! self::isValidComparePrice($comparePrice, $price)) {
                                $errors[] = "SKU {$variant->sku}: giá gốc phải lớn hơn giá bán — bỏ qua.";
                                continue;
                            }

                            $variant->update([
                                'price'          => $price,
                                'compare_price'  => $comparePrice,
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

                        Notification::make()->title("Đã cập nhật {$updated} biến thể")->success()->send();
                    }),

                // ── Xóa tất cả & tạo lại ─────────────────────────────────────
                Action::make('deleteAllVariants')
                    ->label('Xóa tất cả & tạo lại')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Xóa toàn bộ biến thể?')
                    ->modalDescription('Hành động này xóa TẤT CẢ biến thể hiện tại và không thể hoàn tác. Dùng khi cần đổi cấu trúc thuộc tính.')
                    ->modalSubmitActionLabel('Xóa tất cả')
                    ->visible(fn() => $this->getOwnerRecord()->variants()->exists())
                    ->disabled(fn() => (bool) $this->getOwnerRecord()->status)
                    ->tooltip(
                        fn() => $this->getOwnerRecord()->status
                            ? 'Tắt hiển thị sản phẩm trước khi xóa toàn bộ biến thể'
                            : null
                    )
                    ->action(function (): void {
                        $product = $this->getOwnerRecord();

                        if ($product->status) {
                            Notification::make()
                                ->title('Không thể xóa')
                                ->body('Sản phẩm đang hiển thị cho khách. Tắt hiển thị trước.')
                                ->danger()->send();
                            return;
                        }

                        try {
                            $count = DB::transaction(function () use ($product) {
                                $variants = $product->variants()->get();
                                $count    = $variants->count();

                                // Xóa file ảnh trước (không thể làm hàng loạt)
                                foreach ($variants as $variant) {
                                    if ($variant->image) {
                                        Storage::disk('public')->delete($variant->image);
                                    }
                                }

                                // ── FIX: xóa pivot + variants bằng query trực tiếp ──
                                // Không dùng $variant->delete() từng record vì mỗi lần xóa
                                // ProductVariant::booted()::deleted sẽ chạy và cố gán
                                // is_default cho variant tiếp theo → N query thừa + race condition.
                                // Query trực tiếp trên Builder không trigger booted() events.
                                $variantIds = $variants->pluck('id');
                                \App\Models\VariantAttributeValue::whereIn('variant_id', $variantIds)->delete();
                                $product->variants()->delete(); // Builder::delete() — không trigger Model events

                                return $count;
                            });

                            Notification::make()
                                ->title("Đã xóa {$count} biến thể")
                                ->body('Dùng "Generate biến thể" để tạo lại.')
                                ->success()->send();
                        } catch (QueryException $e) {
                            if ($e->getCode() === '23000') {
                                Notification::make()
                                    ->title('Không thể xóa')
                                    ->body('Một hoặc nhiều biến thể đang được tham chiếu trong đơn hàng. Hủy / hoàn thành đơn hàng trước.')
                                    ->danger()->send();
                            } else {
                                report($e);
                                Notification::make()->title('Lỗi không xác định')->body($e->getMessage())->danger()->send();
                            }
                        }
                    }),

                CreateAction::make()
                    ->label('Thêm thủ công')
                    ->mutateFormDataUsing(fn(array $data) => $this->applyColorLinkedImage($data)),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateFormDataUsing(fn(array $data) => $this->applyColorLinkedImage($data)),

                DeleteAction::make()
                    ->before(function ($record, DeleteAction $action) {
                        $product = $record->product;

                        if ($product->status && $product->variants()->count() <= 1) {
                            Notification::make()
                                ->title('Không thể xóa biến thể cuối cùng')
                                ->body('Sản phẩm đang hiển thị. Tắt hiển thị trước hoặc thêm biến thể khác.')
                                ->danger()->send();
                            $action->cancel();
                            return;
                        }

                        try {
                            if ($record->image) {
                                Storage::disk('public')->delete($record->image);
                            }
                        } catch (\Throwable) {
                            // Bỏ qua lỗi xóa file — record vẫn phải được xóa
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Sửa giá / kho cho nhiều variant đã tick
                    BulkAction::make('bulkEditPrice')
                        ->label('Sửa giá & kho hàng loạt')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('info')
                        ->form([
                            TextInput::make('price')
                                ->label('Giá bán mới (₫)')->numeric()->prefix('₫')->minValue(0)
                                ->helperText('Để trống = giữ nguyên'),
                            TextInput::make('compare_price')
                                ->label('Giá gốc mới (₫)')->numeric()->prefix('₫')->minValue(0)->nullable()
                                ->helperText('Để trống = giữ nguyên'),
                            TextInput::make('stock_quantity')
                                ->label('Tồn kho mới')->numeric()->minValue(0)
                                ->helperText('Để trống = giữ nguyên'),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $newPrice   = $data['price'] !== '' && $data['price'] !== null ? (float) $data['price'] : null;
                            $newCompare = ($data['compare_price'] ?? '') !== '' ? (float) $data['compare_price'] : null;
                            $newStock   = ($data['stock_quantity'] ?? '') !== '' ? (int) $data['stock_quantity'] : null;

                            if ($newPrice === null && $newCompare === null && $newStock === null) {
                                Notification::make()->title('Chưa nhập giá trị nào để cập nhật')->warning()->send();
                                return;
                            }

                            $skipped = 0;
                            foreach ($records as $record) {
                                $effectivePrice   = $newPrice ?? $record->price;
                                $effectiveCompare = $newCompare ?? $record->compare_price;

                                if (! self::isValidComparePrice($effectiveCompare, $effectivePrice)) {
                                    $skipped++;
                                    continue;
                                }

                                $update = array_filter([
                                    'price'          => $newPrice,
                                    'compare_price'  => $newCompare,
                                    'stock_quantity' => $newStock,
                                ], fn($v) => $v !== null);

                                $record->update($update);
                            }

                            $updated = $records->count() - $skipped;
                            $msg     = "Đã cập nhật {$updated} biến thể";
                            if ($skipped) {
                                $msg .= ", bỏ qua {$skipped} (giá gốc ≤ giá bán)";
                            }
                            Notification::make()->title($msg)->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Tăng/giảm % giá
                    BulkAction::make('bulkAdjustPricePercent')
                        ->label('Điều chỉnh giá theo %')
                        ->icon('heroicon-o-receipt-percent')
                        ->color('warning')
                        ->form([
                            Radio::make('direction')
                                ->label('Hướng')
                                ->options(['increase' => 'Tăng giá', 'decrease' => 'Giảm giá'])
                                ->default('increase')
                                ->required()
                                ->inline(),
                            TextInput::make('percent')
                                ->label('Tỷ lệ (%)')->numeric()->required()
                                ->minValue(0.01)->maxValue(100)->suffix('%'),
                            Toggle::make('apply_to_compare_price')
                                ->label('Áp dụng cả cho Giá gốc')
                                ->default(false),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $factor   = $data['direction'] === 'increase'
                                ? (1 + $data['percent'] / 100)
                                : (1 - $data['percent'] / 100);
                            $applyCmp = (bool) ($data['apply_to_compare_price'] ?? false);
                            $updated  = 0;
                            $skipped  = [];

                            foreach ($records as $record) {
                                $newPrice = (int) round($record->price * $factor, -2);

                                if ($newPrice <= 0) {
                                    $skipped[] = $record->sku;
                                    continue;
                                }

                                $update = ['price' => $newPrice];

                                if ($applyCmp && $record->compare_price) {
                                    $newCompare = (int) round($record->compare_price * $factor, -2);
                                    // Đảm bảo giá gốc sau điều chỉnh vẫn > giá bán mới
                                    $update['compare_price'] = $newCompare > $newPrice ? $newCompare : null;
                                }

                                $record->update($update);
                                $updated++;
                            }

                            $title = ($data['direction'] === 'increase' ? 'Tăng' : 'Giảm')
                                . " {$data['percent']}% cho {$updated} biến thể";

                            if (! empty($skipped)) {
                                Notification::make()
                                    ->title($title . ', bỏ qua ' . count($skipped))
                                    ->body('Giá sau điều chỉnh ≤ 0: ' . implode(', ', $skipped))
                                    ->warning()->send();
                                return;
                            }

                            Notification::make()->title($title)->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    /**
     * Lấy danh sách tổ hợp attribute_value_id của các variant hiện có.
     * Dùng chung cho generate và buildVariantPreview.
     */
    private function getExistingCombos($product): array
    {
        return $product->variants()->with('attributeValues')->get()
            ->map(fn($v) => $v->attributeValues->pluck('id')
                ->map(fn($id) => (int) $id)->sort()->values()->toArray())
            ->toArray();
    }

    /**
     * Tìm ảnh kế thừa từ variant khác cùng màu hoặc từ thư viện ảnh.
     * Ưu tiên: 1) variant khác cùng màu đã có ảnh, 2) thư viện ảnh gắn màu.
     */
    private function findColorLinkedImage($product, array $valueIds, ?int $excludeVariantId = null): ?string
    {
        if (empty($valueIds)) {
            return null;
        }

        $colorValueIds = AttributeValue::whereIn('id', $valueIds)
            ->whereHas('attribute', fn($q) => $q->where('display_type', 1))
            ->pluck('id');

        if ($colorValueIds->isEmpty()) {
            return null;
        }

        // Ưu tiên 1: variant khác cùng màu có ảnh riêng
        $variant = $product->variants()
            ->whereNotNull('image')
            ->when($excludeVariantId, fn($q) => $q->where('id', '!=', $excludeVariantId))
            ->whereHas('attributeValues', fn($q) => $q->whereIn('attribute_values.id', $colorValueIds))
            ->first();

        if ($variant?->image) {
            return $variant->image;
        }

        // Ưu tiên 2: thư viện ảnh sản phẩm gắn màu này
        return $product->images()
            ->whereIn('attribute_value_id', $colorValueIds)
            ->orderBy('is_primary', 'desc')
            ->orderBy('sort_order')
            ->value('image_url');
    }

    /**
     * Nếu admin không upload ảnh, tự kế thừa ảnh theo màu (nếu có).
     * Nếu admin muốn xóa ảnh: trả về null và không override.
     */
    private function applyColorLinkedImage(array $data): array
    {
        if (! empty($data['image'])) {
            return $data; // admin tự upload → không can thiệp
        }

        $product  = $this->getOwnerRecord();
        $valueIds = $data['attributeValues'] ?? [];
        $linked   = $this->findColorLinkedImage($product, $valueIds);

        if ($linked) {
            $data['image'] = $linked;
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

    private function buildVariantPreview($product, callable $get): array
    {
        $attributes = $product->attributes()->with('attributeValues')->get();

        $groups = $attributes
            ->map(fn($attr) => $get("attribute_{$attr->id}") ?? [])
            ->filter(fn($v) => ! empty($v))
            ->map(fn($v) => array_map('intval', (array) $v))
            ->values()->toArray();

        if (empty($groups)) {
            return ['groups' => [], 'rows' => [], 'total' => 0, 'willCreate' => 0, 'willSkip' => 0];
        }

        $skuPrefix = strtoupper(Str::slug($product->base_sku ?: $product->code));

        $combinations   = $this->cartesian($groups);
        $existingCombos = $this->getExistingCombos($product);

        $existingSkus = ProductVariant::pluck('sku')
            ->map(fn($s) => strtoupper($s))
            ->flip()->toArray();

        // Load tất cả AttributeValue cần thiết 1 lần — tránh N+1
        $allIds   = collect($combinations)->flatten()->unique()->values();
        $valueMap = AttributeValue::whereIn('id', $allIds)
            ->with('attribute')
            ->get()
            ->keyBy('id');

        $rows       = [];
        $willCreate = 0;
        $willSkip   = 0;

        foreach ($combinations as $combo) {
            $comboIds = collect($combo)->map(fn($id) => (int) $id)->sort()->values()->toArray();
            $exists   = in_array($comboIds, $existingCombos);

            $values = collect($comboIds)
                ->map(fn($id) => $valueMap->get($id))
                ->filter()
                ->sortBy(fn($v) => [$v->attribute_id, $v->sort_order]);

            $label       = $values->map(fn($v) => $v->attribute->name . ': ' . $v->value)->join(' / ');
            $valueLabels = $values->map(fn($v) => Str::slug($v->value))->implode('-');
            $baseSku     = strtoupper($skuPrefix . '-' . $valueLabels);

            if ($exists) {
                $willSkip++;
            } else {
                $sku = $baseSku;
                $i   = 2;
                while (isset($existingSkus[$sku])) {
                    $sku = $baseSku . '-' . $i++;
                }
                $existingSkus[$sku] = true;
                $existingCombos[]   = $comboIds;
                $baseSku            = $sku;
                $willCreate++;
            }

            $rows[] = ['sku' => $baseSku, 'label' => $label, 'exists' => $exists];
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
