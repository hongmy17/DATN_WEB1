<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Placeholder::make('product_name')
                ->label('Sản phẩm')
                ->content(fn($record) => $record?->product?->name ?? '—'),

            Placeholder::make('user_name')
                ->label('Người đánh giá')
                ->content(fn($record) => $record?->user?->name ?? '—'),

            Placeholder::make('rating')
                ->label('Số sao')
                ->content(fn($record) => str_repeat('★', $record?->rating ?? 0) . str_repeat('☆', 5 - ($record?->rating ?? 0))),

            Placeholder::make('comment')
                ->label('Nội dung')
                ->content(fn($record) => $record?->comment ?? '(Không có nội dung)'),

           
        ]);
    }
}
