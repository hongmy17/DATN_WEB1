<?php
namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Loại')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state == 0 ? 'Theo %' : 'Cố định')
                    ->color(fn($state) => $state == 0 ? 'info' : 'warning'),

                TextColumn::make('value')
                    ->label('Giá trị')
                    ->formatStateUsing(fn($state, $record) =>
                        $record->type == 0 ? $state.'%' : number_format($state).'₫'
                    ),

                TextColumn::make('min_order_value')
                    ->label('Đơn tối thiểu')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('used_count')
                    ->label('Đã dùng')
                    ->sortable()
                    ->formatStateUsing(fn($state, $record) =>
                        $record->max_usage ? $state.'/'.$record->max_usage : $state.'/ ∞'
                    ),

                TextColumn::make('start_date')
                    ->label('Bắt đầu')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Kết thúc')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                IconColumn::make('status')
                    ->label('Trạng thái')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Loại giảm giá')
                    ->options([
                        0 => 'Theo %',
                        1 => 'Cố định',
                    ]),

                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        1 => 'Kích hoạt',
                        0 => 'Vô hiệu',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}