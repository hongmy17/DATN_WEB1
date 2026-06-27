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
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
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

                // ── MỚI: hiện màu/giá trị thuộc tính ảnh này gắn với ──────
                TextColumn::make('attributeValue.value')
                    ->label('Gắn với màu / giá trị')
                    ->badge()
                    ->color('info')
                    ->placeholder('Ảnh chung')
                    ->description(fn ($record) => $record->attributeValue?->attribute->name),
            ])
            ->filters([])
            ->headerActions([
                // ── MỚI: tổng quan màu nào đã có ảnh, màu nào chưa ─────────────
                // Trước đây phải mở từng variant mới biết màu nào thiếu ảnh.
                // Giờ bấm 1 nút là thấy hết, giống bảng kiểm tra của Flatsome.
                Action::make('colorOverview')
                    ->label('Tổng quan theo màu')
                    ->icon('heroicon-o-swatch')
                    ->color('gray')
                    ->modalHeading('Tổng quan ảnh theo màu')
                    ->modalWidth('xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Đóng')
                    ->visible(fn () => ! empty($this->getColorAttributeOptions($this->getOwnerRecord())))
                    ->form(function (): array {
                        $product = $this->getOwnerRecord();

                        // Tất cả giá trị màu mà sản phẩm đang dùng (qua attribute đã chọn)
                        $colorValues = AttributeValue::whereIn(
                            'attribute_id',
                            $product->attributes()->where('display_type', 1)->pluck('attributes.id')
                        )->orderBy('sort_order')->get();

                        // Ảnh đã gắn theo từng attribute_value_id (Thư viện ảnh)
                        $imageCountByValue = $product->images()
                            ->whereNotNull('attribute_value_id')
                            ->selectRaw('attribute_value_id, count(*) as total')
                            ->groupBy('attribute_value_id')
                            ->reorder() // ← xóa ORDER BY sort_order kế thừa từ relation, không hợp lệ với GROUP BY
                            ->pluck('total', 'attribute_value_id');

                        // Variant nào theo màu đã có ảnh riêng (image hoặc gallery)
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
                                ? '<span style="display:inline-block;width:12px;height:12px;border-radius:50%;'
                                  . 'background:' . e($value->color_code) . ';margin-right:6px;vertical-align:middle;'
                                  . 'border:1px solid #ccc"></span>'
                                : '';

                            $status = $hasAny
                                ? '<span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:10px;font-size:11px">✓ Đã có ảnh</span>'
                                : '<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:10px;font-size:11px">⚠ Chưa có ảnh nào</span>';

                            return '<tr style="border-bottom:1px solid var(--gray-200)">'
                                . '<td style="padding:8px 10px">' . $swatch . e($value->value) . '</td>'
                                . '<td style="padding:8px 10px;font-size:12px">' . $libCount . ' ảnh (Thư viện)</td>'
                                . '<td style="padding:8px 10px;font-size:12px">' . $variantCount . ' biến thể có ảnh riêng</td>'
                                . '<td style="padding:8px 10px">' . $status . '</td>'
                                . '</tr>';
                        })->join('');

                        $html = '<div style="max-height:380px;overflow-y:auto;border:1px solid var(--gray-200);border-radius:8px">'
                            . '<table style="width:100%;border-collapse:collapse">'
                            . '<thead><tr style="background:var(--gray-50)">'
                            . '<th style="padding:8px 10px;text-align:left;font-size:11px">Màu</th>'
                            . '<th style="padding:8px 10px;text-align:left;font-size:11px">Thư viện ảnh</th>'
                            . '<th style="padding:8px 10px;text-align:left;font-size:11px">Biến thể</th>'
                            . '<th style="padding:8px 10px;text-align:left;font-size:11px">Trạng thái</th>'
                            . '</tr></thead><tbody>' . $rows . '</tbody></table></div>';

                        return [
                            \Filament\Forms\Components\Placeholder::make('overview')
                                ->label('')
                                ->content(new \Illuminate\Support\HtmlString($html)),
                        ];
                    }),

                Action::make('uploadImages')
                    ->label('Upload ảnh')
                    ->icon('heroicon-o-photo')
                    ->color('primary')
                    ->form(function () {
                        $product  = $this->getOwnerRecord();
                        $existing = $product->images()->count();
                        $canAdd   = max(0, $this->maxImages - $existing);

                        // ── MỚI: lấy danh sách attribute value của các attribute
                        //         thuộc loại màu sắc (display_type=1) để gắn ảnh
                        $colorOptions = $this->getColorAttributeOptions($product);

                        $fields = [
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

                        // ── MỚI: chỉ hiện select gắn màu nếu sản phẩm có
                        //         attribute màu sắc (display_type=1) ──────────
                        if (! empty($colorOptions)) {
                            $fields[] = Select::make('attribute_value_id')
                                ->label('Gắn ảnh này với màu / giá trị')
                                ->options($colorOptions)
                                ->nullable()
                                ->searchable()
                                ->placeholder('Ảnh chung (hiển thị cho tất cả biến thể)')
                                ->helperText('Áp dụng cho TẤT CẢ ảnh chọn ở trên trong lần upload này. '
                                    . 'Nếu các ảnh đang chọn không cùng 1 màu, hãy upload theo từng nhóm màu riêng, '
                                    . 'hoặc dùng "Gắn màu hàng loạt" ở bảng dưới sau khi upload xong.');
                        }

                        return $fields;
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

                        $hasPrimary         = $product->images()->where('is_primary', 1)->exists();
                        $currentSort        = $product->images()->max('sort_order') ?? 0;
                        $attributeValueId   = $data['attribute_value_id'] ?? null;

                        foreach ($images as $index => $path) {
                            $isPrimary = ! $hasPrimary && $index === 0;

                            ProductImage::create([
                                'product_id'          => $product->id,
                                'image_url'           => $path,
                                'is_primary'          => $isPrimary ? 1 : 0,
                                'sort_order'          => $currentSort + $index + 1,
                                'attribute_value_id'  => $attributeValueId, // ← MỚI
                            ]);

                            if ($isPrimary) {
                                $hasPrimary = true;
                            }
                        }

                        Notification::make()
                            ->title('Đã upload ' . count($images) . ' ảnh')
                            ->success()->send();
                    }),

                // ── MỚI: sửa màu cho từng ảnh khác nhau, lưu 1 lần duy nhất ──
                // VD: ảnh 1 → Đen, ảnh 2 → Đỏ, ảnh 3 → Hồng, sửa hết rồi
                // bấm "Lưu tất cả" 1 lần, không cần mở từng ảnh ra sửa riêng.
                Action::make('editAllColors')
                    ->label('Sửa màu hàng loạt')
                    ->icon('heroicon-o-pencil-square')
                    ->color('info')
                    ->visible(fn () => $this->getOwnerRecord()->images()->exists()
                        && ! empty($this->getColorAttributeOptions($this->getOwnerRecord())))
                    ->form(function (): array {
                        $product      = $this->getOwnerRecord();
                        $colorOptions = $this->getColorAttributeOptions($product);
                        $images       = $product->images()->orderBy('sort_order')->get();

                        $fields = [
                            \Filament\Forms\Components\Placeholder::make('hint')
                                ->label('')
                                ->content(new \Illuminate\Support\HtmlString(
                                    '<div style="font-size:13px;color:var(--gray-500);margin-bottom:4px">'
                                    . '💡 Chọn màu riêng cho từng ảnh bên dưới, xong bấm "Lưu tất cả" 1 lần.</div>'
                                )),
                        ];

                        foreach ($images as $img) {
                            $fields[] = \Filament\Schemas\Components\Grid::make(['default' => 4])
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make("preview_{$img->id}")
                                        ->label('')
                                        ->content(new \Illuminate\Support\HtmlString(
                                            '<img src="' . e($img->url) . '" style="width:64px;height:64px;'
                                            . 'object-fit:cover;border-radius:6px;border:1px solid var(--gray-200)">'
                                        ))
                                        ->columnSpan(1),

                                    Select::make("rows.{$img->id}")
                                        ->label($img->is_primary ? 'Ảnh chính' : 'Ảnh #' . $img->sort_order)
                                        ->options($colorOptions)
                                        ->nullable()
                                        ->searchable()
                                        ->placeholder('Ảnh chung (không gắn màu)')
                                        ->default($img->attribute_value_id)
                                        ->columnSpan(3),
                                ]);
                        }

                        return $fields;
                    })
                    ->modalHeading('Sửa màu cho từng ảnh')
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel('Lưu tất cả')
                    ->action(function (array $data): void {
                        $rows    = $data['rows'] ?? [];
                        $updated = 0;

                        foreach ($rows as $imageId => $attributeValueId) {
                            ProductImage::where('id', $imageId)->update([
                                'attribute_value_id' => $attributeValueId ?: null,
                            ]);
                            $updated++;
                        }

                        Notification::make()
                            ->title("Đã cập nhật màu cho {$updated} ảnh")
                            ->success()->send();
                    }),
            ])
            ->recordActions([
                // ── MỚI: action gắn/đổi màu cho ảnh đã upload ──────────────
                Action::make('linkColor')
                    ->label('Gắn màu')
                    ->icon('heroicon-o-swatch')
                    ->color('info')
                    ->form(function ($record) {
                        $product      = $this->getOwnerRecord();
                        $colorOptions = $this->getColorAttributeOptions($product);

                        return [
                            Select::make('attribute_value_id')
                                ->label('Gắn với màu / giá trị')
                                ->options($colorOptions)
                                ->default($record->attribute_value_id)
                                ->nullable()
                                ->searchable()
                                ->placeholder('Ảnh chung (không gắn màu cụ thể)'),
                        ];
                    })
                    ->action(function ($record, array $data): void {
                        $record->update([
                            'attribute_value_id' => $data['attribute_value_id'] ?? null,
                        ]);
                        Notification::make()->title('Đã cập nhật liên kết màu')->success()->send();
                    })
                    ->visible(fn () => ! empty($this->getColorAttributeOptions($this->getOwnerRecord()))),

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
                    // ── MỚI: gắn màu hàng loạt cho nhiều ảnh đã tick chọn ───
                    // Tick chọn các ảnh cùng 1 màu (vd 3 ảnh đều là "Đen")
                    // → chọn 1 lần "Màu sắc: Đen" → áp dụng cho tất cả,
                    // không cần bấm "Gắn màu" từng ảnh một như trước.
                    BulkAction::make('bulkLinkColor')
                        ->label('Gắn màu hàng loạt')
                        ->icon('heroicon-o-swatch')
                        ->color('info')
                        ->form(function () {
                            $colorOptions = $this->getColorAttributeOptions($this->getOwnerRecord());

                            if (empty($colorOptions)) {
                                return [
                                    \Filament\Forms\Components\Placeholder::make('empty')
                                        ->label('')
                                        ->content('Sản phẩm này chưa có thuộc tính kiểu màu sắc.'),
                                ];
                            }

                            return [
                                Select::make('attribute_value_id')
                                    ->label('Gắn tất cả ảnh đã chọn với màu / giá trị')
                                    ->options($colorOptions)
                                    ->nullable()
                                    ->searchable()
                                    ->placeholder('Ảnh chung (bỏ gắn màu)')
                                    ->helperText('Áp dụng cho toàn bộ ảnh đang được tick chọn.'),
                            ];
                        })
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $attributeValueId = $data['attribute_value_id'] ?? null;

                            ProductImage::whereIn('id', $records->pluck('id'))
                                ->update(['attribute_value_id' => $attributeValueId]);

                            Notification::make()
                                ->title('Đã gắn màu cho ' . $records->count() . ' ảnh')
                                ->success()->send();
                        })
                        ->visible(fn () => ! empty($this->getColorAttributeOptions($this->getOwnerRecord())))
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Lấy danh sách AttributeValue của các Attribute kiểu màu (display_type=1)
     * được gắn với sản phẩm này, dùng cho select "gắn ảnh với màu".
     *
     * Format: [id => "Màu sắc: Đen", ...]
     */
    private function getColorAttributeOptions($product): array
    {
        $colorAttributeIds = $product->attributes()
            ->where('display_type', 1)
            ->pluck('attributes.id');

        if ($colorAttributeIds->isEmpty()) {
            return [];
        }

        return AttributeValue::whereIn('attribute_id', $colorAttributeIds)
            ->with('attribute')
            ->get()
            ->mapWithKeys(fn ($v) => [
                $v->id => $v->attribute->name . ': ' . $v->value,
            ])
            ->toArray();
    }
}