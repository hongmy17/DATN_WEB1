<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('coupon_id')
                    ->numeric(),
                TextInput::make('address_id')
                    ->numeric(),
                TextInput::make('receiver_name')
                    ->required(),
                TextInput::make('receiver_phone')
                    ->tel()
                    ->required(),
                Textarea::make('shipping_address')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('order_status')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                Textarea::make('note')
                    ->columnSpanFull(),
            ]);
    }
}
