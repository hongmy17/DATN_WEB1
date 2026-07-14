<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function ($record, $action) {
                    // FIX: xem giải thích ở CategoriesTable.php — halt() phải gọi SAU
                    // khi đã send() notification, không phải trước.
                    if ($record->children()->exists()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Không thể xóa!')
                            ->body('Danh mục đang có danh mục con.')
                            ->danger()
                            ->send();
                        $action->halt();
                    }

                    if ($record->products()->exists()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Không thể xóa!')
                            ->body('Danh mục đang có sản phẩm.')
                            ->danger()
                            ->send();
                        $action->halt();
                    }
                }),
        ];
    }
}