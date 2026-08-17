<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\RefundRequests\RefundRequestResource;
use App\Models\RefundRequest;

class OrderStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $revenueToday = Order::revenue()
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $revenueMonth = Order::revenue()
            ->where('created_at', '>=', $monthStart)
            ->sum('total_amount');

        $ordersToday = Order::whereDate('created_at', $today)->count();

        // Đơn cần admin xử lý: chờ xác nhận (0) + chờ xác nhận hủy (6)
        $needAttention = Order::whereIn('order_status', [
            Order::STATUS_PENDING,
            Order::STATUS_CANCEL_REQUESTED,
        ])->count();

        $pendingRefunds = RefundRequest::where('status', RefundRequest::STATUS_PENDING)->count();

        // Sparkline doanh thu 7 ngày gần nhất cho thẻ "Doanh thu tháng này"
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
            return Order::revenue()
                ->whereDate('created_at', Carbon::today()->subDays($daysAgo))
                ->sum('total_amount');
        })->toArray();

        return [
            Stat::make('Doanh thu hôm nay', number_format($revenueToday, 0, ',', '.') . '₫')
                ->color('success'),

            Stat::make('Doanh thu tháng này', number_format($revenueMonth, 0, ',', '.') . '₫')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($last7Days)
                ->chartColor('success')
                ->color('success'),

            Stat::make('Đơn mới hôm nay', $ordersToday)
                ->color('info')
                ->url(OrderResource::getUrl('index')),

            Stat::make('Đơn cần xử lý', $needAttention)
                ->description('Chờ xác nhận / Chờ xác nhận hủy')
                ->color($needAttention > 0 ? 'warning' : 'success')
                ->url(OrderResource::getUrl('index', ['quick' => 'pending'])),

            Stat::make('Yêu cầu hoàn tiền', $pendingRefunds)
                ->description('Đang chờ xử lý')
                ->color($pendingRefunds > 0 ? 'danger' : 'success')
                ->url(RefundRequestResource::getUrl('index', [
                    'tableFilters' => ['status' => ['value' => RefundRequest::STATUS_PENDING]],
                ])),
        ];
    }
}
