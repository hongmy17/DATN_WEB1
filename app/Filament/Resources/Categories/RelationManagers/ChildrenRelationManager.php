<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
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
            ->filters([
                TrashedFilter::make()->label('Thùng rác'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Sửa')
                    ->visible(fn($record) => ! $record->trashed()),

                // Cùng quy tắc với danh mục cha: còn sản phẩm thì ẩn nút xóa.
                DeleteAction::make()
                    ->label('Chuyển vào thùng rác')
                    ->visible(fn($record) => ! $record->trashed()
                        && (int) $record->products_count === 0)
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

                RestoreAction::make()->label('Khôi phục'),

                ForceDeleteAction::make()
                    ->label('Xóa vĩnh viễn')
                    ->requiresConfirmation()
                    ->before(function ($record, ForceDeleteAction $action) {
                        // withTrashed(): một danh mục con trông rỗng vẫn có thể
                        // đang giữ sản phẩm trong thùng rác — xóa cứng khi đó sẽ
                        // ném lỗi khóa ngoại (products.category_id là restrict).
                        if ($record->products()->withTrashed()->exists()) {
                            Notification::make()
                                ->title('Không thể xóa vĩnh viễn')
                                ->body('Danh mục con vẫn còn sản phẩm (kể cả sản phẩm trong thùng rác).')
                                ->danger()
                                ->persistent()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->headerActions([
                CreateAction::make()->label('Thêm danh mục con'),
            ]);
    }
}
