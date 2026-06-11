<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\AttributeTemplate;
use App\Models\ProductCustomAttributeValue;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomAttributesRelationManager extends RelationManager
{
    protected static string $relationship = 'customAttributes';
    protected static ?string $title = 'Thuộc tính riêng';

    // ─── Form ─────────────────────────────────────────────────

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Tên thuộc tính')
                ->required()
                ->placeholder('VD: RAM, Màn hình, Bảo hành...')
                ->columnSpanFull(),

            // BUG 1 FIX: Dùng Repeater để nhập values thay vì TextInput('value')
            // vì product_custom_attributes không có cột 'value'.
            // Giá trị lưu vào bảng product_custom_attribute_values qua relation values().
            Repeater::make('values')
                ->label('Giá trị')
                ->relationship('values')
                ->schema([
                    TextInput::make('value')
                        ->label('Giá trị')
                        ->required()
                        ->placeholder('VD: 18GB, 6.1 inch OLED, 1 năm...'),
                ])
                ->defaultItems(1)
                ->addActionLabel('+ Thêm giá trị')
                ->columnSpanFull(),

            Toggle::make('is_visible')
                ->label('Hiện trên trang sản phẩm')
                ->default(true),
        ]);
    }

    // ─── Table ────────────────────────────────────────────────

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->width('40px')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Thuộc tính')
                    ->searchable(),

                TextColumn::make('values.value')
                    ->label('Giá trị')
                    ->badge()
                    ->separator(',')
                    ->color('gray'),

                IconColumn::make('is_visible')
                    ->label('Hiển thị')
                    ->boolean(),
            ])
            ->headerActions([
                Action::make('quick_generate')
                    ->label('⚡ Tạo nhanh theo danh mục')
                    ->color('success')
                    ->icon('heroicon-o-bolt')
                    ->requiresConfirmation()
                    ->modalHeading('Tạo thuộc tính nhanh')
                    ->modalDescription(function () {
                        $product  = $this->getOwnerRecord();
                        $template = AttributeTemplate::where('category_id', $product->category_id)
                            ->with('items')
                            ->first();

                        if (! $template) {
                            $catName = $product->category?->name ?? 'không xác định';
                            return "Danh mục \"{$catName}\" chưa có template. Vào Cấu hình → Mẫu thuộc tính để tạo.";
                        }

                        $existing = $product->customAttributes()
                            ->pluck('name')
                            ->map(fn($n) => strtolower(trim($n)));

                        $willCreate = $template->items
                            ->filter(fn($item) => ! $existing->contains(strtolower(trim($item->name))))
                            ->pluck('name');

                        $willSkip = $template->items
                            ->filter(fn($item) => $existing->contains(strtolower(trim($item->name))))
                            ->pluck('name');

                        $msg = "Template: {$template->name}\n";
                        if ($willCreate->isNotEmpty()) {
                            $msg .= "Sẽ tạo: " . $willCreate->join(', ') . ".\n";
                        }
                        if ($willSkip->isNotEmpty()) {
                            $msg .= "Bỏ qua (đã có): " . $willSkip->join(', ') . ".";
                        }

                        return $msg;
                    })
                    ->modalSubmitActionLabel('Tạo ngay')
                    ->action(function () {
                        $product  = $this->getOwnerRecord();
                        $template = AttributeTemplate::with('items')
                            ->where('category_id', $product->category_id)
                            ->first();

                        if (! $template) {
                            Notification::make()
                                ->title('Chưa có template cho danh mục này')
                                ->body('Vào Cấu hình → Mẫu thuộc tính để tạo.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $existing = $product->customAttributes()
                            ->pluck('name')
                            ->map(fn($n) => strtolower(trim($n)));

                        $maxSortOrder = $product->customAttributes()->max('sort_order') ?? 0;

                        $created = 0;
                        $skipped = 0;

                        foreach ($template->items->sortBy('sort_order') as $index => $item) {
                            if ($existing->contains(strtolower(trim($item->name)))) {
                                $skipped++;
                                continue;
                            }

                            $product->customAttributes()->create([
                                'name'       => $item->name,
                                'is_visible' => true,
                                'sort_order' => $maxSortOrder + $created + 1,
                            ]);

                            $created++;
                        }

                        if ($created === 0) {
                            Notification::make()
                                ->title("Tất cả {$skipped} thuộc tính đã tồn tại, không tạo thêm")
                                ->warning()
                                ->send();
                            return;
                        }

                        $msg = "Đã tạo {$created} thuộc tính";
                        if ($skipped > 0) {
                            $msg .= ", bỏ qua {$skipped} thuộc tính đã có";
                        }

                        Notification::make()
                            ->title($msg)
                            ->body('Nhấn vào từng dòng để điền giá trị.')
                            ->success()
                            ->send();
                    }),

                CreateAction::make()->label('+ Thêm thủ công'),
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