<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopProductsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Top sản phẩm bán chạy (30 ngày qua)')
            ->records(function () {
                return DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.created_at', '>=', now()->subDays(30))
                    // FIX: cùng quy tắc "doanh thu thực thu" với Order::scopeRevenue() —
                    // COD chỉ tính khi Hoàn thành, thanh toán online tính từ Đã xác nhận.
                    // Không gọi được scope Eloquent ở đây vì đang dùng DB::table thô
                    // (cần JOIN + GROUP BY trên order_items, không phải trên chính bảng orders).
                    ->where(function ($q) {
                        $q->where(function ($cod) {
                            $cod->where('orders.payment_method', 'cod')
                                ->where('orders.order_status', Order::STATUS_COMPLETED);
                        })->orWhere(function ($online) {
                            $online->where('orders.payment_method', '!=', 'cod')
                                ->whereIn('orders.order_status', [
                                    Order::STATUS_CONFIRMED,
                                    Order::STATUS_SHIPPING,
                                    Order::STATUS_COMPLETED,
                                ]);
                        });
                    })
                    ->select(
                        'order_items.product_name',
                        DB::raw('SUM(order_items.quantity) as total_qty'),
                        DB::raw('SUM(order_items.total_price) as total_revenue'),
                    )
                    ->groupBy('order_items.product_name')
                    ->orderByDesc('total_qty')
                    ->limit(10)
                    ->get()
                    ->map(fn ($row, $i) => [
                        'id' => $i + 1, // key giả để Filament table có id duy nhất (đây không phải Eloquent model thật)
                        'product_name' => $row->product_name,
                        'total_qty' => $row->total_qty,
                        'total_revenue' => $row->total_revenue,
                    ]);
            })
            ->columns([
                TextColumn::make('product_name')
                    ->label('Sản phẩm')
                    ->wrap()
                    ->weight('medium'),
                TextColumn::make('total_qty')
                    ->label('Đã bán')
                    ->numeric()
                    ->alignEnd()
                    ->badge()
                    ->color('success'),
                TextColumn::make('total_revenue')
                    ->label('Doanh thu')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.') . '₫')
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}