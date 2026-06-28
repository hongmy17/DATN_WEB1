<?php

namespace App\Filament\Resources\AttributeTemplates\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Danh sách thông số';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Tên thông số')
                ->required()
                ->maxLength(100)
                ->placeholder('VD: RAM, Màn hình, Xuất xứ, Bảo hành...'),

            TextInput::make('sort_order')
                ->hiddenLabel()->numeric()->default(0)->hidden()->dehydrated(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('sort_order')->label('STT')->sortable()->width('50px'),
                TextColumn::make('name')->label('Tên thông số')->searchable()->weight('medium'),
            ])
            ->headerActions([CreateAction::make()->label('+ Thêm thông số')])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}