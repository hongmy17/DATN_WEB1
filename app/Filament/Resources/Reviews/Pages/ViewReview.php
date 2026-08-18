<?php

namespace App\Filament\Resources\Reviews\Pages;

use App\Filament\Resources\Reviews\ReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReview extends ViewRecord
{
    protected static string $resource = ReviewResource::class;

    /**
     * Resource này không có trang Edit (chỉ có index + view), nên các nút xóa
     * mềm / khôi phục được đặt ở header của trang Xem chi tiết.
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Chuyển vào thùng rác')
                ->visible(fn($record) => ! $record->trashed()),

            RestoreAction::make()
                ->label('Khôi phục'),

            ForceDeleteAction::make()
                ->label('Xóa vĩnh viễn')
                ->requiresConfirmation()
                ->modalHeading('Xóa vĩnh viễn đánh giá')
                ->modalDescription('Đánh giá và toàn bộ phản hồi kèm theo sẽ bị xóa khỏi database. KHÔNG THỂ hoàn tác.'),
        ];
    }
}
