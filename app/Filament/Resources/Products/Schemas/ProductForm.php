<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
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

            // FIX: Đổi tên field từ 'attributes' → 'selectedAttributes'
            // để tránh conflict với tên relation 'attributes' trên Model Product.
            // Dùng ->relationship() để Filament tự sync pivot table product_attributes.
            Select::make('selectedAttributes')
                ->label('Thuộc tính sản phẩm')
                ->relationship('attributes', 'name') // vẫn dùng relation đúng
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
                ->nullable(),

            Toggle::make('status')
                ->label('Hiển thị sản phẩm')
                ->default(true)
                ->helperText('Tắt để ẩn sản phẩm khỏi trang khách hàng'),
        ]);
    }
}