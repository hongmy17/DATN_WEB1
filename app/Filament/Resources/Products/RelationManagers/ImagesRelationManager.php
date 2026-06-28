<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\AttributeValue;
use App\Models\ProductImage;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';
    protected static ?string $title = 'Thư viện ảnh';

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
                    ->size(72)
                    ->defaultImageUrl(asset('images/no-image.png'))
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),

                IconColumn::make('is_primary')
                    ->label('Ảnh chính')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning'),

                TextColumn::make('attributeValue.value')
                    ->label('Gắn với màu')
                    ->badge()
                    ->color('info')
                    ->placeholder('Ảnh chung')
                    ->description(fn ($record) => $record->attributeValue?->attribute->name),

                TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([])
            ->headerActions([
                // ── Upload ảnh ───────────────────────────────────────────────
                Action::make('uploadImages')
                    ->label('Upload ảnh')
                    ->icon('heroicon-o-photo')
                    ->color('primary')
                    ->form(function () {
                        $product  = $this->getOwnerRecord();
                        $existing = $product->images()->count();
                        $canAdd   = max(0, $this->maxImages - $existing);

                        $fields = [
                            FileUpload::make('images')
                                ->label("Chọn ảnh (còn thêm được {$canAdd} / tối đa {$this->maxImages})")
                                ->image()
                                ->multiple()
                                ->directory('products/gallery')
                                ->imagePreviewHeight('120')
                                ->reorderable()
                                ->required()
                                ->maxFiles($canAdd > 0 ? $canAdd : 1),
                        ];

                        $colorOptions = $this->getColorOptions($product);
                        if (! empty($colorOptions)) {
                            $fields[] = Select::make('attribute_value_id')
                                ->label('Gắn với màu / giá trị')
                                ->options($colorOptions)
                                ->nullable()
                                ->searchable()
                                ->placeholder('Ảnh chung (hiển thị cho tất cả màu)')
                                ->helperText('Áp dụng cho tất cả ảnh trong lần upload này. Muốn gắn màu khác nhau: upload từng nhóm riêng.');
                        }

                        return $fields;
                    })
                    ->action(function (array $data): void {
                        $product       = $this->getOwnerRecord();
                        $images        = $data['images'] ?? [];
                        $existingCount = $product->images()->count();

                        if ($existingCount + count($images) > $this->maxImages) {
                            Notification::make()
                                ->title('Vượt giới hạn ảnh')
                                ->body("Chỉ còn thể thêm " . ($this->maxImages - $existingCount) . " ảnh nữa.")
                                ->warning()->send();
                            return;
                        }

                        $hasPrimary       = $product->images()->where('is_primary', 1)->exists();
                        $currentSort      = $product->images()->max('sort_order') ?? 0;
                        $attributeValueId = $data['attribute_value_id'] ?? null;

                        foreach ($images as $index => $path) {
                            $isPrimary = ! $hasPrimary && $index === 0;
                            ProductImage::create([
                                'product_id'         => $product->id,
                                'image_url'          => $path,
                                'is_primary'         => $isPrimary ? 1 : 0,
                                'sort_order'         => $currentSort + $index + 1,
                                'attribute_value_id' => $attributeValueId,
                            ]);
                            if ($isPrimary) {
                                $hasPrimary = true;
                            }
                        }

                        Notification::make()
                            ->title('Đã upload ' . count($images) . ' ảnh')
                            ->success()->send();
                    }),

                // ── Tổng quan màu ────────────────────────────────────────────
                Action::make('colorOverview')
                    ->label('Kiểm tra màu')
                    ->icon('heroicon-o-swatch')
                    ->color('gray')
                    ->modalHeading('Tổng quan ảnh theo màu')
                    ->modalWidth('xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Đóng')
                    ->visible(fn () => ! empty($this->getColorOptions($this->getOwnerRecord())))
                    ->form(function (): array {
                        $product = $this->getOwnerRecord();

                        $colorValues = AttributeValue::whereIn(
                            'attribute_id',
                            $product->attributes()->where('display_type', 1)->pluck('attributes.id')
                        )->orderBy('sort_order')->get();

                        // FIX: dùng query gốc từ Model ProductImage (không qua relation
                        // $product->images() vì relation đó tự gắn sẵn orderBy('sort_order'),
                        // gây lỗi MySQL 1055 (only_full_group_by) khi kết hợp với groupBy().
                        // reorder() đôi khi không loại bỏ kịp order đã gắn từ relation,
                        // nên tạo query mới sạch là cách chắc chắn nhất.
                        $imageCountByValue = ProductImage::query()
                            ->where('product_id', $product->id)
                            ->whereNotNull('attribute_value_id')
                            ->selectRaw('attribute_value_id, count(*) as total')
                            ->groupBy('attribute_value_id')
                            ->pluck('total', 'attribute_value_id');

                        $variantsWithImage = $product->variants()
                            ->with('attributeValues')
                            ->get()
                            ->filter(fn ($v) => $v->image || ! empty($v->gallery));

                        $rows = $colorValues->map(function ($value) use ($imageCountByValue, $variantsWithImage) {
                            $libCount     = $imageCountByValue[$value->id] ?? 0;
                            $variantCount = $variantsWithImage->filter(
                                fn ($v) => $v->attributeValues->pluck('id')->contains($value->id)
                            )->count();

                            $hasAny = $libCount > 0 || $variantCount > 0;
                            $swatch = $value->color_code
                                ? '<span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:' . e($value->color_code) . ';margin-right:6px;vertical-align:middle;border:1px solid #ccc"></span>'
                                : '';

                            $statusBadge = $hasAny
                                ? '<span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:10px;font-size:11px">✓ Đã có ảnh</span>'
                                : '<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:10px;font-size:11px">⚠ Chưa có ảnh</span>';

                            return '<tr style="border-bottom:1px solid var(--gray-200)">'
                                . '<td style="padding:8px 10px">' . $swatch . e($value->value) . '</td>'
                                . '<td style="padding:8px 10px;font-size:12px">' . $libCount . ' ảnh thư viện</td>'
                                . '<td style="padding:8px 10px;font-size:12px">' . $variantCount . ' biến thể có ảnh riêng</td>'
                                . '<td style="padding:8px 10px">' . $statusBadge . '</td>'
                                . '</tr>';
                        })->join('');

                        $html = '<div style="max-height:380px;overflow-y:auto;border:1px solid var(--gray-200);border-radius:8px">'
                            . '<table style="width:100%;border-collapse:collapse">'
                            . '<thead><tr style="background:var(--gray-50)">'
                            . '<th style="padding:8px 10px;text-align:left;font-size:11px">Màu</th>'
                            . '<th style="padding:8px 10px;text-align:left;font-size:11px">Thư viện</th>'
                            . '<th style="padding:8px 10px;text-align:left;font-size:11px">Biến thể</th>'
                            . '<th style="padding:8px 10px;text-align:left;font-size:11px">Trạng thái</th>'
                            . '</tr></thead><tbody>' . $rows . '</tbody></table></div>';

                        return [
                            Placeholder::make('overview')
                                ->label('')
                                ->content(new \Illuminate\Support\HtmlString($html)),
                        ];
                    }),
            ])
            ->recordActions([
                // Set ảnh chính
                Action::make('setPrimary')
                    ->label('Đặt làm ảnh chính')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->hidden(fn ($record) => (bool) $record->is_primary)
                    ->action(function ($record): void {
                        $this->getOwnerRecord()->images()->where('is_primary', 1)->update(['is_primary' => 0]);
                        $record->update(['is_primary' => 1]);
                        Notification::make()->title('Đã đặt ảnh chính')->success()->send();
                    }),

                // Gắn màu cho ảnh
                Action::make('linkColor')
                    ->label('Gắn màu')
                    ->icon('heroicon-o-swatch')
                    ->color('info')
                    ->visible(fn () => ! empty($this->getColorOptions($this->getOwnerRecord())))
                    ->form(function ($record) {
                        return [
                            Select::make('attribute_value_id')
                                ->label('Gắn với màu / giá trị')
                                ->options($this->getColorOptions($this->getOwnerRecord()))
                                ->default($record->attribute_value_id)
                                ->nullable()
                                ->searchable()
                                ->placeholder('Ảnh chung (không gắn màu)'),
                        ];
                    })
                    ->action(function ($record, array $data): void {
                        $record->update(['attribute_value_id' => $data['attribute_value_id'] ?? null]);
                        Notification::make()->title('Đã cập nhật màu cho ảnh')->success()->send();
                    }),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Gắn màu hàng loạt
                    BulkAction::make('bulkLinkColor')
                        ->label('Gắn màu hàng loạt')
                        ->icon('heroicon-o-swatch')
                        ->color('info')
                        ->visible(fn () => ! empty($this->getColorOptions($this->getOwnerRecord())))
                        ->form(function () {
                            $colorOptions = $this->getColorOptions($this->getOwnerRecord());
                            return [
                                Select::make('attribute_value_id')
                                    ->label('Gắn tất cả ảnh đã chọn với màu')
                                    ->options($colorOptions)
                                    ->nullable()
                                    ->searchable()
                                    ->placeholder('Ảnh chung (bỏ gắn màu)')
                                    ->helperText('Áp dụng cho toàn bộ ảnh đang được chọn.'),
                            ];
                        })
                        ->action(function (Collection $records, array $data): void {
                            ProductImage::whereIn('id', $records->pluck('id'))
                                ->update(['attribute_value_id' => $data['attribute_value_id'] ?? null]);
                            Notification::make()
                                ->title('Đã gắn màu cho ' . $records->count() . ' ảnh')
                                ->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    /**
     * Trả về danh sách AttributeValue của các Attribute kiểu màu (display_type=1)
     * đang được gắn với sản phẩm này. Format: [id => "Màu sắc: Đen"].
     */
    private function getColorOptions($product): array
    {
        $colorAttributeIds = $product->attributes()
            ->where('display_type', 1)
            ->pluck('attributes.id');

        if ($colorAttributeIds->isEmpty()) {
            return [];
        }

        return AttributeValue::whereIn('attribute_id', $colorAttributeIds)
            ->with('attribute')
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn ($v) => [$v->id => $v->attribute->name . ': ' . $v->value])
            ->toArray();
    }
}