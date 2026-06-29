<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Mã đơn')
                    ->formatStateUsing(fn($state) => 'ĐH-' . str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('receiver_name')
                    ->label('Người nhận')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('receiver_phone')
                    ->label('Số điện thoại')
                    ->searchable(),

                TextColumn::make('shipping_address')
                    ->label('Địa chỉ')
                    ->wrap()
                    ->limit(50),

                TextColumn::make('total_amount')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('coupon.coupon_code')
                    ->label('Mã giảm giá')
                    ->placeholder('—')
                    ->badge()
                    ->color('success'),

                SelectColumn::make('order_status')
                    ->label('Trạng thái')
                    ->options([
                        0 => '🟡 Chờ xác nhận',
                        1 => '🔵 Đã xác nhận',
                        2 => '🟠 Đang giao',
                        3 => '🟢 Hoàn thành',
                        4 => '🔴 Đã hủy',
                    ]),

                TextColumn::make('created_at')
                    ->label('Ngày đặt')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('order_status')
                    ->label('Trạng thái')
                    ->placeholder('Tất cả')
                    ->options([
                        0 => 'Chờ xác nhận',
                        1 => 'Đã xác nhận',
                        2 => 'Đang giao',
                        3 => 'Hoàn thành',
                        4 => 'Đã hủy',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Xem chi tiết')
                    ->modal()
                    ->modalHeading(fn($record) => 'Chi tiết đơn hàng ĐH-' . str_pad($record->id, 5, '0', STR_PAD_LEFT))
                    ->modalWidth('2xl')
                    ->form([
                        Section::make('📍 Thông tin người nhận')
                            ->columns(2)
                            ->schema([
                                Placeholder::make('receiver_name')
                                    ->label('Họ và tên')
                                    ->content(fn($record) => $record->receiver_name),

                                Placeholder::make('receiver_phone')
                                    ->label('Số điện thoại')
                                    ->content(fn($record) => $record->receiver_phone),

                                Placeholder::make('address')
                                    ->label('Địa chỉ giao hàng')
                                    ->content(fn($record) => $record->shipping_address
                                        ?: ($record->address_detail . ', ' . $record->ward . ', ' . $record->district . ', ' . $record->province))
                                    ->columnSpanFull(),

                                Placeholder::make('note')
                                    ->label('Ghi chú')
                                    ->content(fn($record) => $record->note ?: '(Không có)')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('💰 Thông tin thanh toán')
                            ->columns(3)
                            ->schema([
                                Placeholder::make('subtotal')
                                    ->label('Tạm tính')
                                    ->content(fn($record) => number_format($record->subtotal ?? $record->sub_total ?? 0) . '₫'),
                                Placeholder::make('discount_amount')
                                    ->label('Giảm giá')
                                    ->content(fn($record) => '-' . number_format($record->discount_amount) . '₫'),

                                Placeholder::make('total_amount')
                                    ->label('Tổng cộng')
                                    ->content(fn($record) => number_format($record->total_amount) . '₫'),

                                Placeholder::make('coupon_code')
                                    ->label('Mã giảm giá')
                                    ->content(fn($record) => $record->coupon_code ?: '(Không có)')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('📦 Trạng thái đơn hàng')
                            ->schema([
                                Placeholder::make('order_status')
                                    ->label('Trạng thái')
                                    ->content(fn($record) => match ((int)$record->order_status) {
                                        0 => '🟡 Chờ xác nhận',
                                        1 => '🔵 Đã xác nhận',
                                        2 => '🟠 Đang giao',
                                        3 => '🟢 Hoàn thành',
                                        4 => '🔴 Đã hủy',
                                        default => 'Không xác định',
                                    }),
                            ]),
                    ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa'),
                ]),
            ]);
    }
}
