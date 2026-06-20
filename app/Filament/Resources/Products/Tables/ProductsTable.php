<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
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

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // FIX N+1: dùng modifyQueryUsing CHỈ để withMin/withMax/withCount,
            // không override toàn bộ query (TrashedFilter vẫn áp dụng được vì
            // nó tự thêm withTrashed()/onlyTrashed() vào builder ở bước sau).
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withCount('variants')
                ->withMin('variants', 'price')
                ->withMax('variants', 'price')
            )
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

                TextColumn::make('variants_count')
                    ->label('Biến thể')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'info' : 'danger'),

                // FIX: dùng cột đã withMin/withMax sẵn — không query riêng từng dòng
                TextColumn::make('variants_min_price')
                    ->label('Giá')
                    ->money('VND')
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        if (! $record->variants_count) {
                            return '—';
                        }

                        if ($record->variants_min_price == $record->variants_max_price) {
                            return number_format($record->variants_min_price, 0, ',', '.') . '₫';
                        }

                        return number_format($record->variants_min_price, 0, ',', '.')
                            . '₫ - ' . number_format($record->variants_max_price, 0, ',', '.') . '₫';
                    }),

                IconColumn::make('status')
                    ->label('Hiển thị')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash'),

                // Cảnh báo trực quan: publish nhưng thiếu ảnh/biến thể
                IconColumn::make('is_ready_to_publish')
                    ->label('Sẵn sàng')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn ($record) => $record->is_ready_to_publish
                        ? 'Đã có ảnh và biến thể'
                        : 'Thiếu ảnh hoặc biến thể'),

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

                // Thêm: lọc nhanh sản phẩm chưa đủ điều kiện publish
                \Filament\Tables\Filters\Filter::make('not_ready')
                    ->label('Thiếu ảnh/biến thể')
                    ->query(fn (Builder $query) => $query
                        ->where(fn ($q) => $q
                            ->whereDoesntHave('images')
                            ->orWhereDoesntHave('variants')
                        )
                    ),

                TrashedFilter::make()
                    ->label('Sản phẩm đã xóa'),
            ])
            ->recordActions([
                EditAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}