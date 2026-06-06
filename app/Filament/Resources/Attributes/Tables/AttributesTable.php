<?php

namespace App\Filament\Resources\Attributes\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttributesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Tên thuộc tính')
                    ->searchable(),

                TextColumn::make('display_type')
                    ->label('Kiểu')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Màu sắc' : 'Text')
                    ->color(fn($state) => $state == 1 ? 'success' : 'gray'),

                TextColumn::make('attribute_values_count')
                    ->label('Số giá trị')
                    ->counts('attributeValues'),

                TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->date('d/m/Y'),
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