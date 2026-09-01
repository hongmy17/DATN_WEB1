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
    protected static ?string $title = 'Thông số kỹ thuật';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Tên thông số')
                ->required()
                ->placeholder('VD: RAM, Màn hình, Bảo hành...')
                ->columnSpanFull(),

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
                    ->label('Thông số')
                    ->searchable()
                    ->weight('medium'),

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
                // ── Tạo nhanh từ template danh mục ───────────────────────────
                Action::make('quickGenerate')
                    ->label('Tạo nhanh từ mẫu thông số kỹ thuật')
                    ->icon('heroicon-o-bolt')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tạo thông số kỹ thuật nhanh')
                    ->modalDescription(function () {
                        $product  = $this->getOwnerRecord();
                        $template = AttributeTemplate::where('category_id', $product->category_id)
                            ->with('items')
                            ->first();

                        if (! $template) {
                            $cat = $product->category?->name ?? 'không xác định';
                            return "Danh mục \"{$cat}\" chưa có mẫu. Vào Cấu hình → Thông số kỹ thuật để tạo.";
                        }

                        $existing   = $product->customAttributes()->pluck('name')
                            ->map(fn($n) => strtolower(trim($n)));
                        $willCreate = $template->items
                            ->filter(fn($item) => ! $existing->contains(strtolower(trim($item->name))))
                            ->pluck('name');
                        $willSkip   = $template->items
                            ->filter(fn($item) => $existing->contains(strtolower(trim($item->name))))
                            ->pluck('name');

                        $msg = "Mẫu: {$template->name}\n";
                        if ($willCreate->isNotEmpty()) {
                            $msg .= 'Sẽ tạo: ' . $willCreate->join(', ') . ".\n";
                        }
                        if ($willSkip->isNotEmpty()) {
                            $msg .= 'Bỏ qua (đã có): ' . $willSkip->join(', ') . '.';
                        }
                        return $msg;
                    })
                    ->modalSubmitActionLabel('Tạo ngay')
                    ->action(function (): void {
                        $product  = $this->getOwnerRecord();
                        $template = AttributeTemplate::with('items')
                            ->where('category_id', $product->category_id)
                            ->first();

                        if (! $template) {
                            Notification::make()
                                ->title('Chưa có mẫu cho danh mục này')
                                ->body('Vào Cấu hình → Thông số kỹ thuật để tạo.')
                                ->warning()->send();
                            return;
                        }

                        $existing      = $product->customAttributes()->pluck('name')
                            ->map(fn($n) => strtolower(trim($n)));
                        $maxSortOrder  = $product->customAttributes()->max('sort_order') ?? 0;
                        $created       = 0;
                        $skipped       = 0;

                        foreach ($template->items->sortBy('sort_order') as $i => $item) {
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
                                ->title("Tất cả {$skipped} thông số đã tồn tại")
                                ->warning()->send();
                            return;
                        }

                        $msg = "Đã tạo {$created} thông số";
                        if ($skipped > 0) {
                            $msg .= ", bỏ qua {$skipped} đã có";
                        }
                        Notification::make()->title($msg)->body('Bấm vào từng dòng để điền giá trị.')->success()->send();
                    }),

                CreateAction::make()->label('Thêm thủ công'),
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
