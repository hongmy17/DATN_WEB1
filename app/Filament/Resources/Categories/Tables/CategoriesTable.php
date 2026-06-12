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
            ->defaultSort('id', 'asc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function ($record, $action) {
                        if ($record->children()->exists()) {
                            $action->halt(); // dừng action
                            \Filament\Notifications\Notification::make()
                                ->title('Không thể xóa!')
                                ->body('Danh mục đang có danh mục con.')
                                ->danger()
                                ->send();
                        }

                        if ($record->products()->exists()) {
                            $action->halt();
                            \Filament\Notifications\Notification::make()
                                ->title('Không thể xóa!')
                                ->body('Danh mục đang có sản phẩm.')
                                ->danger()
                                ->send();
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