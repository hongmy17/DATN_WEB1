<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Attribute;
use App\Models\AttributeTemplate;
use App\Models\AttributeValue;
use App\Models\Product;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // ── Thông tin cơ bản ──────────────────────────────────────────────
            Section::make('Thông tin cơ bản')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Tên sản phẩm')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! empty($state)) {
                                    $set('slug', Str::slug($state));
                                    $set('code', self::generateNextCode($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('Slug URL')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(200)
                            ->helperText('Tự động tạo từ tên — có thể chỉnh tay'),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('code')
                            ->label('Mã sản phẩm')
                            ->required()
                            ->maxLength(15)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Tự động sinh, tối đa 15 ký tự'),

                        TextInput::make('base_sku')
                            ->label('SKU gốc')
                            ->maxLength(50)
                            ->placeholder('VD: MACBOOK-PRO-14')
                            ->helperText('Biến thể sẽ ghép SKU này + thuộc tính'),
                    ]),

                    Select::make('category_id')
                        ->label('Danh mục')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set, callable $get, $record) {
                            if (! $state) {
                                return;
                            }

                            // Cảnh báo nếu sản phẩm đã có biến thể
                            if ($record?->variants()->exists()) {
                                Notification::make()
                                    ->title('Sản phẩm đã có biến thể')
                                    ->body('Đổi danh mục có thể làm lệch thuộc tính biến thể hiện tại. Kiểm tra lại tab "Biến thể" sau khi lưu.')
                                    ->warning()
                                    ->persistent()
                                    ->send();
                            }

                            // Tự điền thuộc tính từ template nếu chưa chọn
                            if (! empty($get('selectedAttributes'))) {
                                return;
                            }

                            $template = AttributeTemplate::where('category_id', $state)
                                ->with('items')
                                ->first();

                            $attributeIds = $template?->items
                                ->pluck('attribute_id')
                                ->filter()
                                ->values()
                                ->toArray();

                            if (! empty($attributeIds)) {
                                $set('selectedAttributes', $attributeIds);
                            }
                        }),
                ]),

            // ── Mô tả ─────────────────────────────────────────────────────────
            Section::make('Mô tả sản phẩm')
                ->icon('heroicon-o-document-text')
                ->collapsed()
                ->schema([
                    Textarea::make('short_description')
                        ->label('Mô tả ngắn')
                        ->rows(2)
                        ->maxLength(500),

                    RichEditor::make('description')
                        ->label('Mô tả chi tiết')
                        ->columnSpanFull(),
                ]),

            // ── Ảnh & thuộc tính biến thể ─────────────────────────────────────
            Section::make('Ảnh & Thuộc tính')
                ->icon('heroicon-o-photo')
                ->schema([
                    FileUpload::make('thumbnail')
                        ->label('Ảnh đại diện')
                        ->image()
                        ->directory('products/thumbnails')
                        ->imagePreviewHeight('160')
                        ->nullable()
                        ->helperText('Nếu để trống, hệ thống dùng ảnh chính trong Thư viện ảnh'),

                    Select::make('selectedAttributes')
                        ->label('Thuộc tính biến thể')
                        ->relationship('attributes', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->live()
                        ->helperText(function (callable $get) {
                            $categoryId = $get('category_id');
                            if (! $categoryId) {
                                return 'Chọn danh mục trước để tự động điền từ mẫu, hoặc nhấn + để tạo thuộc tính mới.';
                            }
                            $template    = AttributeTemplate::where('category_id', $categoryId)->first();
                            $mappedCount = $template?->items()->whereNotNull('attribute_id')->count() ?? 0;
                            if (! $template || $mappedCount === 0) {
                                return 'Danh mục này chưa có mẫu thuộc tính. Chọn thủ công hoặc nhấn + để tạo mới.';
                            }
                            return "Đã điền từ mẫu \"{$template->name}\" ({$mappedCount} thuộc tính). Nhấn + để tạo thêm.";
                        })
                        // Chặn gỡ attribute đang dùng bởi variant
                        ->rules([
                            function ($get, $record) {
                                return function (string $attribute, $value, $fail) use ($record) {
                                    if (! $record) {
                                        return;
                                    }

                                    $removed = array_diff(
                                        $record->attributes()->pluck('attributes.id')->map(fn ($id) => (int) $id)->toArray(),
                                        collect($value ?? [])->map(fn ($id) => (int) $id)->toArray()
                                    );

                                    if (empty($removed)) {
                                        return;
                                    }

                                    $usedNames = Attribute::whereIn('id', $removed)
                                        ->whereHas('attributeValues.variantAttributeValues', fn ($q) =>
                                            $q->whereHas('variant', fn ($vq) => $vq->where('product_id', $record->id))
                                        )
                                        ->pluck('name');

                                    if ($usedNames->isNotEmpty()) {
                                        $fail(
                                            'Không thể gỡ "' . $usedNames->join(', ') . '" — đang có biến thể sử dụng. '
                                            . 'Xóa biến thể liên quan trước (tab "Biến thể" → "Xóa tất cả & tạo lại").'
                                        );
                                    }
                                };
                            },
                        ])
                        // Tạo thuộc tính mới ngay trong form
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Tên thuộc tính')
                                ->required()
                                ->maxLength(100)
                                ->placeholder('VD: Màu sắc, Dung lượng, Kích cỡ...'),

                            Select::make('display_type')
                                ->label('Kiểu hiển thị')
                                ->options([
                                    0 => 'Text (chip)  —  S / M / L / XL',
                                    1 => 'Màu sắc (color swatch)',
                                ])
                                ->default(0)
                                ->required()
                                ->live(),

                            Repeater::make('values')
                                ->label('Giá trị ban đầu')
                                ->schema([
                                    TextInput::make('value')
                                        ->label('Giá trị')
                                        ->required()
                                        ->placeholder('VD: Đỏ, 512GB...'),

                                    ColorPicker::make('color_code')
                                        ->label('Mã màu')
                                        ->visible(fn (callable $get) => (int) $get('../../display_type') === 1),
                                ])
                                ->columns(2)
                                ->defaultItems(1)
                                ->addActionLabel('+ Thêm giá trị')
                                ->reorderable(),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            return DB::transaction(function () use ($data): int {
                                $attribute = Attribute::create([
                                    'name'         => $data['name'],
                                    'display_type' => (int) ($data['display_type'] ?? 0),
                                ]);

                                foreach ($data['values'] ?? [] as $index => $item) {
                                    if (empty(trim($item['value'] ?? ''))) {
                                        continue;
                                    }
                                    AttributeValue::create([
                                        'attribute_id' => $attribute->id,
                                        'value'        => trim($item['value']),
                                        'color_code'   => $item['color_code'] ?? null,
                                        'sort_order'   => $index + 1,
                                    ]);
                                }

                                return $attribute->id;
                            });
                        })
                        ->createOptionModalHeading('Tạo thuộc tính mới'),
                ]),

            // ── Trạng thái & Checklist publish ────────────────────────────────
            Section::make('Trạng thái')
                ->icon('heroicon-o-check-circle')
                ->schema([
                    Placeholder::make('publish_readiness')
                        ->label('')
                        ->content(function ($record) {
                            if (! $record) {
                                return new \Illuminate\Support\HtmlString(
                                    '<div style="padding:10px 14px;background:var(--warning-50,#fefce8);border:1px solid var(--warning-200,#fde047);border-radius:8px;font-size:13px;color:#854d0e">'
                                    . '💡 Lưu sản phẩm trước, sau đó thêm <b>ảnh</b> và <b>biến thể</b> để có thể bật hiển thị.'
                                    . '</div>'
                                );
                            }

                            $hasImages   = $record->images()->exists();
                            $hasVariants = $record->variants()->exists();

                            if ($hasImages && $hasVariants) {
                                return new \Illuminate\Support\HtmlString(
                                    '<div style="padding:10px 14px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;font-size:13px;color:#166534">'
                                    . '✅ Sản phẩm đủ điều kiện hiển thị cho khách.'
                                    . '</div>'
                                );
                            }

                            $missing = collect([
                                $hasImages   ? null : '📷 Thư viện ảnh còn trống',
                                $hasVariants ? null : '📦 Chưa có biến thể nào',
                            ])->filter()->join(' &nbsp;·&nbsp; ');

                            return new \Illuminate\Support\HtmlString(
                                '<div style="padding:10px 14px;background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;font-size:13px;color:#9a3412">'
                                . '⚠️ Còn thiếu: ' . $missing
                                . '</div>'
                            );
                        }),

                    Toggle::make('status')
                        ->label('Hiển thị sản phẩm cho khách')
                        ->default(false)
                        ->helperText('Bật khi đã có đủ ảnh và biến thể')
                        ->rules([
                            function ($record) {
                                return function (string $attribute, $value, $fail) use ($record) {
                                    if (! $value) {
                                        return;
                                    }
                                    if (! $record) {
                                        $fail('Lưu sản phẩm trước, rồi thêm ảnh và biến thể trước khi bật hiển thị.');
                                        return;
                                    }
                                    if (! $record->images()->exists()) {
                                        $fail('Chưa có ảnh — vào tab "Thư viện ảnh" để thêm trước.');
                                        return;
                                    }
                                    if (! $record->variants()->exists()) {
                                        $fail('Chưa có biến thể — vào tab "Biến thể" để tạo trước.');
                                    }
                                };
                            },
                        ]),
                ]),
        ]);
    }

    // ── Sinh mã sản phẩm ─────────────────────────────────────────────────────

    private static function generateNextCode(?string $productName = ''): string
    {
        if (empty($productName)) {
            return self::buildCode('PRD', 3);
        }

        $abbr = self::generateAbbreviation($productName);

        if (empty($abbr) || is_numeric($abbr)) {
            return self::buildCode('PRD', 3);
        }

        return self::buildCode($abbr, strlen($abbr) + 1);
    }

    /**
     * Tìm số thứ tự tiếp theo cho prefix rồi ghép lại, hard-cap tổng ≤ 15 ký tự.
     */
    private static function buildCode(string $prefix, int $prefixLen): string
    {
        $suffix    = '-' . str_pad('1', 3, '0', STR_PAD_LEFT);
        $maxPreLen = 15 - strlen($suffix); // tổng ≤ 15
        $prefix    = substr($prefix, 0, max($maxPreLen, 1));

        $last = Product::withTrashed()
            ->where('code', 'like', $prefix . '-%')
            ->orderByRaw('CAST(SUBSTRING(code, ' . (strlen($prefix) + 2) . ') AS UNSIGNED) DESC')
            ->value('code');

        $next = $last ? ((int) substr($last, strlen($prefix) + 1)) + 1 : 1;

        return $prefix . '-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    private static function generateAbbreviation(string $name): string
    {
        $name = preg_replace('/["\'`()\[\]{}]/u', '', $name);
        $name = self::removeAccents($name);

        $specialWords = [
            'pro' => 'P', 'max' => 'M', 'ultra' => 'U',
            'plus' => 'P', 'mini' => 'M', 'air' => 'A',
            'studio' => 'S', 'edge' => 'E',
        ];

        $words  = preg_split('/[\s\-_]+/', $name);
        $abbrev = '';

        foreach (array_filter($words) as $word) {
            if (preg_match('/^[0-9]+$/', $word)) {
                $abbrev .= $word;
            } elseif (preg_match('/^([A-Za-z])([0-9]+)$/', $word, $m)) {
                $abbrev .= $m[1] . $m[2];
            } elseif (preg_match('/^([0-9]+)([A-Za-z])$/', $word, $m)) {
                $abbrev .= $m[1] . $m[2];
            } elseif (isset($specialWords[strtolower($word)])) {
                $abbrev .= $specialWords[strtolower($word)];
            } else {
                $abbrev .= strtoupper($word[0]);
            }
        }

        return substr($abbrev, 0, 10);
    }

    private static function removeAccents(string $str): string
    {
        return preg_replace(
            ['/[àáạảãâầấậẩẫăằắặẳẵ]/u', '/[èéẹẻẽêềếệểễ]/u', '/[ìíịỉĩ]/u',
             '/[òóọỏõôồốộổỗơờớợởỡ]/u', '/[ùúụủũưừứựửữ]/u', '/[ỳýỵỷỹ]/u', '/đ/u'],
            ['a', 'e', 'i', 'o', 'u', 'y', 'd'],
            $str
        );
    }
}
