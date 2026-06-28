<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function ($record, DeleteAction $action) {
                    // Không cho xóa sản phẩm đang hiển thị
                    if ($record->status) {
                        \Filament\Notifications\Notification::make()
                            ->title('Không thể xóa sản phẩm đang hiển thị')
                            ->body('Tắt hiển thị trước khi xóa.')
                            ->danger()
                            ->send();
                        $action->cancel();
                    }
                }),

            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
