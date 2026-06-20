<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label('Mã sản phẩm')
                ->required()
                ->maxLength(10)
                ->unique(ignoreRecord: true)
                ->placeholder('VD: PRD0000001'),

            // Thêm: base_sku để variant kế thừa khi tạo thủ công không nhập SKU riêng
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
                ->afterStateUpdated(fn ($state, callable $set) =>
                    $set('slug', Str::slug($state))
                ),

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
                ->preload(),

            Select::make('selectedAttributes')
                ->label('Thuộc tính sản phẩm')
                ->relationship('attributes', 'name')
                ->multiple()
                ->preload()
                ->searchable()
                ->helperText('Chọn các thuộc tính dùng để tạo biến thể (VD: Màu sắc, Dung lượng)'),

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

            // FIX QUAN TRỌNG: validate không cho publish nếu chưa đủ điều kiện
            Toggle::make('status')
                ->label('Hiển thị sản phẩm')
                ->default(false) // mặc định OFF — admin phải chủ động bật sau khi đủ điều kiện
                ->live()
                ->helperText('Tắt để ẩn sản phẩm khỏi trang khách hàng')
                ->rules([
                    function ($get, $record) {
                        return function (string $attribute, $value, $fail) use ($record) {
                            if (! $value) {
                                return; // tắt hiển thị luôn được phép
                            }

                            // record null = đang tạo mới, chưa thể có variant/ảnh
                            // → chặn bật status khi tạo mới, bắt buộc lưu trước rồi mới publish
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

            // Hiện trạng thái sẵn sàng ngay trong form để admin biết thiếu gì
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
}