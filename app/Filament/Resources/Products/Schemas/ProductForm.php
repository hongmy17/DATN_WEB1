<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Attribute;
use App\Models\AttributeTemplate;
use App\Models\AttributeValue;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // ── FIX: code sinh theo tên sản phẩm: MBP14M3P-001 ──
            TextInput::make('code')
                ->label('Mã sản phẩm')
                ->required()
                ->maxLength(20)
                ->unique(ignoreRecord: true)
                ->disabled()
                ->dehydrated()
                ->default(function ($get) {
                    $name = $get('name');
                    return !empty($name) ? self::generateNextCode($name) : self::generateNextCode();
                })
                ->helperText('Tự động sinh theo tên sản phẩm khi nhập tên, VD: MBP14M3P-001'),

            TextInput::make('base_sku')
                ->label('SKU gốc')
                ->maxLength(50)
                ->placeholder('VD: MACBOOK-PRO-14')
                ->helperText('Biến thể sẽ tự ghép SKU này + thuộc tính nếu không nhập SKU riêng'),

            TextInput::make('name')
                ->label('Tên sản phẩm')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set, $livewire) {
                    $set('slug', Str::slug($state));
                    
                    // Cập nhật code khi tên thay đổi
                    if (!empty($state)) {
                        $code = self::generateNextCode($state);
                        $set('code', $code);
                    }
                }),

            TextInput::make('slug')
                ->label('Slug URL')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(200),

            Select::make('category_id')
                ->label('Danh mục')
                ->relationship('category', 'name')
                ->required()
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    if (! $state) {
                        return;
                    }

                    $currentAttrs = $get('selectedAttributes');
                    if (! empty($currentAttrs)) {
                        return;
                    }

                    $template = AttributeTemplate::where('category_id', $state)
                        ->with('items')
                        ->first();

                    if (! $template) {
                        return;
                    }

                    $attributeIds = $template->items
                        ->pluck('attribute_id')
                        ->filter()
                        ->values()
                        ->toArray();

                    if (! empty($attributeIds)) {
                        $set('selectedAttributes', $attributeIds);
                    }
                }),

            Select::make('selectedAttributes')
                ->label('Thuộc tính sản phẩm')
                ->relationship('attributes', 'name')
                ->multiple()
                ->preload()
                ->searchable()
                ->live()

                // ── FIX: nút quản lý giá trị cho các thuộc tính ĐÃ chọn ─────────
                // Không cần thoát sang AttributeResource để thêm "Xanh" vào
                // attribute "Màu sắc" đã có sẵn — bấm thẳng ở đây.
                ->hintAction(
                    Action::make('manageAttributeValues')
                        ->label('Quản lý giá trị')
                        ->icon('heroicon-o-pencil-square')
                        ->visible(fn (callable $get) => ! empty($get('selectedAttributes')))
                        ->form(function (callable $get) {
                            $attributeIds = $get('selectedAttributes') ?? [];
                            $attributes   = Attribute::whereIn('id', $attributeIds)
                                ->with('attributeValues')
                                ->get();

                            if ($attributes->isEmpty()) {
                                return [
                                    Placeholder::make('empty')
                                        ->label('')
                                        ->content('Chưa chọn thuộc tính nào.'),
                                ];
                            }

                            // Mỗi attribute đã chọn → 1 Select để chọn xem, 1 Repeater để sửa value
                            return $attributes->map(function ($attribute) {
                                return \Filament\Schemas\Components\Section::make($attribute->name)
                                    ->description($attribute->display_type === 1
                                        ? 'Thuộc tính màu sắc — có thể thêm mã màu'
                                        : 'Thuộc tính dạng text')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make("attr_values_{$attribute->id}")
                                            ->label('Giá trị')
                                            ->default(
                                                $attribute->attributeValues
                                                    ->sortBy('sort_order')
                                                    ->map(fn ($v) => [
                                                        'id'         => $v->id,
                                                        'value'      => $v->value,
                                                        'color_code' => $v->color_code,
                                                    ])->values()->toArray()
                                            )
                                            ->schema([
                                                \Filament\Forms\Components\Hidden::make('id'),

                                                TextInput::make('value')
                                                    ->label('Giá trị')
                                                    ->required()
                                                    ->placeholder('VD: Xanh lá, 1TB...'),

                                                ColorPicker::make('color_code')
                                                    ->label('Mã màu')
                                                    ->visible(fn () => $attribute->display_type === 1),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('+ Thêm giá trị mới')
                                            ->reorderable()
                                            ->deletable(true), // xóa value cũ ngay tại đây
                                    ]);
                            })->toArray();
                        })
                        ->modalHeading('Quản lý giá trị thuộc tính')
                        ->modalWidth('2xl')
                        ->modalSubmitActionLabel('Lưu thay đổi')
                        ->action(function (array $data, callable $get): void {
                            $attributeIds = $get('selectedAttributes') ?? [];

                            DB::transaction(function () use ($data, $attributeIds) {
                                foreach ($attributeIds as $attrId) {
                                    $rows = $data["attr_values_{$attrId}"] ?? [];
                                    $keptIds = [];

                                    foreach ($rows as $index => $row) {
                                        if (empty(trim($row['value'] ?? ''))) {
                                            continue;
                                        }

                                        if (! empty($row['id'])) {
                                            // value cũ → update
                                            AttributeValue::where('id', $row['id'])->update([
                                                'value'      => trim($row['value']),
                                                'color_code' => $row['color_code'] ?? null,
                                                'sort_order' => $index + 1,
                                            ]);
                                            $keptIds[] = (int) $row['id'];
                                        } else {
                                            // value mới → create
                                            $new = AttributeValue::create([
                                                'attribute_id' => $attrId,
                                                'value'        => trim($row['value']),
                                                'color_code'   => $row['color_code'] ?? null,
                                                'sort_order'   => $index + 1,
                                            ]);
                                            $keptIds[] = $new->id;
                                        }
                                    }

                                    // Value nào bị xóa khỏi Repeater (admin bấm nút xóa)
                                    // → xóa khỏi DB, NHƯNG chỉ khi chưa gắn vào variant nào
                                    // để tránh xóa nhầm value đang được dùng thật.
                                    AttributeValue::where('attribute_id', $attrId)
                                        ->whereNotIn('id', $keptIds)
                                        ->whereDoesntHave('variantAttributeValues')
                                        ->delete();
                                }
                            });

                            Notification::make()
                                ->title('Đã lưu thay đổi giá trị thuộc tính')
                                ->success()
                                ->send();
                        })
                )

                // ── Modal tạo thuộc tính mới — đầy đủ như trang Attributes ──────
                ->createOptionForm([
                    TextInput::make('name')
                        ->label('Tên thuộc tính')
                        ->required()
                        ->maxLength(100)
                        ->placeholder('VD: Màu sắc, Dung lượng, Kích cỡ...'),

                    Select::make('display_type')
                        ->label('Kiểu hiển thị')
                        ->options([
                            0 => 'Text (chip)  —  VD: S / M / L / XL',
                            1 => 'Màu sắc (color swatch)  —  hiển thị ô màu tròn',
                        ])
                        ->default(0)
                        ->required()
                        ->live()
                        ->helperText('Chọn "Màu sắc" để hiện thêm ô chọn màu bên dưới'),

                    // Repeater giá trị — dùng key 'values' (không phải relationship)
                    // vì đây là modal tạo mới, chưa có record ID để dùng ->relationship()
                    Repeater::make('values')
                        ->label('Danh sách giá trị')
                        ->schema([
                            TextInput::make('value')
                                ->label('Giá trị')
                                ->required()
                                ->placeholder('VD: Đỏ, 512GB, Size M...'),

                            // Ô màu chỉ hiện khi display_type = 1 (màu sắc)
                            // Path '../../display_type': lên 1 cấp (repeater item) → lên 1 cấp (form root)
                            ColorPicker::make('color_code')
                                ->label('Mã màu')
                                ->live()
                                ->visible(fn (callable $get) => (int) $get('../../display_type') === 1),
                        ])
                        ->columns(2)
                        ->defaultItems(1)
                        ->addActionLabel('+ Thêm giá trị')
                        ->reorderable()
                        ->collapsible(),
                ])

                // ── Tự xử lý lưu: tạo Attribute + tất cả AttributeValues ────────
                ->createOptionUsing(function (array $data): int {
                    return DB::transaction(function () use ($data): int {
                        $attribute = Attribute::create([
                            'name'         => $data['name'],
                            'display_type' => (int) ($data['display_type'] ?? 0),
                        ]);

                        $values = $data['values'] ?? [];
                        foreach ($values as $index => $item) {
                            if (empty(trim($item['value'] ?? ''))) {
                                continue; // bỏ qua dòng trống
                            }

                            AttributeValue::create([
                                'attribute_id' => $attribute->id,
                                'value'        => trim($item['value']),
                                'color_code'   => $item['color_code'] ?? null,
                                'sort_order'   => $index + 1,
                            ]);
                        }

                        return $attribute->id;
                        // Filament tự động chọn ID này vào Select sau khi modal đóng
                    });
                })

                ->createOptionModalHeading('Tạo thuộc tính mới')

                ->helperText(function (callable $get) {
                    $categoryId = $get('category_id');
                    if (! $categoryId) {
                        return 'Chọn danh mục để tự động điền thuộc tính từ mẫu. Nhấn + để tạo thuộc tính mới kèm giá trị ngay tại đây.';
                    }

                    $template = AttributeTemplate::where('category_id', $categoryId)->first();
                    if (! $template) {
                        return 'Danh mục này chưa có mẫu thuộc tính. Chọn thủ công hoặc nhấn + để tạo mới.';
                    }

                    $mappedCount = $template->items()->whereNotNull('attribute_id')->count();
                    $totalCount  = $template->items()->count();

                    if ($mappedCount === 0) {
                        return "Mẫu \"{$template->name}\" chưa map thuộc tính. Vào Attribute Templates để cập nhật.";
                    }

                    return "Đã tự động điền từ mẫu \"{$template->name}\" ({$mappedCount}/{$totalCount} thuộc tính). Nhấn + để tạo thêm thuộc tính mới.";
                }),

            Textarea::make('short_description')
                ->label('Mô tả ngắn')
                ->rows(2)
                ->maxLength(500),

            RichEditor::make('description')
                ->label('Mô tả chi tiết')
                ->columnSpanFull(),

            FileUpload::make('thumbnail')
                ->label('Ảnh đại diện')
                ->image()
                ->directory('products/thumbnails')
                ->imagePreviewHeight('200')
                ->nullable()
                ->helperText('Nếu để trống, hệ thống sẽ dùng ảnh chính trong Thư viện ảnh khi hiển thị ra client'),

            Toggle::make('status')
                ->label('Hiển thị sản phẩm')
                ->default(false)
                ->live()
                ->helperText('Tắt để ẩn sản phẩm khỏi trang khách hàng')
                ->rules([
                    function ($get, $record) {
                        return function (string $attribute, $value, $fail) use ($record) {
                            if (! $value) {
                                return;
                            }

                            if (! $record) {
                                $fail('Hãy lưu sản phẩm và thêm ảnh + biến thể trước khi bật hiển thị.');
                                return;
                            }

                            if (! $record->images()->exists()) {
                                $fail('Sản phẩm chưa có ảnh nào. Vào tab "Thư viện ảnh" để thêm trước khi hiển thị.');
                                return;
                            }

                            if (! $record->variants()->exists()) {
                                $fail('Sản phẩm chưa có biến thể nào. Vào tab "Biến thể sản phẩm" để generate trước khi hiển thị.');
                            }
                        };
                    },
                ]),

            Placeholder::make('publish_readiness')
                ->label('')
                ->content(function ($record) {
                    if (! $record) {
                        return '💡 Lưu sản phẩm trước, sau đó thêm ảnh và biến thể để có thể bật hiển thị.';
                    }

                    $hasImages   = $record->images()->exists();
                    $hasVariants = $record->variants()->exists();

                    if ($hasImages && $hasVariants) {
                        return '✅ Sản phẩm đã đủ ảnh và biến thể, có thể hiển thị cho khách.';
                    }

                    $missing = [];
                    if (! $hasImages) {
                        $missing[] = 'ảnh';
                    }
                    if (! $hasVariants) {
                        $missing[] = 'biến thể';
                    }

                    return '⚠️ Sản phẩm còn thiếu: ' . implode(', ', $missing) . '. Chưa nên bật hiển thị.';
                })
                ->visible(fn ($record) => true),
        ]);
    }

    /**
     * Tự sinh mã sản phẩm theo format: Viết tắt chữ đầu của tên + số thứ tự
     * VD: "MacBook Pro 14" M3 Pro" → MBP14M3P-001
     */
    private static function generateNextCode(?string $productName = ''): string
    {
        // Nếu chưa có tên, tạo mã tạm thời PRD + số
        if (empty($productName)) {
            $lastCode = \App\Models\Product::withTrashed()
                ->where('code', 'like', 'PRD%')
                ->orderByRaw('CAST(SUBSTRING(code, 4) AS UNSIGNED) DESC')
                ->value('code');
            
            $nextNumber = $lastCode 
                ? ((int) substr($lastCode, 3)) + 1 
                : 1;
                
            return 'PRD' . str_pad((string) $nextNumber, 7, '0', STR_PAD_LEFT);
        }
        
        // Loại bỏ các ký tự đặc biệt
        $cleanName = preg_replace('/["\'\`\(\)\[\]\{\}]/', '', $productName);
        $cleanName = trim($cleanName);
        
        // Nếu tên chỉ có số
        if (is_numeric($cleanName)) {
            return 'PRD' . str_pad($cleanName, 7, '0', STR_PAD_LEFT);
        }
        
        // 1. Tạo mã viết tắt từ tên sản phẩm
        $abbreviation = self::generateAbbreviation($cleanName);
        
        // Nếu abbreviation rỗng hoặc chỉ có số
        if (empty($abbreviation) || is_numeric($abbreviation)) {
            $lastCode = \App\Models\Product::withTrashed()
                ->where('code', 'like', 'PRD%')
                ->orderByRaw('CAST(SUBSTRING(code, 4) AS UNSIGNED) DESC')
                ->value('code');
            
            $nextNumber = $lastCode 
                ? ((int) substr($lastCode, 3)) + 1 
                : 1;
                
            return 'PRD' . str_pad((string) $nextNumber, 7, '0', STR_PAD_LEFT);
        }
        
        // 2. Lấy số thứ tự tiếp theo cho abbreviation này
        $lastCode = \App\Models\Product::withTrashed()
            ->where('code', 'like', $abbreviation . '%')
            ->orderByRaw('CAST(SUBSTRING(code, ' . (strlen($abbreviation) + 2) . ') AS UNSIGNED) DESC')
            ->value('code');
        
        $nextNumber = $lastCode 
            ? ((int) substr($lastCode, strlen($abbreviation) + 1)) + 1 
            : 1;
        
        // 3. Ghép abbreviation + số (3 chữ số, padding 0)
        return $abbreviation . '-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Tạo mã viết tắt từ tên sản phẩm
     * - Lấy chữ cái đầu của mỗi từ
     * - Giữ nguyên số (nếu có)
     * - Bỏ qua các từ đặc biệt (Pro, Max, Ultra... thành P, M, U)
     * - Tối đa 10 ký tự
     */
    private static function generateAbbreviation(string $name): string
    {
        // Loại bỏ dấu nháy, dấu ngoặc, ký tự đặc biệt
        $name = preg_replace('/["\'\`\(\)\[\]\{\}]/', '', $name);
        
        // Chuẩn hóa: loại bỏ dấu, chuyển về không dấu
        $name = self::removeAccents($name);
        
        // Tách từ: loại bỏ các ký tự đặc biệt, chỉ giữ chữ và số
        $words = preg_split('/[\s\-_]+/', $name);
        $words = array_filter($words, fn($w) => !empty(trim($w)));
        
        $abbreviation = '';
        $specialWords = [
            'pro' => 'P',
            'max' => 'M',
            'ultra' => 'U',
            'plus' => 'P',
            'mini' => 'M',
            'air' => 'A',
            'studio' => 'S',
            'edge' => 'E',
        ];
        
        foreach ($words as $word) {
            // Nếu từ là số (hoặc có dạng số như M3, M4...)
            if (preg_match('/^[0-9]+$/', $word)) {
                // Giữ nguyên số
                $abbreviation .= $word;
                continue;
            }
            
            // Nếu từ có dạng chữ + số (VD: M3, M4, A14...)
            if (preg_match('/^([A-Za-z])([0-9]+)$/', $word, $matches)) {
                // Lấy chữ cái + số: M3 → M3
                $abbreviation .= $matches[1] . $matches[2];
                continue;
            }
            
            // Nếu từ có dạng số + chữ (VD: 14M)
            if (preg_match('/^([0-9]+)([A-Za-z])$/', $word, $matches)) {
                $abbreviation .= $matches[1] . $matches[2];
                continue;
            }
            
            // Nếu từ là từ đặc biệt, dùng ký tự viết tắt đã định nghĩa
            $lowerWord = strtolower($word);
            if (isset($specialWords[$lowerWord])) {
                $abbreviation .= $specialWords[$lowerWord];
                continue;
            }
            
            // Nếu từ có 1 ký tự, lấy ký tự đó
            if (strlen($word) === 1) {
                $abbreviation .= strtoupper($word);
                continue;
            }
            
            // Lấy ký tự đầu tiên
            $abbreviation .= strtoupper($word[0]);
        }
        
        // Giới hạn tối đa 10 ký tự cho abbreviation
        return substr($abbreviation, 0, 10);
    }

    /**
     * Loại bỏ dấu tiếng Việt
     */
    private static function removeAccents(string $str): string
    {
        $unaccented = preg_replace(
            [
                '/[àáạảãâầấậẩẫăằắặẳẵ]/u',
                '/[èéẹẻẽêềếệểễ]/u',
                '/[ìíịỉĩ]/u',
                '/[òóọỏõôồốộổỗơờớợởỡ]/u',
                '/[ùúụủũưừứựửữ]/u',
                '/[ỳýỵỷỹ]/u',
                '/đ/u',
            ],
            [
                'a', 'e', 'i', 'o', 'u', 'y', 'd'
            ],
            $str
        );
        
        return $unaccented;
    }
}