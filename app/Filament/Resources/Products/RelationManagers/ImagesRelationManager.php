<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Thư viện ảnh';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('image_url')
                ->label('Ảnh sản phẩm')
                ->image()
                ->directory('products/gallery')
                ->imagePreviewHeight('150')
                ->required(),

            Toggle::make('is_primary')
                ->label('Đặt làm ảnh chính')
                ->default(false)
                ->helperText('Mỗi sản phẩm chỉ có 1 ảnh chính — ảnh chính cũ sẽ tự động bỏ chọn'),
        
            TextInput::make('sort_order')
                ->label('Thứ tự hiển thị')
                ->numeric()
                ->default(0)
                ->helperText('Số nhỏ hơn hiển thị trước'),
        ]);
    }

    /**
     * Khi set is_primary = true, unset tất cả ảnh chính cũ của cùng sản phẩm.
     */
    private function enforceSinglePrimary(array $data, ?int $excludeId = null): void
    {
        if (!empty($data['is_primary'])) {
            $query = $this->getOwnerRecord()->images()->where('is_primary', 1);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            $query->update(['is_primary' => 0]);
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('image_url')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Ảnh')
                    ->size(64)
                    ->defaultImageUrl(asset('images/no-image.png')),

                IconColumn::make('is_primary')
                    ->label('Ảnh chính')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning'),

                TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->label('Thêm ảnh')
                    ->mutateFormDataUsing(function (array $data): array {
                        $this->enforceSinglePrimary($data);
                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data, $record): array {
                        $this->enforceSinglePrimary($data, $record->id);
                        return $data;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
