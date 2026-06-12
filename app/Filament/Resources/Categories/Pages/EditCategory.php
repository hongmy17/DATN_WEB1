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
                    if ($record->children()->exists()) {
                        $action->halt();
                        \Filament\Notifications\Notification::make()
                            ->title('Không thể xóa!')
                            ->body('Danh mục đang có danh mục con.')
                            ->danger()
                            ->send();
                    }

                    if ($record->products()->exists()) {
                        $action->halt();
                        \Filament\Notifications\Notification::make()
                            ->title('Không thể xóa!')
                            ->body('Danh mục đang có sản phẩm.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}