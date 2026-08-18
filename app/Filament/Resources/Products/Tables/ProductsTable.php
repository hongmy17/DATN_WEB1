<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
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
                    ->disk('public')
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

                TextColumn::make('sold_count')
                    ->label('Đã bán (30 ngày)')
                    ->getStateUsing(fn($record) => \App\Models\Product::soldCountsMap()[$record->id] ?? 0)
                    ->numeric()
                    ->alignEnd()
                    ->badge()
                    ->color('success')
                    ->sortable(false), // không sort được vì đây là giá trị tính toán, không phải cột thật trong DB
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

                // ─── BỘ LỌC THÙNG RÁC ───────────────────────────────────────
                // TrashedFilter là bộ lọc dựng sẵn của Filament dành riêng cho
                // model có SoftDeletes. Ba lựa chọn:
                //   - (mặc định) chỉ sản phẩm đang hoạt động
                //   - "Chỉ mục đã xóa" → xem thùng rác
                //   - "Tất cả" → gộp cả hai
                TrashedFilter::make()
                    ->label('Thùng rác'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Sửa')
                    // Không cho sửa sản phẩm đang nằm trong thùng rác —
                    // phải khôi phục trước rồi mới sửa.
                    ->visible(fn($record) => ! $record->trashed()),

                // ─── XÓA MỀM ────────────────────────────────────────────────
                // Trước đây bảng này KHÔNG có nút xóa: muốn xóa phải mở trang
                // Sửa rồi mới thấy nút ở header. Nay đưa thẳng ra danh sách.
                //
                // Nút chỉ hiện khi sản phẩm ĐANG TẮT hiển thị. Lý do: không để
                // sản phẩm biến mất đột ngột khỏi trang chủ trong lúc khách
                // đang xem. Cùng triết lý với danh mục — ẩn nút thay vì cho bấm
                // rồi mới báo lỗi.
                DeleteAction::make()
                    ->label('Chuyển vào thùng rác')
                    ->modalDescription('Sản phẩm vẫn được giữ trong database và có thể khôi phục sau.')
                    ->visible(fn($record) => ! $record->trashed() && ! $record->status),

                // Khôi phục: đưa sản phẩm từ thùng rác trở lại hoạt động
                // (chỉ đơn giản là gán deleted_at = NULL).
                RestoreAction::make()
                    ->label('Khôi phục'),

                // Xóa vĩnh viễn: chạy DELETE thật, KHÔNG khôi phục được.
                // Chỉ hiện với sản phẩm đã nằm trong thùng rác — tức là admin
                // buộc phải xóa mềm trước, xóa cứng sau (quy tắc "hai lần bấm").
                ForceDeleteAction::make()
                    ->label('Xóa vĩnh viễn')
                    ->requiresConfirmation()
                    ->modalHeading('Xóa vĩnh viễn sản phẩm')
                    ->modalDescription('Hành động này KHÔNG THỂ hoàn tác. Dữ liệu sẽ bị xóa khỏi database.')
                    ->before(function ($record, ForceDeleteAction $action) {
                        // Chặn xóa cứng nếu sản phẩm đã từng được đặt mua:
                        // order_items trỏ tới product_variants của sản phẩm này
                        // với ràng buộc khóa ngoại, xóa cứng sẽ ném lỗi SQL và
                        // phá vỡ lịch sử đơn hàng.
                        $hasOrders = \App\Models\OrderItem::whereIn(
                            'variant_id',
                            $record->variants()->withTrashed()->pluck('id')
                        )->exists();

                        if ($hasOrders) {
                            Notification::make()
                                ->title('Không thể xóa vĩnh viễn')
                                ->body('Sản phẩm này đã từng được đặt mua. Xóa cứng sẽ phá vỡ lịch sử đơn hàng.')
                                ->danger()
                                ->persistent()
                                ->send();
                            $action->cancel();
                        }
                    }),
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

                    DeleteBulkAction::make()->label('Chuyển vào thùng rác'),
                    RestoreBulkAction::make()->label('Khôi phục'),
                    ForceDeleteBulkAction::make()->label('Xóa vĩnh viễn'),
                ]),
            ]);
    }
}
