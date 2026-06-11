<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\ProductImage;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';
    protected static ?string $title = 'Thư viện ảnh';

    // ── Tính năng 3: Config giới hạn ảnh, mặc định 10 như WooCommerce ──
    protected int $maxImages = 10;

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
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
                    ->size(80)
                    ->defaultImageUrl(asset('images/no-image.png')),

                IconColumn::make('is_primary')
                    ->label('Ảnh chính')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning'),
            ])
            ->filters([])
            ->headerActions([
                Action::make('uploadImages')
                    ->label('Upload ảnh')
                    ->icon('heroicon-o-photo')
                    ->color('primary')
                    ->form(function () {
                        $existing   = $this->getOwnerRecord()->images()->count();
                        $canAdd     = max(0, $this->maxImages - $existing);

                        return [
                            FileUpload::make('images')
                                ->label("Chọn ảnh (còn thêm được {$canAdd} ảnh)")
                                ->image()
                                ->multiple()
                                ->directory('products/gallery')
                                ->imagePreviewHeight('120')
                                ->reorderable()
                                ->required()
                                ->maxFiles($canAdd > 0 ? $canAdd : 1)
                                ->helperText("Tối đa {$this->maxImages} ảnh / sản phẩm. Ảnh đầu tiên tự set làm ảnh chính nếu chưa có."),
                        ];
                    })
                    ->action(function (array $data): void {
                        $product       = $this->getOwnerRecord();
                        $images        = $data['images'] ?? [];
                        $existingCount = $product->images()->count();

                        if ($existingCount + count($images) > $this->maxImages) {
                            $canAdd = $this->maxImages - $existingCount;
                            Notification::make()
                                ->title("Chỉ được thêm tối đa {$canAdd} ảnh nữa (giới hạn {$this->maxImages} ảnh/sản phẩm)")
                                ->warning()->send();
                            return;
                        }

                        $hasPrimary  = $product->images()->where('is_primary', 1)->exists();
                        $currentSort = $product->images()->max('sort_order') ?? 0;

                        foreach ($images as $index => $path) {
                            $isPrimary = !$hasPrimary && $index === 0;

                            ProductImage::create([
                                'product_id' => $product->id,
                                'image_url'  => $path,
                                'is_primary' => $isPrimary ? 1 : 0,
                                'sort_order' => $currentSort + $index + 1,
                            ]);

                            if ($isPrimary) $hasPrimary = true;
                        }

                        Notification::make()
                            ->title('Đã upload ' . count($images) . ' ảnh')
                            ->success()->send();
                    }),
            ])
            ->recordActions([
                Action::make('setPrimary')
                    ->label('Set ảnh chính')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->hidden(fn ($record) => (bool) $record->is_primary)
                    ->action(function ($record): void {
                        $this->getOwnerRecord()->images()->where('is_primary', 1)->update(['is_primary' => 0]);
                        $record->update(['is_primary' => 1]);
                        Notification::make()->title('Đã set ảnh chính')->success()->send();
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