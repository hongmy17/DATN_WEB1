<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
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
                ->afterStateUpdated(fn(string $operation, $state, callable $set) =>
                $operation === 'create' ? $set('slug', Str::slug($state)) : null),

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
                EditAction::make()->label('Sửa'),

                // Cùng quy tắc với danh mục cha: còn sản phẩm thì ẩn nút xóa.
                DeleteAction::make()
                    ->label('Xóa')
                    ->visible(fn($record) => (int) $record->products_count === 0)
                    ->before(function ($record, $action) {
                        if ($record->products()->exists()) {
                            Notification::make()
                                ->title('Không thể xóa!')
                                ->body('Danh mục con đang có sản phẩm.')
                                ->danger()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->headerActions([
                CreateAction::make()->label('Thêm danh mục con'),
            ]);
    }
}
