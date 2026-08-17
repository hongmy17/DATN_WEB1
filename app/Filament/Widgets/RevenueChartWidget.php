<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Doanh thu';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 2;

    // Bộ lọc hiển thị ở góc widget — cho phép đổi qua lại NGÀY / THÁNG
    protected function getFilters(): ?array
    {
        return [
            '7d'  => '7 ngày qua',
            '30d' => '30 ngày qua',
            '12m' => '12 tháng qua',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? '30d';

        if ($filter === '12m') {
            return $this->dataByMonth();
        }

        $days = $filter === '7d' ? 7 : 30;
        return $this->dataByDay($days);
    }

    private function dataByDay(int $days): array
    {
        $labels = [];
        $values = [];

        foreach (range($days - 1, 0) as $daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            $revenue = Order::revenue()
                ->whereDate('created_at', $date)
                ->sum('total_amount');

            $labels[] = $date->format('d/m');
            $values[] = round($revenue / 1000); // đơn vị nghìn đồng cho trục gọn hơn
        }

        return [
            'datasets' => [[
                'label' => 'Doanh thu (nghìn ₫)',
                'data' => $values,
                'borderColor' => '#f59e0b',
                'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                'fill' => true,
                'tension' => 0.3,
            ]],
            'labels' => $labels,
        ];
    }

    private function dataByMonth(): array
    {
        $labels = [];
        $values = [];

        foreach (range(11, 0) as $monthsAgo) {
            $month = Carbon::now()->subMonths($monthsAgo);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $revenue = Order::revenue()
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_amount');

            $labels[] = 'Th.' . $month->format('n/Y');
            $values[] = round($revenue / 1000);
        }

        return [
            'datasets' => [[
                'label' => 'Doanh thu (nghìn ₫)',
                'data' => $values,
                'borderColor' => '#f59e0b',
                'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                'fill' => true,
                'tension' => 0.3,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}