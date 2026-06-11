<?php
namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('coupon_code')
                    ->label('Mã giảm giá')
                    ->required()
                    ->maxLength(50)
                    
                    ->unique(ignoreRecord: true),

                Select::make('type')
                    ->label('Loại giảm giá')
                    ->required()
                    ->default(0)
                    ->native(false)
                    ->options([
                        0 => 'Giảm theo % ',
                        1 => 'Giảm số tiền cố định',
                    ]),

                TextInput::make('value')
                    ->label('Giá trị giảm')
                    ->required()
                    ->numeric()
                    ->minValue(0),

                TextInput::make('min_order_value')
                    ->label('Giá trị đơn tối thiểu')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                TextInput::make('max_usage')
                    ->label('Số lần dùng tối đa')
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Để trống = không giới hạn'),

                TextInput::make('used_count')
                    ->label('Số lần đã dùng')
                    ->numeric()
                    ->default(0)
                    ->disabled(),

                DateTimePicker::make('start_date')
                    ->label('Ngày bắt đầu')
                    ->required(),

                DateTimePicker::make('end_date')
                    ->label('Ngày kết thúc')
                    ->required(),

                Toggle::make('status')
                    ->label('Kích hoạt')
                    ->default(true),
            ]);
    }
}