<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('avatar')
                    ->label('Ảnh đại diện')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                TextColumn::make('code')
                    ->label('Mã')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('name')
                    ->label('Họ và tên')
                    ->searchable()
                    ->sortable()
                    // Gạch ngang tên của tài khoản đang nằm trong thùng rác,
                    // để admin nhận ra ngay khi bật bộ lọc "Tất cả".
                    ->description(fn($record) => $record->trashed()
                        ? 'Đã xóa lúc ' . $record->deleted_at->format('d/m/Y H:i')
                        : null),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable(),

                TextColumn::make('role')
                    ->label('Vai trò')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        1       => 'Quản trị viên',
                        default => 'Khách hàng',
                    })
                    ->color(fn($state) => match ($state) {
                        1       => 'danger',
                        default => 'success',
                    }),

                IconColumn::make('status')
                    ->label('Trạng thái')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('Asia/Ho_Chi_Minh'),

                TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('Asia/Ho_Chi_Minh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->defaultSort('id', 'desc')

            ->filters([
                SelectFilter::make('role')
                    ->label('Vai trò')
                    ->placeholder('Tất cả')
                    ->options([
                        1 => 'Quản trị viên',
                        0 => 'Khách hàng',
                    ]),

                TrashedFilter::make()
                    ->label('Thùng rác'),
            ])

            ->recordActions([
                EditAction::make()
                    ->label('Sửa')
                    ->visible(fn($record) => ! $record->trashed()),

                // Xóa mềm — bắt buộc với bảng users, vì orders.user_id khai báo
                // onDelete('restrict'): xóa cứng một khách đã từng đặt hàng sẽ
                // ném lỗi SQL và làm hỏng cả trang.
                DeleteAction::make()
                    ->label('Chuyển vào thùng rác')
                    ->modalDescription('Tài khoản sẽ không đăng nhập được nữa nhưng toàn bộ đơn hàng và đánh giá vẫn được giữ nguyên. Có thể khôi phục sau.')
                    ->before(function ($record, DeleteAction $action) {
                        // Không cho admin tự xóa chính mình — sẽ bị đăng xuất
                        // ngay lập tức và có thể khóa luôn quyền truy cập.
                        if ($record->id === auth()->id()) {
                            Notification::make()
                                ->title('Không thể xóa chính tài khoản đang đăng nhập')
                                ->danger()
                                ->send();
                            $action->cancel();
                        }
                    }),

                RestoreAction::make()
                    ->label('Khôi phục'),

                ForceDeleteAction::make()
                    ->label('Xóa vĩnh viễn')
                    ->requiresConfirmation()
                    ->modalHeading('Xóa vĩnh viễn tài khoản')
                    ->modalDescription('Hành động này KHÔNG THỂ hoàn tác.')
                    ->before(function ($record, ForceDeleteAction $action) {
                        // Chặn ở tầng ứng dụng để báo lỗi tử tế, thay vì để MySQL
                        // ném ra lỗi ràng buộc khóa ngoại giữa màn hình admin.
                        if (Order::where('user_id', $record->id)->exists()) {
                            Notification::make()
                                ->title('Không thể xóa vĩnh viễn')
                                ->body('Tài khoản này đã có đơn hàng. Xóa cứng sẽ phá vỡ dữ liệu đơn hàng — hãy giữ ở thùng rác.')
                                ->danger()
                                ->persistent()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Chuyển vào thùng rác'),
                    RestoreBulkAction::make()->label('Khôi phục'),
                    // Cố ý KHÔNG có ForceDeleteBulkAction: xóa cứng hàng loạt
                    // tài khoản là thao tác quá nguy hiểm, và không có cách nào
                    // kiểm tra ràng buộc đơn hàng cho từng bản ghi một cách rõ
                    // ràng trong luồng bulk.
                ]),
            ]);
    }
}
