<?php

namespace App\Filament\Resources\AttributeTemplates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttributeTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable()->width('50px'),

                TextColumn::make('name')
                    ->label('Tên mẫu')->searchable()->weight('medium'),

                TextColumn::make('category.name')
                    ->label('Danh mục')->badge()->color('info')->sortable()->searchable(),

                TextColumn::make('items_count')
                    ->label('Số thuộc tính')->counts('items')
                    ->badge()->color(fn ($state) => $state > 0 ? 'success' : 'danger')->alignCenter(),


                TextColumn::make('created_at')
                    ->label('Tạo lúc')->date('d/m/Y')->sortable()->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')->label('Danh mục')
                    ->relationship('category', 'name')->searchable()->preload(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}