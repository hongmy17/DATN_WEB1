<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            // ── FIX N+1: Eager-load giá min/max vào query chính ──────────────
            // Trước: getStateUsing(fn ($r) => $r->variants()->min('price'))
            //        → mỗi dòng bắn 1 query riêng → 20 SP = 20 query phụ
            // Sau:   withMin/withMax → Laravel gộp vào 1 subquery JOIN duy nhất
            //        → $record->variants_min_price / variants_max_price có sẵn,
            //          accessor getMinPriceAttribute() trong Product model
            //          sẽ đọc từ đây thay vì query lại.
            ->modifyQueryUsing(
                fn($query) => $query
                    ->withMin('variants', 'price')
                    ->withMax('variants', 'price')
            )
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('')
                    ->size(52)
                    ->defaultImageUrl(asset('images/no-image.png'))
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),

                TextColumn::make('name')
                    ->label('Sản phẩm')
                    ->description(fn($record) => $record->code)
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('variants_count')
                    ->label('Biến thể')
                    ->counts('variants')
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'success' : 'danger')
                    ->alignCenter(),

                // ── Giá từ: đọc từ variants_min_price đã được eager-load ──────
                // withMin() gắn kết quả vào $record->variants_min_price (snake_case).
                // Accessor getMinPriceAttribute() trong Product model tự đọc từ đó.
                // Không còn query phụ nào được bắn.
                TextColumn::make('variants_min_price')
                    ->label('Giá từ')
                    ->money('VND')
                    ->color('success')
                    ->sortable()                     // ← sortable được vì là column DB
                    ->placeholder('Chưa có biến thể'),

                ToggleColumn::make('status')
                    ->label('Hiển thị')
                    ->updateStateUsing(function ($record, $state) {
                        if ($state) {
                            if (! $record->variants()->exists()) {
                                Notification::make()->title('Chưa có biến thể')
                                    ->body('Thêm ít nhất 1 biến thể trước khi bật hiển thị.')
                                    ->warning()->send();
                                return; // không update
                            }
                            if (! $record->images()->exists()) {
                                Notification::make()->title('Chưa có ảnh')
                                    ->body('Thêm ít nhất 1 ảnh trước khi bật hiển thị.')
                                    ->warning()->send();
                                return;
                            }
                        }
                        $record->update(['status' => $state]);
                    }),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Danh mục')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('status')
                    ->label('Trạng thái')
                    ->trueLabel('Đang hiển thị')
                    ->falseLabel('Đang ẩn'),

                TernaryFilter::make('has_variants')
                    ->label('Biến thể')
                    ->trueLabel('Đã có biến thể')
                    ->falseLabel('Chưa có biến thể')
                    ->queries(
                        true: fn($query) => $query->whereHas('variants'),
                        false: fn($query) => $query->whereDoesntHave('variants'),
                    ),
            ])
            ->recordActions([
                EditAction::make()->label('Sửa'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('toggleStatus')
                        ->label('Bật/tắt hiển thị')
                        ->icon('heroicon-o-eye')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->update(['status' => ! $record->status]);
                            }
                            Notification::make()
                                ->title('Đã cập nhật trạng thái ' . $records->count() . ' sản phẩm')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
