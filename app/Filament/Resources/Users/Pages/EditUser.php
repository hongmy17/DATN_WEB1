<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Order;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Chuyển vào thùng rác')
                ->modalDescription('Tài khoản sẽ không đăng nhập được nữa nhưng đơn hàng và đánh giá vẫn được giữ nguyên.')
                ->before(function ($record, DeleteAction $action) {
                    if ($record->id === auth()->id()) {
                        Notification::make()
                            ->title('Không thể xóa chính tài khoản đang đăng nhập')
                            ->danger()
                            ->send();
                        $action->cancel();
                    }
                }),

            RestoreAction::make()->label('Khôi phục'),

            ForceDeleteAction::make()
                ->label('Xóa vĩnh viễn')
                ->requiresConfirmation()
                ->before(function ($record, ForceDeleteAction $action) {
                    if (Order::where('user_id', $record->id)->exists()) {
                        Notification::make()
                            ->title('Không thể xóa vĩnh viễn')
                            ->body('Tài khoản này đã có đơn hàng.')
                            ->danger()
                            ->persistent()
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
