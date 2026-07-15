<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

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

        // Sparkline doanh thu 7 ngày gần nhất cho thẻ "Doanh thu tháng này"
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
            return Order::revenue()
                ->whereDate('created_at', Carbon::today()->subDays($daysAgo))
                ->sum('total_amount');
        })->toArray();

        return [
            Stat::make('Doanh thu hôm nay', number_format($revenueToday, 0, ',', '.') . '₫')
                ->description('COD: đã giao xong · Online: đã thanh toán')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Doanh thu tháng này', number_format($revenueMonth, 0, ',', '.') . '₫')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($last7Days)
                ->chartColor('success')
                ->color('success'),

            Stat::make('Đơn hàng mới hôm nay', $ordersToday)
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make('Đơn cần xử lý', $needAttention)
                ->description('Chờ xác nhận + chờ xác nhận hủy')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($needAttention > 0 ? 'warning' : 'success'),
        ];
    }
}