<?php

namespace App\Filament\Resources\Coupons\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('coupon_code')
                    ->label('Mã giảm giá')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép mã!')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Loại')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state == 0 ? 'Theo %' : 'Cố định')
                    ->color(fn($state) => $state == 0 ? 'info' : 'warning'),

                TextColumn::make('value')
                    ->label('Giá trị')
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->type == 0
                            ? $state . '%'
                            : number_format($state, 0, ',', '.') . '₫'
                    )
                    ->sortable(),

                TextColumn::make('max_discount')
                    ->label('Giảm tối đa')
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->type == 0 && $state
                            ? number_format($state, 0, ',', '.') . '₫'
                            : '—'
                    )
                    ->placeholder('—'),

                TextColumn::make('min_order_value')
                    ->label('Đơn tối thiểu')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('used_count')
                    ->label('Đã dùng / Tối đa')
                    ->sortable()
                    ->formatStateUsing(
                        fn($state, $record) =>
                        $record->max_usage
                            ? $state . ' / ' . $record->max_usage
                            : $state . ' / ∞'
                    ),

                TextColumn::make('start_date')
                    ->label('Bắt đầu')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Kết thúc')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->color(fn($record) => Carbon::now()->gt($record->end_date) ? 'danger' : null),

                // Badge trạng thái thông minh: phân biệt Hoạt động / Hết hạn / Hết lượt / Vô hiệu
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        if ($state != 1) return 'Vô hiệu';
                        if (Carbon::now()->gt($record->end_date)) return 'Hết hạn';
                        if ($record->max_usage !== null && $record->used_count >= $record->max_usage) return 'Hết lượt';
                        return 'Hoạt động';
                    })
                    ->color(function ($state, $record) {
                        if ($state != 1) return 'gray';
                        if (Carbon::now()->gt($record->end_date)) return 'danger';
                        if ($record->max_usage !== null && $record->used_count >= $record->max_usage) return 'warning';
                        return 'success';
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Loại giảm giá')
                    ->options([
                        0 => 'Theo %',
                        1 => 'Cố định',
                    ]),

                TernaryFilter::make('status')
                    ->label('Kích hoạt')
                    ->trueLabel('Kích hoạt')
                    ->falseLabel('Vô hiệu'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
