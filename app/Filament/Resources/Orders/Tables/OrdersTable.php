<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use App\Models\ProductVariant;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['payment', 'items']))
            ->columns([
                /*
                |--------------------------------------------------------------
                | YÊU CẦU GIẢNG VIÊN: bỏ các cột đã có trong "Xem chi tiết"
                |--------------------------------------------------------------
                | Đã gỡ 4 cột: Người nhận, Số điện thoại, Tổng tiền, Mã giảm giá.
                | Cả 4 đều hiển thị đầy đủ trong modal "Xem chi tiết" bên dưới.
                |
                | NHƯNG: hai cột Người nhận / Số điện thoại trước đây có
                | ->searchable(), gỡ đi là admin mất luôn khả năng tìm đơn theo
                | tên hoặc SĐT khách. Nên phần tìm kiếm được CHUYỂN vào cột "Mã
                | đơn" bằng searchable(query: ...) — ô tìm kiếm vẫn tra được tên
                | và SĐT dù hai cột đó không còn hiển thị.
                */
                TextColumn::make('id')
                    ->label('Mã đơn')
                    ->formatStateUsing(fn($state) => 'NX-' . str_pad($state, 6, '0', STR_PAD_LEFT))
                    ->description(fn($record) => $record->receiver_name)
                    ->searchable(query: fn($query, string $search) => $query
                        ->where('id', 'like', "%{$search}%")
                        ->orWhere('receiver_name', 'like', "%{$search}%")
                        ->orWhere('receiver_phone', 'like', "%{$search}%"))
                    ->sortable(),

                /*
                | Trước đây cột này là SelectColumn — admin đổi trạng thái trực
                | tiếp trên bảng, tự do đi bất kỳ đâu (kể cả từ "Đã hủy" ngược về
                | "Đang giao", hoặc tự gán "Đã hoàn tiền" không qua quy trình).
                |
                | Nay chuyển thành cột hiển thị (badge). Việc đổi trạng thái đưa
                | vào action riêng "Cập nhật trạng thái" bên dưới, nơi có thể
                | kiểm tra tồn kho trước và chỉ cho phép các bước đi hợp lệ.
                */
                TextColumn::make('order_status')
                    ->label('Trạng thái đơn hàng')
                    ->badge()
                    ->formatStateUsing(fn($record) => $record->statusLabel())
                    ->color(fn($state) => match ((int) $state) {
                        Order::STATUS_PENDING          => 'warning',
                        Order::STATUS_CONFIRMED        => 'info',
                        Order::STATUS_SHIPPING         => 'primary',
                        Order::STATUS_COMPLETED        => 'success',
                        Order::STATUS_CANCELLED        => 'danger',
                        Order::STATUS_AWAITING_PAYMENT => 'gray',
                        Order::STATUS_CANCEL_REQUESTED => 'danger',
                        Order::STATUS_REFUNDED         => 'gray',
                        default                        => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Phương thức thanh toán')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'vnpay'         => 'VNPay',
                        'bank_transfer' => 'Chuyển khoản',
                        default         => 'COD',
                    })
                    ->badge()
                    ->color(fn($state) => $state === 'cod' ? 'warning' : 'info'),

                TextColumn::make('payment.status')
                    ->label('Trạng thái thanh toán')
                    ->formatStateUsing(fn($state) => match ((int) $state) {
                        1       => 'Đã thu tiền',
                        2       => 'Thất bại',
                        3       => 'Đã hoàn tiền',
                        default => 'Chờ thu tiền',
                    })
                    ->badge()
                    ->color(fn($state) => match ((int) $state) {
                        1       => 'success',
                        2       => 'danger',
                        3       => 'gray',
                        default => 'warning',
                    }),

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
                    ->multiple()
                    // Đọc từ nguồn sự thật duy nhất trong model — không chép tay
                    // danh sách trạng thái ra đây nữa.
                    ->options(Order::STATUS_LABELS),
            ])
            ->recordActions([

                /*
                |--------------------------------------------------------------
                | 1. CẬP NHẬT TRẠNG THÁI (thay cho SelectColumn cũ)
                |--------------------------------------------------------------
                | - Chỉ liệt kê những trạng thái hợp lệ theo Order::STATUS_TRANSITIONS
                | - Kiểm tra tồn kho TRƯỚC khi cho xác nhận, báo lỗi tử tế thay vì
                |   để RuntimeException trong model bắn ra màn hình lỗi 500
                */
                Action::make('updateStatus')
                    ->label('Cập nhật trạng thái')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->visible(fn($record) => ! empty($record->allowedNextStatuses()))
                    ->modalHeading(fn($record) => 'Cập nhật đơn NX-' . str_pad($record->id, 6, '0', STR_PAD_LEFT))
                    ->modalSubmitActionLabel('Cập nhật')
                    ->modalCancelActionLabel('Đóng')
                    ->form([
                        Placeholder::make('current')
                            ->label('Trạng thái hiện tại')
                            ->content(fn($record) => $record->statusLabel()),

                        Select::make('order_status')
                            ->label('Chuyển sang')
                            ->options(fn($record) => $record->allowedNextStatuses())
                            ->required()
                            ->native(false)
                            ->helperText(fn($record) => $record->stock_deducted
                                ? 'Đơn này đã trừ tồn kho. Nếu hủy, hàng sẽ được hoàn lại kho.'
                                : 'Đơn này CHƯA trừ tồn kho. Tồn kho chỉ bị trừ khi chuyển sang "Đã xác nhận".'),

                        Textarea::make('cancel_reason')
                            ->label('Lý do hủy')
                            ->rows(2)
                            ->required(fn(callable $get) => (int) $get('order_status') === Order::STATUS_CANCELLED)
                            ->visible(fn(callable $get) => (int) $get('order_status') === Order::STATUS_CANCELLED),
                    ])
                    ->action(function (Order $record, array $data, Action $action): void {
                        $newStatus = (int) $data['order_status'];

                        // Chốt chặn 1: bước đi có hợp lệ không?
                        if (! array_key_exists($newStatus, $record->allowedNextStatuses())) {
                            Notification::make()
                                ->title('Không thể chuyển trạng thái')
                                ->body('Bước chuyển này không hợp lệ. Vui lòng tải lại trang.')
                                ->danger()
                                ->send();
                            $action->halt();
                        }

                        // Chốt chặn 2: nếu là XÁC NHẬN đơn → kiểm tra tồn kho trước.
                        // Model cũng kiểm tra lần nữa (và khóa dòng), nhưng kiểm
                        // tra ở đây để báo lỗi đẹp thay vì màn hình 500.
                        if ($newStatus === Order::STATUS_CONFIRMED && ! $record->stock_deducted) {
                            foreach ($record->items as $item) {
                                $variant = ProductVariant::withTrashed()->find($item->variant_id);

                                if (! $variant || ! $variant->manage_stock) {
                                    continue;
                                }

                                if ($variant->stock_quantity < $item->quantity) {
                                    Notification::make()
                                        ->title('Không đủ tồn kho')
                                        ->body("\"{$item->product_name}\" chỉ còn {$variant->stock_quantity} "
                                            . "nhưng đơn cần {$item->quantity}. Vui lòng nhập thêm hàng trước khi xác nhận.")
                                        ->danger()
                                        ->persistent()
                                        ->send();
                                    $action->halt();
                                }
                            }
                        }

                        $payload = ['order_status' => $newStatus];

                        if ($newStatus === Order::STATUS_CANCELLED && ! empty($data['cancel_reason'])) {
                            $payload['cancel_reason'] = $data['cancel_reason'];
                        }

                        $record->update($payload);

                        $message = match ($newStatus) {
                            Order::STATUS_CONFIRMED => 'Đã xác nhận đơn hàng và trừ tồn kho.',
                            Order::STATUS_CANCELLED => $record->stock_deducted === false
                                ? 'Đã hủy đơn hàng.'
                                : 'Đã hủy đơn hàng và hoàn lại tồn kho.',
                            default                 => 'Đã cập nhật trạng thái đơn hàng.',
                        };

                        Notification::make()->title($message)->success()->send();
                    }),

                /*
                |--------------------------------------------------------------
                | 2. DUYỆT HỦY  (chỉ hiện khi khách đã gửi yêu cầu hủy — status 6)
                |--------------------------------------------------------------
                | Đây là nửa còn lại của luồng "yêu cầu hủy" mà giảng viên yêu
                | cầu làm cho hoạt động thật: khách hủy đơn ĐÃ XÁC NHẬN thì không
                | được hủy thẳng, phải chờ shop duyệt (xem OrderController::cancel).
                */
                Action::make('approveCancel')
                    ->label('Duyệt hủy')
                    ->icon('heroicon-o-check-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Duyệt yêu cầu hủy đơn')
                    ->modalDescription(fn($record) => 'Lý do khách đưa ra: "' . ($record->cancel_reason ?: 'Không có') . '". '
                        . 'Duyệt sẽ hủy đơn và hoàn lại toàn bộ tồn kho.')
                    ->modalSubmitActionLabel('Đồng ý hủy đơn')
                    ->visible(fn($record) => (int) $record->order_status === Order::STATUS_CANCEL_REQUESTED)
                    ->action(function (Order $record): void {
                        $record->update(['order_status' => Order::STATUS_CANCELLED]);

                        Notification::make()
                            ->title('Đã duyệt yêu cầu hủy, tồn kho đã được hoàn lại')
                            ->success()
                            ->send();
                    }),

                /*
                |--------------------------------------------------------------
                | 3. TỪ CHỐI HỦY  (đơn quay lại trạng thái "Đã xác nhận")
                |--------------------------------------------------------------
                */
                Action::make('rejectCancel')
                    ->label('Từ chối hủy')
                    ->icon('heroicon-o-x-circle')
                    ->color('gray')
                    ->visible(fn($record) => (int) $record->order_status === Order::STATUS_CANCEL_REQUESTED)
                    ->form([
                        Textarea::make('reject_note')
                            ->label('Lý do từ chối (gửi tới khách hàng)')
                            ->rows(3)
                            ->required()
                            ->placeholder('Ví dụ: Đơn hàng đã được đóng gói và bàn giao cho đơn vị vận chuyển.'),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->update([
                            'order_status'  => Order::STATUS_CONFIRMED,
                            // Xóa lý do hủy cũ để trang đơn hàng của khách không
                            // còn hiển thị thông tin gây hiểu nhầm.
                            'cancel_reason' => 'Yêu cầu hủy đã bị từ chối — ' . $data['reject_note'],
                        ]);

                        Notification::make()
                            ->title('Đã từ chối yêu cầu hủy, đơn trở lại trạng thái "Đã xác nhận"')
                            ->warning()
                            ->send();
                    }),

                /*
                |--------------------------------------------------------------
                | 4. XEM CHI TIẾT — nơi chứa 4 cột vừa được gỡ khỏi bảng
                |--------------------------------------------------------------
                */
                ViewAction::make()
                    ->label('Xem chi tiết')
                    // Mã đơn dùng chung một định dạng NX-000000 với cột "Mã đơn",
                    // hóa đơn PDF và email — trước đây modal này dùng "ĐH-00000".
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

                        Placeholder::make('cancel_reason')
                            ->label('Lý do hủy')
                            ->visible(fn($record) => ! empty($record->cancel_reason))
                            ->content(fn($record) => new HtmlString(
                                '<div style="display:flex;align-items:flex-start;gap:8px;padding:10px 12px;
                     background:#fee2e2;border-radius:8px;color:#991b1b;font-size:13px">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" style="flex-shrink:0;margin-top:1px">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            ' . e($record->cancel_reason) . '
        </div>'
                            )),

                        Placeholder::make('section_items')
                            ->hiddenLabel()
                            ->content(new HtmlString('<div style="font-weight:800;font-size:14px;padding:8px 0;border-bottom:2px solid #eee;margin-bottom:4px">Sản phẩm đã đặt</div>')),

                        Placeholder::make('items')->label('')
                            ->content(function ($record) {
                                $items = $record->items;
                                if ($items->isEmpty()) {
                                    return 'Không có sản phẩm';
                                }
                                $html = '<table style="width:100%;border-collapse:collapse;font-size:13px">
                                    <tr style="background:#f9f9f9;font-weight:700">
                                        <td style="padding:10px 8px">Sản phẩm</td>
                                        <td style="padding:10px 8px">Biến thể</td>
                                        <td style="padding:10px 8px;text-align:center">SL</td>
                                        <td style="padding:10px 8px;text-align:right">Đơn giá</td>
                                        <td style="padding:10px 8px;text-align:right">Thành tiền</td>
                                    </tr>';
                                foreach ($items as $item) {
                                    $sku  = $item->variant_sku ? "<div style='font-size:11px;color:#aaa'>SKU: " . e($item->variant_sku) . '</div>' : '';
                                    $name = e($item->product_name);
                                    $desc = e($item->variant_description);
                                    $html .= "<tr style='border-top:1px solid #eee'>
                                        <td style='padding:10px 8px;font-weight:600'>{$name}{$sku}</td>
                                        <td style='padding:10px 8px;color:#888;font-size:12px'>{$desc}</td>
                                        <td style='padding:10px 8px;text-align:center'>{$item->quantity}</td>
                                        <td style='padding:10px 8px;text-align:right'>" . number_format($item->unit_price) . "₫</td>
                                        <td style='padding:10px 8px;text-align:right;font-weight:700'>" . number_format($item->total_price ?? $item->unit_price * $item->quantity) . '₫</td>
                                    </tr>';
                                }
                                $html .= '</table>';
                                return new HtmlString($html);
                            }),

                        Placeholder::make('payment_method')
                            ->label('Phương thức thanh toán')
                            ->content(fn($record) => match ($record->payment_method) {
                                'vnpay'         => 'Ví điện tử VNPay',
                                'momo'          => 'Ví MoMo',
                                'zalopay'       => 'ZaloPay',
                                'bank_transfer' => 'Chuyển khoản ngân hàng',
                                default         => 'Thanh toán khi nhận hàng (COD)',
                            }),

                        Placeholder::make('coupon')
                            ->label('Mã giảm giá')
                            ->content(fn($record) => $record->coupon_code ?: '(Không có)'),

                        Placeholder::make('subtotal')
                            ->label('Tạm tính')
                            ->content(fn($record) => number_format($record->subtotal ?? 0) . '₫'),

                        Placeholder::make('discount_amount')
                            ->label('Giảm giá')
                            ->content(fn($record) => '-' . number_format($record->discount_amount ?? 0) . '₫'),

                        Placeholder::make('total_amount')
                            ->label('Tổng cộng')
                            ->content(fn($record) => new HtmlString(
                                '<span style="font-size:18px;font-weight:800;color:#E30019">' . number_format($record->total_amount) . '₫</span>'
                            )),

                        Placeholder::make('stock_deducted')
                            ->label('Tồn kho')
                            ->content(fn($record) => $record->stock_deducted
                                ? 'Đã trừ tồn kho'
                                : 'Chưa trừ tồn kho (chờ xác nhận đơn)'),
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
            // Đã gỡ DeleteBulkAction: đơn hàng là chứng từ tài chính, không được
            // xóa hàng loạt. Muốn dừng đơn thì hủy đơn, không xóa dữ liệu.
            ->toolbarActions([]);
    }
}
