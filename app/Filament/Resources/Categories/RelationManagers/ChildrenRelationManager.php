<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';
    protected static ?string $title = 'Danh mục con';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Tên danh mục con')
                ->required()
                ->maxLength(100)
                ->live(debounce: 500)
                ->afterStateUpdated(fn (string $operation, $state, callable $set) =>
                    $operation === 'create' ? $set('slug', Str::slug($state)) : null
                ),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(150)
                ->unique('categories', 'slug', ignoreRecord: true),

            Textarea::make('description')
                ->label('Mô tả')
                ->rows(2)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('name')->label('Tên')->searchable(),
                TextColumn::make('slug')->label('Slug')->color('gray'),
                TextColumn::make('products_count')
                    ->label('Sản phẩm')
                    ->counts('products')
                    ->badge()
                    ->color('warning'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()->label('Thêm danh mục con'),
            ]);
    }
}