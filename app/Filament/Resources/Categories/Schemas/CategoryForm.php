<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Tên danh mục')
                ->required()
                ->maxLength(100)
                ->placeholder('VD: Áo Nam, Laptop...')
                ->live(debounce: 500)
                ->afterStateUpdated(function (string $operation, $state, callable $set) {
                    // Chỉ tự sinh slug khi tạo mới
                    if ($operation === 'create') {
                        $set('slug', Str::slug($state));
                    }
                }),

            TextInput::make('slug')
                ->label('Slug (URL)')
                ->required()
                ->maxLength(150)
                ->unique(Category::class, 'slug', ignoreRecord: true)
                ->placeholder('tu-dong-sinh-tu-ten')
                ->helperText('Tự động sinh từ tên, có thể chỉnh tay.'),

            Select::make('parent_id')
                ->label('Danh mục cha')
                ->options(function (?Category $record) {
                    // Loại bỏ chính nó và con của nó khỏi danh sách
                    $query = Category::whereNull('parent_id')->orderBy('name');

                    if ($record?->id) {
                        $query->where('id', '!=', $record->id);
                    }

                    return $query->pluck('name', 'id');
                })
                ->searchable()
                ->nullable()
                ->placeholder('— Là danh mục gốc —')
                ->helperText('Chỉ danh mục gốc mới có thể làm cha.'),

            Textarea::make('description')
                ->label('Mô tả')
                ->rows(3)
                ->maxLength(1000)
                ->placeholder('Mô tả ngắn về danh mục...')
                ->columnSpanFull(),
        ])->columns(2);
    }
}