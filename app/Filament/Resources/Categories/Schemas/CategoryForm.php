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
                    // FIX: trước đây chỉ cho chọn danh mục GỐC làm cha (whereNull('parent_id')),
                    // nên thực chất chỉ tạo được tối đa 2 cấp. Giờ cho chọn BẤT KỲ danh mục
                    // nào làm cha (đa cấp không giới hạn), chỉ loại trừ chính nó + toàn bộ
                    // hậu duệ của nó (để không tạo vòng lặp cha-con vô hạn).
                    $query = Category::orderBy('name');

                    if ($record?->id) {
                        $excludeIds = array_merge([$record->id], $record->descendantIds());
                        $query->whereNotIn('id', $excludeIds);
                    }

                    // Thụt lề theo cấp để nhìn rõ cây phân cấp ngay trong dropdown
                    return $query->get()->mapWithKeys(fn (Category $cat) => [
                        $cat->id => str_repeat('— ', $cat->depth()) . $cat->name,
                    ]);
                })
                ->searchable()
                ->nullable()
                ->placeholder('— Là danh mục gốc —')
                ->helperText('Chọn bất kỳ danh mục nào làm cha — hỗ trợ đa cấp không giới hạn.'),

            Textarea::make('description')
                ->label('Mô tả')
                ->rows(3)
                ->maxLength(1000)
                ->placeholder('Mô tả ngắn về danh mục...')
                ->columnSpanFull(),
        ])->columns(2);
    }
}