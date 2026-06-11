<?php

namespace App\Filament\Resources\AttributeTemplates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttributeTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Tên mẫu')
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('items_count')
                    ->label('Số thuộc tính')
                    ->counts('items')
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
