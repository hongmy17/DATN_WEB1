<?php

namespace App\Filament\Resources\RefundRequests\Tables;

use App\Console\Commands\AutoRejectStaleRefunds;
use App\Mail\RefundStatusMail;
use App\Models\Order;
use App\Models\Payment;
use App\Models\RefundRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;

class RefundRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['order', 'user']))
            ->columns([
                TextColumn::make('order_id')
                    ->label('Mã đơn')
                    ->formatStateUsing(fn($state) => 'NX-' . str_pad($state, 6, '0', STR_PAD_LEFT))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Khách hàng')
                    ->searchable(),

                TextColumn::make('reason')
                    ->label('Lý do')
                    ->formatStateUsing(fn($state) => RefundRequest::REASONS[$state] ?? $state),

                TextColumn::make('refund_amount')
                    ->label('Số tiền')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn($record) => $record->statusLabel())
                    ->color(fn($state) => match ((int) $state) {
                        RefundRequest::STATUS_APPROVED => 'info',
                        RefundRequest::STATUS_REFUNDED => 'success',
                        RefundRequest::STATUS_REJECTED => 'danger',
                        default                        => 'warning',
                    }),

                // ─────────────────────────────────────────────────────────────
                // ĐỒNG HỒ ĐẾM NGƯỢC — cho admin thấy còn bao lâu trước khi hệ
                // thống tự động từ chối yêu cầu này (lệnh refunds:auto-reject).
                // Đây cũng là bằng chứng trực quan khi demo cho hội đồng.
                // ─────────────────────────────────────────────────────────────
                TextColumn::make('deadline')
                    ->label('Hạn xử lý')
                    ->state(function ($record) {
                        if ((int) $record->status !== RefundRequest::STATUS_PENDING) {
                            return '—';
                        }

                        $expiresAt  = $record->created_at->copy()->addDays(AutoRejectStaleRefunds::DEADLINE_DAYS);
                        $daysLeft   = (int) ceil(now()->diffInHours($expiresAt, false) / 24);

                        return $daysLeft <= 0 ? 'Quá hạn' : "Còn {$daysLeft} ngày";
                    })
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state === 'Quá hạn' => 'danger',
                        $state === '—'       => 'gray',
                        $state === 'Còn 1 ngày' => 'danger',
                        default              => 'warning',
                    })
                    ->tooltip('Quá ' . AutoRejectStaleRefunds::DEADLINE_DAYS
                        . ' ngày không xử lý, hệ thống sẽ tự động từ chối yêu cầu'),

                TextColumn::make('created_at')
                    ->label('Ngày gửi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->placeholder('Tất cả')
                    ->options([
                        RefundRequest::STATUS_PENDING  => 'Chờ xử lý',
                        RefundRequest::STATUS_APPROVED => 'Đã duyệt - chờ chuyển tiền',
                        RefundRequest::STATUS_REFUNDED => 'Đã hoàn tiền',
                        RefundRequest::STATUS_REJECTED => 'Đã từ chối',
                    ]),
            ])
            ->recordActions([

                // ── XEM CHI TIẾT ──────────────────────────────
                Action::make('view')
                    ->label('Xem chi tiết')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn($record) => 'Yêu cầu hoàn tiền — NX-' . str_pad($record->order_id, 6, '0', STR_PAD_LEFT))
                    ->modalWidth('2xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Đóng')
                    ->form([
                        Placeholder::make('reason')
                            ->label('Lý do')
                            ->content(fn($record) => RefundRequest::REASONS[$record->reason] ?? $record->reason),

                        Placeholder::make('reason_detail')
                            ->label('Mô tả thêm')
                            ->content(fn($record) => $record->reason_detail ?: '(Không có)'),

                        Placeholder::make('evidence')
                            ->label('Bằng chứng')
                            ->content(function ($record) {
                                if (empty($record->evidence_images)) {
                                    return '(Không có)';
                                }
                                $html = '<div style="display:flex;gap:8px;flex-wrap:wrap">';
                                foreach ($record->evidence_images as $path) {
                                    $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
                                    $html .= "<a href='{$url}' target='_blank'><img src='{$url}' style='width:80px;height:80px;object-fit:cover;border-radius:8px'></a>";
                                }
                                $html .= '</div>';
                                return new HtmlString($html);
                            }),

                        Placeholder::make('bank')
                            ->label('Thông tin nhận tiền')
                            ->visible(fn($record) => $record->bank_name)
                            ->content(fn($record) => "{$record->bank_name} — {$record->bank_account_number} — {$record->bank_account_holder}"),

                        Placeholder::make('refund_amount')
                            ->label('Số tiền yêu cầu hoàn')
                            ->content(fn($record) => number_format($record->refund_amount) . '₫'),

                        Placeholder::make('reject_reason')
                            ->label('Lý do từ chối')
                            ->visible(fn($record) => (int) $record->status === RefundRequest::STATUS_REJECTED)
                            ->content(fn($record) => $record->reject_reason ?: '(Không có)'),
                    ]),

                // ── DUYỆT YÊU CẦU (Bước 2 — Đồng ý) ───────────
                Action::make('approve')
                    ->label('Duyệt')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Duyệt yêu cầu hoàn tiền')
                    ->modalDescription('Xác nhận đồng ý hoàn tiền cho yêu cầu này? Khách hàng sẽ nhận được email thông báo.')
                    ->visible(fn($record) => (int) $record->status === RefundRequest::STATUS_PENDING)
                    ->action(function (RefundRequest $record): void {
                        $record->update([
                            'status'      => RefundRequest::STATUS_APPROVED,
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        // BỔ SUNG: trước đây action "Duyệt" là nhánh DUY NHẤT không
                        // gửi mail — khách được duyệt hoàn tiền mà không nhận thông
                        // báo nào, trong khi "Từ chối" và "Đã hoàn tiền" đều có gửi.
                        if ($record->user?->email) {
                            Mail::to($record->user->email)->send(new RefundStatusMail($record));
                        }

                        Notification::make()
                            ->title('Đã duyệt yêu cầu hoàn tiền, email đã được gửi cho khách')
                            ->success()
                            ->send();
                    }),

                // ── TỪ CHỐI (Bước 2 — Từ chối) ────────────────
                Action::make('reject')
                    ->label('Từ chối')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn($record) => (int) $record->status === RefundRequest::STATUS_PENDING)
                    ->form([
                        Textarea::make('reject_reason')
                            ->label('Lý do từ chối')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (RefundRequest $record, array $data): void {
                        $record->update([
                            'status'        => RefundRequest::STATUS_REJECTED,
                            'reject_reason' => $data['reject_reason'],
                            'reviewed_by'   => auth()->id(),
                            'reviewed_at'   => now(),
                        ]);

                        if ($record->user?->email) {
                            Mail::to($record->user->email)->send(new RefundStatusMail($record));
                        }

                        Notification::make()
                            ->title('Đã từ chối yêu cầu, email đã được gửi cho khách')
                            ->warning()
                            ->send();
                    }),

                // ── XÁC NHẬN ĐÃ HOÀN TIỀN (Bước 4) ────────────
                Action::make('confirmRefunded')
                    ->label('Xác nhận đã hoàn tiền')
                    ->icon('heroicon-o-banknotes')
                    ->color('primary')
                    ->visible(fn($record) => (int) $record->status === RefundRequest::STATUS_APPROVED)
                    ->form([
                        FileUpload::make('receipt_image')
                            ->label('Ảnh biên lai chuyển tiền')
                            ->image()
                            ->disk('public')
                            ->directory('refunds/receipts')
                            ->required(),
                    ])
                    ->action(function (RefundRequest $record, array $data): void {
                        $record->update([
                            'status'        => RefundRequest::STATUS_REFUNDED,
                            'receipt_image' => $data['receipt_image'],
                            'refunded_at'   => now(),
                        ]);

                        // Đồng bộ sang Order + Payment.
                        // Order chuyển sang STATUS_REFUNDED sẽ kích hoạt hook trong
                        // Order::booted() → hàng được HOÀN LẠI KHO (vì khách trả hàng),
                        // và chỉ hoàn đúng một lần nhờ cờ stock_deducted.
                        $record->order->update(['order_status' => Order::STATUS_REFUNDED]);
                        $record->order->payment?->update(['status' => Payment::STATUS_REFUNDED]);

                        if ($record->user?->email) {
                            Mail::to($record->user->email)->send(new RefundStatusMail($record));
                        }

                        Notification::make()
                            ->title('Đã xác nhận hoàn tiền, email đã được gửi cho khách')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
