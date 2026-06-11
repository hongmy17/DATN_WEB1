<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // BUG 3 FIX: Bỏ modifyQueryUsing() vì nó override mất query của TrashedFilter.
            // Thay bằng withAggregate/withCount trực tiếp trên Eloquent column,
            // hoặc dùng ->extraAttributes() để eager load riêng.
            // Cách đúng: để Filament tự build query, ta chỉ thêm aggregate qua column.
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Ảnh')
                    ->size(56)
                    ->defaultImageUrl(asset('images/no-image.png')),

                TextColumn::make('code')
                    ->label('Mã SP')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->name),

                TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->badge()
                    ->sortable(),

                // Dùng counts() trên column thay vì modifyQueryUsing
                TextColumn::make('variants_count')
                    ->label('Biến thể')
                    ->counts('variants')
                    ->badge()
                    ->color('info'),

                TextColumn::make('variants_min_price')
                    ->label('Giá từ')
                    ->money('VND')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->variants()->min('price')),

                IconColumn::make('status')
                    ->label('Hiển thị')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash'),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Danh mục')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('status')
                    ->label('Trạng thái hiển thị')
                    ->trueLabel('Đang hiển thị')
                    ->falseLabel('Đang ẩn'),

                TrashedFilter::make()
                    ->label('Sản phẩm đã xóa'),
            ])
            ->recordActions([
                EditAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}