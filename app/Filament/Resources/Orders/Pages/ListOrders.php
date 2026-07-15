<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    // FIX: Livewire KHÔNG gửi lại query string gốc khi chuyển trang/sort (AJAX).
    // Phải đọc request() ĐÚNG 1 LẦN lúc tải trang (mount) rồi lưu vào thuộc tính
    // Livewire — Livewire sẽ tự giữ giá trị này qua mọi lần AJAX tiếp theo.
    public ?string $quickFilter = null;

    public function mount(): void
    {
        parent::mount();
        $this->quickFilter = request()->query('quick');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if ($this->quickFilter === 'pending') {
            $query->whereIn('order_status', [
                Order::STATUS_PENDING,
                Order::STATUS_CANCEL_REQUESTED,
            ]);
        }

        return $query;
    }
}
