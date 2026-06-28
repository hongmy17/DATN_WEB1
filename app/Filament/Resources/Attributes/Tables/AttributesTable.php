<?php

namespace App\Filament\Resources\Attributes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttributesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable()->width('50px'),

                TextColumn::make('name')
                    ->label('Tên thuộc tính')
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('display_type')
                    ->label('Kiểu')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state == 1 ? 'Màu sắc' : 'Text')
                    ->color(fn ($state) => $state == 1 ? 'success' : 'gray'),

                TextColumn::make('attributeValues')
                    ->label('Giá trị')
                    ->html()
                    ->getStateUsing(function ($record) {
                        return $record->attributeValues
                            ->sortBy('sort_order')
                            ->take(6)
                            ->map(function ($val) use ($record) {
                                if ($record->display_type == 1 && $val->color_code) {
                                    $swatch = '<span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:'
                                        . e($val->color_code) . ';border:1px solid rgba(0,0,0,.15);vertical-align:middle;margin-right:3px"></span>';
                                    return '<span style="display:inline-flex;align-items:center;background:var(--gray-100,#f3f4f6);padding:2px 8px;border-radius:99px;font-size:12px;margin:1px">'
                                        . $swatch . e($val->value) . '</span>';
                                }
                                return '<span style="display:inline-block;background:var(--gray-100,#f3f4f6);padding:2px 8px;border-radius:99px;font-size:12px;margin:1px">'
                                    . e($val->value) . '</span>';
                            })
                            ->join(' ')
                            . ($record->attributeValues->count() > 6
                                ? ' <span style="font-size:11px;color:var(--gray-400)">+' . ($record->attributeValues->count() - 6) . ' nữa</span>'
                                : '');
                    }),

                TextColumn::make('attribute_values_count')
                    ->label('Tổng')
                    ->counts('attributeValues')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('display_type')
                    ->label('Kiểu')
                    ->options([0 => 'Text', 1 => 'Màu sắc']),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function ($record, DeleteAction $action) {
                        if ($record->attributeValues()->whereHas('variantAttributeValues')->exists()) {
                            Notification::make()
                                ->title('Không thể xóa')
                                ->body('Đang có biến thể sử dụng giá trị của thuộc tính này.')
                                ->danger()->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
