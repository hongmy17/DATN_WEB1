<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\OrderItem;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Chuyển vào thùng rác')
                ->modalDescription('Sản phẩm sẽ bị ẩn khỏi website nhưng vẫn giữ nguyên trong database. Có thể khôi phục sau.')
                ->before(function ($record, DeleteAction $action) {
                    // Không cho xóa sản phẩm đang hiển thị — buộc admin phải
                    // tắt hiển thị trước, tránh sản phẩm biến mất đột ngột
                    // khỏi trang chủ trong lúc khách đang xem.
                    if ($record->status) {
                        Notification::make()
                            ->title('Không thể xóa sản phẩm đang hiển thị')
                            ->body('Hãy tắt hiển thị trước khi xóa.')
                            ->danger()
                            ->send();
                        $action->cancel();
                    }
                }),

            // Hai nút dưới đây trước giờ đã có trong code nhưng KHÔNG BAO GIỜ
            // hiện ra được: trang Edit không mở nổi bản ghi đã xóa mềm vì global
            // scope của SoftDeletes lọc mất nó. Nay ProductResource đã gỡ scope
            // đó trong getEloquentQuery() nên chúng mới thực sự hoạt động.
            RestoreAction::make()
                ->label('Khôi phục'),

            ForceDeleteAction::make()
                ->label('Xóa vĩnh viễn')
                ->requiresConfirmation()
                ->modalHeading('Xóa vĩnh viễn sản phẩm')
                ->modalDescription('Hành động này KHÔNG THỂ hoàn tác.')
                ->before(function ($record, ForceDeleteAction $action) {
                    $hasOrders = OrderItem::whereIn(
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
        ];
    }
}
