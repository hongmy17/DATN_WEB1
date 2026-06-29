<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
                    ->limit(40)
                    ->wrap(),

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
                    ->modalHeading(fn($record) => 'Chi tiết đơn hàng ĐH-' . str_pad($record->id, 5, '0', STR_PAD_LEFT))
                    ->modalWidth('3xl')
                    ->form([
                        // ── THÔNG TIN NGƯỜI NHẬN ──
                       Placeholder::make('section_receiver')
    ->label('')
    ->hiddenLabel()
    ->content(new \Illuminate\Support\HtmlString('<div style="display:flex;align-items:center;gap:8px;font-weight:800;font-size:15px;padding:8px 0;border-bottom:2px solid #eee;margin-bottom:4px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> Thông tin người nhận</div>')),
                        Placeholder::make('receiver_name')
                            ->label('Họ và tên')
                            ->content(fn($record) => $record->receiver_name),

                        Placeholder::make('receiver_phone')
                            ->label('Số điện thoại')
                            ->content(fn($record) => $record->receiver_phone),

                        Placeholder::make('address')
                            ->label('Địa chỉ giao hàng')
                            ->content(fn($record) => $record->shipping_address ?: '(Không có)'),

                        Placeholder::make('note')
                            ->label('Ghi chú')
                            ->content(fn($record) => $record->note ?: '(Không có)'),

                        // ── SẢN PHẨM ──
                        Placeholder::make('section_items')
    ->label('')
    ->hiddenLabel()
    ->content(new \Illuminate\Support\HtmlString('<div style="display:flex;align-items:center;gap:8px;font-weight:800;font-size:15px;padding:8px 0;border-bottom:2px solid #eee;margin-bottom:4px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg> Sản phẩm đã đặt</div>')),

                        Placeholder::make('items')
                            ->label('')
                            ->content(function($record) {
                                $items = $record->items;
                                if ($items->isEmpty()) return 'Không có sản phẩm';
                                $html = '<table style="width:100%;border-collapse:collapse;font-size:13px">
                                    <tr style="background:#f9f9f9;font-weight:700">
                                        <td style="padding:10px 8px">Sản phẩm</td>
                                        <td style="padding:10px 8px">Biến thể</td>
                                        <td style="padding:10px 8px;text-align:center">SL</td>
                                        <td style="padding:10px 8px;text-align:right">Đơn giá</td>
                                        <td style="padding:10px 8px;text-align:right">Thành tiền</td>
                                    </tr>';
                                foreach ($items as $item) {
                                    $html .= '<tr style="border-top:1px solid #eee">
                                        <td style="padding:10px 8px;font-weight:600">'.$item->product_name.'</td>
                                        <td style="padding:10px 8px;color:#888;font-size:12px">'.$item->variant_description.'</td>
                                        <td style="padding:10px 8px;text-align:center">'.$item->quantity.'</td>
                                        <td style="padding:10px 8px;text-align:right">'.number_format($item->unit_price).'₫</td>
                                        <td style="padding:10px 8px;text-align:right;font-weight:700">'.number_format($item->total_price ?? $item->unit_price * $item->quantity).'₫</td>
                                    </tr>';
                                }
                                $html .= '</table>';
                                return new \Illuminate\Support\HtmlString($html);
                            }),

                        // ── THANH TOÁN ──
                        Placeholder::make('section_payment')
    ->label('')
    ->hiddenLabel()
    ->content(new \Illuminate\Support\HtmlString('<div style="display:flex;align-items:center;gap:8px;font-weight:800;font-size:15px;padding:8px 0;border-bottom:2px solid #eee;margin-bottom:4px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg> Thông tin thanh toán</div>')),

                        Placeholder::make('payment_method')
                            ->label('Phương thức thanh toán')
                            ->content('Thanh toán khi nhận hàng (COD)'),

                        Placeholder::make('coupon')
                            ->label('Mã giảm giá')
                            ->content(fn($record) => $record->coupon?->coupon_code ?: '(Không có)'),

                        Placeholder::make('subtotal')
                            ->label('Tạm tính')
                            ->content(fn($record) => number_format($record->subtotal ?? 0).'₫'),

                        Placeholder::make('discount_amount')
                            ->label('Giảm giá')
                            ->content(fn($record) => '-'.number_format($record->discount_amount ?? 0).'₫'),

                        Placeholder::make('total_amount')
                            ->label('Tổng cộng')
                            ->content(fn($record) => new \Illuminate\Support\HtmlString(
                                '<span style="font-size:18px;font-weight:800;color:#e55a2b">'.number_format($record->total_amount).'₫</span>'
                            )),
                    ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa'),
                ]),
            ]);
    }
}
