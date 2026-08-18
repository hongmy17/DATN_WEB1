<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Cùng quy tắc với CategoriesTable: ẩn nút khi danh mục còn ràng buộc.
            // Ở trang Edit chỉ có DUY NHẤT 1 bản ghi nên gọi exists() không gây N+1.
            DeleteAction::make()
                ->label('Chuyển vào thùng rác')
                ->visible(fn($record) => ! $record->trashed()
                    && ! $record->products()->exists()
                    && ! $record->children()->exists())
                ->before(function ($record, $action) {
                    if ($record->children()->exists()) {
                        Notification::make()
                            ->title('Không thể xóa!')
                            ->body('Danh mục đang có danh mục con.')
                            ->danger()
                            ->send();
                        $action->halt();
                    }

                    if ($record->products()->exists()) {
                        Notification::make()
                            ->title('Không thể xóa!')
                            ->body('Danh mục đang có sản phẩm.')
                            ->danger()
                            ->send();
                        $action->halt();
                    }
                }),

            RestoreAction::make()->label('Khôi phục'),

            ForceDeleteAction::make()
                ->label('Xóa vĩnh viễn')
                ->requiresConfirmation()
                ->before(function ($record, ForceDeleteAction $action) {
                    if ($record->products()->withTrashed()->exists()) {
                        Notification::make()
                            ->title('Không thể xóa vĩnh viễn')
                            ->body('Danh mục vẫn còn sản phẩm (kể cả sản phẩm trong thùng rác).')
                            ->danger()
                            ->persistent()
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
