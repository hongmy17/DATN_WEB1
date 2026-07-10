<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Mã đơn')
                    ->formatStateUsing(fn($state) => 'NX-' . str_pad($state, 6, '0', STR_PAD_LEFT))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('receiver_name')
                    ->label('Người nhận')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('receiver_phone')
                    ->label('Số điện thoại')
                    ->searchable(),

                TextColumn::make('total_amount')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('coupon_code')
                    ->label('Mã giảm giá')
                    ->placeholder('—')
                    ->badge()
                    ->color('success'),

                // FIX: Bỏ emoji trong trạng thái
                SelectColumn::make('order_status')
                    ->label('Trạng thái')
                    ->options([
                        0 => '🟡 Chờ xác nhận',
                        1 => '🔵 Đã xác nhận',
                        2 => '🟠 Đang giao',
                        3 => '🟢 Hoàn thành',
                        4 => '🔴 Đã hủy',
                        5 => '🔷 Chờ thanh toán (VNPay)',
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
                        5 => 'Chờ thanh toán (VNPay)',
                    ]),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make()
                    ->label('Xem')
                    ->modalHeading(fn($record) => 'Chi tiết đơn hàng NX-' . str_pad($record->id, 6, '0', STR_PAD_LEFT))
                    ->modalWidth('3xl')
                    ->form([
                        Placeholder::make('section_receiver')
                            ->hiddenLabel()
                            ->content(new HtmlString('<div style="font-weight:800;font-size:14px;padding:8px 0;border-bottom:2px solid #eee;margin-bottom:4px">Thông tin người nhận</div>')),

                        Placeholder::make('receiver_name')->label('Họ và tên')
                            ->content(fn($record) => $record->receiver_name),
                        Placeholder::make('receiver_phone')->label('Số điện thoại')
                            ->content(fn($record) => $record->receiver_phone),
                        Placeholder::make('address')->label('Địa chỉ')
                            ->content(fn($record) => $record->shipping_address ?: '(Không có)'),
                        Placeholder::make('note')->label('Ghi chú')
                            ->content(fn($record) => $record->note ?: '(Không có)'),

                        Placeholder::make('section_items')
                            ->hiddenLabel()
                            ->content(new HtmlString('<div style="font-weight:800;font-size:14px;padding:8px 0;border-bottom:2px solid #eee;margin-bottom:4px">Sản phẩm đã đặt</div>')),

                        Placeholder::make('items')->label('')
                            ->content(function ($record) {
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
                                    $sku = $item->variant_sku ? "<div style='font-size:11px;color:#aaa'>SKU: {$item->variant_sku}</div>" : '';
                                    $html .= "<tr style='border-top:1px solid #eee'>
                                        <td style='padding:10px 8px;font-weight:600'>{$item->product_name}{$sku}</td>
                                        <td style='padding:10px 8px;color:#888;font-size:12px'>{$item->variant_description}</td>
                                        <td style='padding:10px 8px;text-align:center'>{$item->quantity}</td>
                                        <td style='padding:10px 8px;text-align:right'>" . number_format($item->unit_price) . "₫</td>
                                        <td style='padding:10px 8px;text-align:right;font-weight:700'>" . number_format($item->total_price ?? $item->unit_price * $item->quantity) . "₫</td>
                                    </tr>";
                                }
                                $html .= '</table>';
                                return new HtmlString($html);
                            }),

                        // ĐỔI THÀNH — đọc từ database, hiện đúng phương thức khách đã chọn:
                        Placeholder::make('payment_method')
                            ->label('Phương thức thanh toán')
                            ->content(function ($record) {
                                return match ($record->payment_method) {
                                    'vnpay'         => 'Ví điện tử VNPay',
                                    'momo'          => 'Ví MoMo',
                                    'zalopay'       => 'ZaloPay',
                                    'bank_transfer' => 'Chuyển khoản ngân hàng',
                                    default         => 'Thanh toán khi nhận hàng (COD)',
                                };
                            }),

                        Placeholder::make('coupon')
                            ->label('Mã giảm giá')
                            ->content(fn($record) => $record->coupon?->coupon_code ?: '(Không có)'),

                        Placeholder::make('subtotal')
                            ->label('Tạm tính')
                            ->content(fn($record) => number_format($record->subtotal ?? 0) . '₫'),

                        Placeholder::make('discount_amount')
                            ->label('Giảm giá')
                            ->content(fn($record) => '-' . number_format($record->discount_amount ?? 0) . '₫'),

                        Placeholder::make('total_amount')
                            ->label('Tổng cộng')
                            ->content(fn($record) => new \Illuminate\Support\HtmlString(
                                '<span style="font-size:18px;font-weight:800;color:#e55a2b">' . number_format($record->total_amount) . '₫</span>'
                            )),
                    ]),

                // ── NÚT IN HÓA ĐƠN PDF ──────────────────────────────────
                Action::make('print_invoice')
                    ->label('In hóa đơn')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn($record) => route('admin.invoice.download', $record->id))
                    ->openUrlInNewTab()
                    ->tooltip('Xuất hóa đơn PDF — mở tab mới, dùng Ctrl+P để in'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa'),
                ]),
            ]);
    }
}
