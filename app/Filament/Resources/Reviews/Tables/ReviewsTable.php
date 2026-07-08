<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Models\ReviewReply;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Sản phẩm')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('user.name')
                    ->label('Người dùng')
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Sao')
                    ->badge()
                    ->formatStateUsing(fn($state) => str_repeat('★', $state))
                    ->color(fn($state) => match (true) {
                        $state >= 4 => 'success',
                        $state === 3 => 'warning',
                        default     => 'danger',
                    }),

                TextColumn::make('comment')
                    ->label('Nội dung')
                    ->limit(50)
                    ->placeholder('(Không có)'),

                IconColumn::make('images')
                    ->label('Ảnh')
                    ->boolean()
                    ->trueIcon('heroicon-o-photo')
                    ->falseIcon('heroicon-o-minus')
                    ->getStateUsing(fn($record) => ! empty($record->images)),

                IconColumn::make('status')
                    ->label('Hiển thị')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Ngày gửi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('rating')
                    ->label('Số sao')
                    ->options([
                        5 => '★★★★★ (5 sao)',
                        4 => '★★★★☆ (4 sao)',
                        3 => '★★★☆☆ (3 sao)',
                        2 => '★★☆☆☆ (2 sao)',
                        1 => '★☆☆☆☆ (1 sao)',
                    ]),

                TernaryFilter::make('status')
                    ->label('Trạng thái')
                    ->trueLabel('Đang hiển thị')
                    ->falseLabel('Đang ẩn'),

                TernaryFilter::make('has_image')
                    ->label('Có ảnh')
                    ->queries(
                        true: fn($q) => $q->whereNotNull('images')->where('images', '!=', '[]'),
                        false: fn($q) => $q->where(fn($q) => $q->whereNull('images')->orWhere('images', '[]')),
                    ),
            ])
            ->recordActions([
                // Xem + ẩn/hiện
                EditAction::make()->label('Chi tiết'),

                // Reply của shop
                Action::make('reply')
                    ->label('Phản hồi')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->form([
                        Placeholder::make('original')
                            ->label('Đánh giá gốc')
                            ->content(fn($record) => '"' . ($record->comment ?? '(Không có nội dung)') . '"'),

                        Textarea::make('comment')
                            ->label('Phản hồi của Shop')
                            ->required()
                            ->rows(4)
                            ->placeholder('Cảm ơn bạn đã đánh giá...'),
                    ])
                    ->action(function ($record, array $data): void {
                        ReviewReply::create([
                            'review_id' => $record->id,
                            'user_id' => Auth::id(),
                            'comment'   => $data['comment'],
                        ]);

                        Notification::make()
                            ->title('Đã gửi phản hồi')
                            ->success()->send();
                    })
                    ->modalHeading('Phản hồi đánh giá')
                    ->modalSubmitActionLabel('Gửi phản hồi'),

                // Ẩn/hiện nhanh
                Action::make('toggleStatus')
                    ->label(fn($record) => $record->status ? 'Ẩn' : 'Hiện')
                    ->icon(fn($record) => $record->status ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn($record) => $record->status ? 'warning' : 'success')
                    ->action(function ($record): void {
                        $record->update(['status' => ! $record->status]);
                        Notification::make()
                            ->title($record->status ? 'Đã hiển thị đánh giá' : 'Đã ẩn đánh giá')
                            ->success()->send();
                    }),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('approve')
                        ->label('Duyệt hiển thị')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records): void {
                            $records->each->update(['status' => true]);
                            Notification::make()->title('Đã duyệt ' . $records->count() . ' đánh giá')->success()->send();
                        }),

                    \Filament\Actions\BulkAction::make('hide')
                        ->label('Ẩn')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(function ($records): void {
                            $records->each->update(['status' => false]);
                            Notification::make()->title('Đã ẩn ' . $records->count() . ' đánh giá')->success()->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
