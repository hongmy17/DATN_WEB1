<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersByStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Đơn hàng theo trạng thái';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    // Nhãn + màu khớp với statusLabel() trong Order model, thêm màu riêng cho từng trạng thái
    private const STATUS_META = [
        Order::STATUS_PENDING          => ['Chờ xác nhận', '#f59e0b'],
        Order::STATUS_CONFIRMED        => ['Đã xác nhận', '#3b82f6'],
        Order::STATUS_SHIPPING         => ['Đang giao', '#fb923c'],
        Order::STATUS_COMPLETED        => ['Hoàn thành', '#22c55e'],
        Order::STATUS_CANCELLED        => ['Đã hủy', '#ef4444'],
        Order::STATUS_AWAITING_PAYMENT => ['Chờ thanh toán', '#06b6d4'],
        Order::STATUS_CANCEL_REQUESTED => ['Chờ xác nhận hủy', '#a855f7'],
        Order::STATUS_REFUNDED         => ['Đã hoàn tiền', '#6b7280'],
    ];

    protected function getFilters(): ?array
    {
        return [
            '30d' => '30 ngày qua',
            'all' => 'Toàn thời gian',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? '30d';

        $query = Order::query();
        if ($filter === '30d') {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        $counts = $query->selectRaw('order_status, count(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status');

        $labels = [];
        $data = [];
        $colors = [];

        foreach (self::STATUS_META as $status => [$label, $color]) {
            $count = $counts[$status] ?? 0;
            if ($count === 0) {
                continue; // ẩn trạng thái không có đơn nào cho biểu đồ gọn hơn
            }
            $labels[] = $label . " ({$count})";
            $data[] = $count;
            $colors[] = $color;
        }

        return [
            'datasets' => [[
                'data' => $data,
                'backgroundColor' => $colors,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}