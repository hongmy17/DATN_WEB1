<?php

namespace App\Filament\Resources\Attributes\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttributeValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'attributeValues';

    protected static ?string $recordTitleAttribute = 'value';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('value')
                ->label('Giá trị')
                ->required()
                ->maxLength(100)
                ->placeholder('VD: Đỏ, Xanh, 512GB...'),

            ColorPicker::make('color_code')
                ->label('Mã màu')
                ->helperText('Chỉ nhập khi thuộc tính là "Màu sắc"')
                ->visible(fn () => $this->ownerRecord?->display_type == 1),

            TextInput::make('sort_order')
                ->label('Thứ tự')
                ->numeric()
                ->default(0)
                ->helperText('Số càng nhỏ càng hiển thị lên trước'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('value')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('value')
                    ->label('Giá trị')
                    ->searchable(),

                TextColumn::make('color_code')
                    ->label('Mã màu')
                    ->badge()
                    ->visible(fn () => $this->ownerRecord?->display_type == 1),

                TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Thêm giá trị'),
            ])
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