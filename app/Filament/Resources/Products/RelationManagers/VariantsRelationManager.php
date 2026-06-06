<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

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
                ->placeholder('VD: MBP14-BLK-512')
                ->helperText('Mã định danh duy nhất cho biến thể này'),

            TextInput::make('price')
                ->label('Giá bán (₫)')
                ->required()
                ->numeric()
                ->prefix('₫')
                ->minValue(0),

            TextInput::make('compare_price')
                ->label('Giá gốc / Giá so sánh (₫)')
                ->numeric()
                ->prefix('₫')
                ->nullable()
                ->helperText('Để trống nếu không muốn hiển thị giá gạch ngang'),

            TextInput::make('stock_quantity')
                ->label('Tồn kho')
                ->required()
                ->numeric()
                ->default(0)
                ->minValue(0),

            Select::make('attributeValues')
                ->label('Thuộc tính biến thể')
                ->multiple()
                ->relationship('attributeValues', 'value')
                ->getOptionLabelFromRecordUsing(fn($record) => "{$record->attribute->name}: {$record->value}")
                ->preload()
                ->helperText('Chọn màu sắc, dung lượng... cho biến thể này'),

            FileUpload::make('image')
                ->label('Ảnh riêng biến thể')
                ->image()
                ->directory('products/variants')
                ->imagePreviewHeight('120')
                ->nullable()
                ->helperText('Để trống nếu dùng ảnh chung của sản phẩm'),

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

                TextColumn::make('attributeValues.value')
                    ->label('Thuộc tính')
                    ->badge()
                    ->separator(','),

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
                    ->color(fn($state) => match (true) {
                        $state === 0  => 'danger',
                        $state < 5    => 'warning',
                        default       => 'success',
                    }),

                ToggleColumn::make('status')
                    ->label('Đang bán'),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()->label('Thêm biến thể'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
