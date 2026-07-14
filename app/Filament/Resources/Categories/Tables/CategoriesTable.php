<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->description(fn ($record) => $record->description
                        ? \Illuminate\Support\Str::limit($record->description, 60)
                        : null
                    ),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->color('gray')
                    ->copyable()
                    ->copyMessage('Đã copy slug!'),

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
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function ($record, $action) {
                        // FIX: halt() ném exception ngay lập tức để dừng action — nên phải
                        // gọi Notification::send() TRƯỚC halt(), không phải sau. Code cũ đặt
                        // halt() trước khiến notification phía dưới trở thành dead code,
                        // không bao giờ chạy tới (xóa vẫn bị chặn đúng, nhưng không có thông báo).
                        if ($record->children()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Không thể xóa!')
                                ->body('Danh mục đang có danh mục con.')
                                ->danger()
                                ->send();
                            $action->halt();
                        }

                        if ($record->products()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Không thể xóa!')
                                ->body('Danh mục đang có sản phẩm.')
                                ->danger()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}