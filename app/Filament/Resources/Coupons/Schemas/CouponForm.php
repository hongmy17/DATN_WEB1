<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
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
                    ->unique(ignoreRecord: true)
                    ->placeholder('VD: SUMMER25'),

                Select::make('type')
                    ->label('Loại giảm giá')
                    ->required()
                    ->default(0)
                    ->native(false)
                    ->live()
                    ->options([
                        0 => 'Giảm theo %',
                        1 => 'Giảm số tiền cố định',
                    ]),

                TextInput::make('value')
                    ->label(fn (Get $get): string =>
                        (int) $get('type') === 0 ? 'Phần trăm giảm (%)' : 'Số tiền giảm (₫)'
                    )
                    ->required()
                    ->numeric()
                    ->minValue(0.01)
                    ->maxValue(fn (Get $get): ?int => (int) $get('type') === 0 ? 100 : null)
                    ->suffix(fn (Get $get): string => (int) $get('type') === 0 ? '%' : '₫'),

                // Chỉ hiện khi type = % (0)
                TextInput::make('max_discount')
                    ->label('Giảm tối đa (₫)')
                    ->numeric()
                    ->minValue(0)
                    ->suffix('₫')
                    ->placeholder('Để trống = không giới hạn')
                    ->helperText('Số tiền giảm tối đa khi dùng loại %. VD: giảm 20% nhưng tối đa 100.000₫.')
                    ->visible(fn (Get $get): bool => (int) $get('type') === 0),

                TextInput::make('min_order_value')
                    ->label('Giá trị đơn tối thiểu (₫)')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->suffix('₫')
                    ->helperText('Đặt 0 = không yêu cầu giá trị tối thiểu.'),

                TextInput::make('max_usage')
                    ->label('Số lần dùng tối đa')
                    ->numeric()
                    ->minValue(1)
                    ->placeholder('Để trống = không giới hạn'),

                TextInput::make('used_count')
                    ->label('Số lần đã dùng')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(false),

                DateTimePicker::make('start_date')
                    ->label('Ngày bắt đầu')
                    ->required()
                    ->native(false)
                    ->live(),

                DateTimePicker::make('end_date')
                    ->label('Ngày kết thúc')
                    ->required()
                    ->native(false)
                    ->after('start_date')
                    ->helperText('Phải sau ngày bắt đầu.'),

                Toggle::make('status')
                    ->label('Kích hoạt')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }
}