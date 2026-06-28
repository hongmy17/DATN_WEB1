<?php
namespace App\Filament\Resources\Attributes\Pages;
use App\Filament\Resources\Attributes\AttributeResource;
use Filament\Actions\DeleteAction; use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
class EditAttribute extends EditRecord {
    protected static string $resource = AttributeResource::class;
    protected function getHeaderActions(): array {
        return [
            DeleteAction::make()->before(function ($record, DeleteAction $action) {
                if ($record->attributeValues()->whereHas("variantAttributeValues")->exists()) {
                    Notification::make()->title("Không thể xóa")->body("Đang có biến thể sử dụng thuộc tính này.")->danger()->send();
                    $action->cancel();
                }
            }),
        ];
    }
}
