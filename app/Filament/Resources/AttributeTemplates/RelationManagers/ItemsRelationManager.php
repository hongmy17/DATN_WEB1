<?php

namespace App\Filament\Resources\AttributeTemplates\RelationManagers;

use App\Models\Attribute;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Danh sách thuộc tính';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            /**
             * FIX 4: attribute_id PHẢI được map để ProductForm auto-fill hoạt động.
             * Khi admin chọn Attribute ở đây, ProductForm sẽ tự điền vào
             * "Thuộc tính sản phẩm" khi chọn danh mục có template này.
             *
             * Các item cũ (attribute_id = NULL) sẽ bị bỏ qua khi auto-fill —
             * hãy vào edit từng item để map lại.
             */
            Select::make('attribute_id')
                ->label('Thuộc tính toàn cục')
                ->options(Attribute::orderBy('name')->pluck('name', 'id'))
                // FIX 4: bỏ ->required() để không vỡ validation với item cũ
                // Admin nên map đủ để auto-fill hoạt động, nhưng không bắt buộc
                ->nullable()
                ->searchable()
                ->placeholder('Chọn để auto-fill vào ProductForm...')
                ->helperText(
                    '⚠️ Nếu để trống, danh mục này sẽ không tự điền thuộc tính khi tạo sản phẩm.'
                )
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $attr = Attribute::find($state);
                        if ($attr) {
                            $set('name', $attr->name);
                        }
                    }
                })
                ->live(),

            // Giữ lại name để tương thích ngược với các item cũ chưa có attribute_id
            TextInput::make('name')
                ->label('Tên hiển thị')
                ->required()
                ->maxLength(100)
                ->placeholder('VD: RAM, Màn hình, Pin...')
                ->helperText('Tự điền khi chọn thuộc tính ở trên. Có thể sửa lại.'),

            TextInput::make('sort_order')
                ->label('Thứ tự')
                ->numeric()
                ->default(0)
                ->disabled()
                ->dehydrated(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('STT')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Tên thuộc tính')
                    ->searchable(),

                // Hiện tên Attribute đã map — nếu NULL thì badge "Chưa map"
                // FIX 4: item "Chưa map" sẽ không auto-fill vào ProductForm
                TextColumn::make('attribute.name')
                    ->label('Attribute toàn cục')
                    ->badge()
                    ->color(fn ($state) => $state ? 'primary' : 'warning')
                    ->placeholder('⚠️ Chưa map — cần Edit để chọn'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Thêm thuộc tính'),
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
