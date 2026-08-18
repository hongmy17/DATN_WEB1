<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),

                TextColumn::make('name')
                    ->label('Tên danh mục')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->description
                        ? \Illuminate\Support\Str::limit($record->description, 60)
                        : null),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Đã sao chép slug!'),

                TextColumn::make('parent.name')
                    ->label('Danh mục cha')
                    ->badge()
                    ->color('info')
                    ->placeholder('— Gốc —'),

                TextColumn::make('children_count')
                    ->label('Danh mục con')
                    ->counts('children')
                    ->badge()
                    ->color('success'),

                TextColumn::make('products_count')
                    ->label('Sản phẩm')
                    ->counts('products')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('parent_id')
                    ->label('Danh mục cha')
                    ->relationship('parent', 'name')
                    ->placeholder('Tất cả'),

                TrashedFilter::make()
                    ->label('Thùng rác'),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make()
                    ->label('Sửa')
                    ->visible(fn($record) => ! $record->trashed()),

                // ─────────────────────────────────────────────────────────────
                // YÊU CẦU GIẢNG VIÊN: danh mục còn sản phẩm thì KHÔNG hiện nút
                // xóa; khi sản phẩm được chuyển đi hết thì nút tự hiện lại.
                //
                // Hai lớp bảo vệ, cố ý giữ cả hai:
                //   1. visible()  → chặn ở GIAO DIỆN, admin không thấy nút.
                //   2. before()   → chặn ở SERVER, phòng trường hợp dữ liệu vừa
                //                   thay đổi ở tab khác, hoặc request bị giả mạo.
                //      Nguyên tắc: không bao giờ chỉ tin vào validation phía UI.
                //
                // Dùng $record->products_count (đã được ->counts() nạp sẵn ở cột
                // phía trên bằng MỘT câu SQL cho cả bảng) thay vì
                // $record->products()->exists() — nếu gọi exists() ở đây, mỗi
                // dòng sẽ bắn thêm 2 query (lỗi N+1).
                // ─────────────────────────────────────────────────────────────
                DeleteAction::make()
                    ->label('Chuyển vào thùng rác')
                    // Ba điều kiện cùng lúc: chưa nằm trong thùng rác, không còn
                    // sản phẩm, không còn danh mục con.
                    ->visible(fn($record) => ! $record->trashed()
                        && (int) $record->products_count === 0
                        && (int) $record->children_count === 0)
                    ->before(function ($record, $action) {
                        // halt() ném exception ngay lập tức để dừng action — nên phải
                        // gọi Notification::send() TRƯỚC halt(), không phải sau.
                        if ($record->children()->exists()) {
                            Notification::make()
                                ->title('Không thể xóa!')
                                ->body('Danh mục đang có danh mục con.')
                                ->danger()
                                ->send();
                            $action->halt();
                        }

                        if ($record->products()->exists()) {
                            Notification::make()
                                ->title('Không thể xóa!')
                                ->body('Danh mục đang có sản phẩm.')
                                ->danger()
                                ->send();
                            $action->halt();
                        }
                    }),

                RestoreAction::make()
                    ->label('Khôi phục'),

                ForceDeleteAction::make()
                    ->label('Xóa vĩnh viễn')
                    ->requiresConfirmation()
                    ->modalHeading('Xóa vĩnh viễn danh mục')
                    ->modalDescription('Hành động này KHÔNG THỂ hoàn tác. Dữ liệu sẽ bị xóa khỏi database.')
                    ->before(function ($record, ForceDeleteAction $action) {
                        // Kiểm tra CẢ sản phẩm đã xóa mềm (withTrashed): một danh
                        // mục rỗng trên giao diện vẫn có thể đang giữ sản phẩm
                        // trong thùng rác. Xóa cứng danh mục khi đó sẽ ném lỗi
                        // ràng buộc khóa ngoại vì products.category_id khai báo
                        // onDelete('restrict').
                        if ($record->products()->withTrashed()->exists()) {
                            Notification::make()
                                ->title('Không thể xóa vĩnh viễn')
                                ->body('Danh mục vẫn còn sản phẩm (kể cả sản phẩm trong thùng rác).')
                                ->danger()
                                ->persistent()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Xóa hàng loạt trước đây KHÔNG bị chặn: admin tick 10 danh mục
                    // rồi bấm Xóa là xóa sạch, kể cả danh mục đang có sản phẩm.
                    // Nay: bỏ qua các danh mục không đủ điều kiện và báo rõ tên.
                    DeleteBulkAction::make()
                        ->label('Xóa các mục đã chọn')
                        ->action(function ($records) {
                            $blocked = collect();
                            $deleted = 0;

                            foreach ($records as $record) {
                                if ($record->products()->exists() || $record->children()->exists()) {
                                    $blocked->push($record->name);
                                    continue;
                                }
                                $record->delete();
                                $deleted++;
                            }

                            if ($deleted > 0) {
                                Notification::make()
                                    ->title("Đã xóa {$deleted} danh mục")
                                    ->success()
                                    ->send();
                            }

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->title('Một số danh mục không thể xóa')
                                    ->body('Còn sản phẩm hoặc danh mục con: ' . $blocked->implode(', '))
                                    ->warning()
                                    ->persistent()
                                    ->send();
                            }
                        }),

                    RestoreBulkAction::make()->label('Khôi phục'),
                ]),
            ]);
    }
}