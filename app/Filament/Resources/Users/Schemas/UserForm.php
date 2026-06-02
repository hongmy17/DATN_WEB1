<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                self::leftColumnComponents(),
                self::rightColumnComponents(),
            ]);
    }

    private static function leftColumnComponents(): Section
    {
        return Section::make('Thông tin cá nhân')
            ->icon('heroicon-o-user-circle')
            ->columnSpan(1)
            ->schema([
                FileUpload::make('avatar')
                    ->label('Ảnh đại diện')
                    ->image()
                    ->avatar()
                    ->disk('public')
                    ->directory('avatars')
                    ->visibility('public')
                    ->dehydrated(true)
                    ->columnSpanFull(),

                TextInput::make('code')
                    ->label('Mã người dùng')
                   
                    ->required()
                    ->placeholder('VD: USER000001 hoặc CUS-001')
                    ->default(function () {
                        $lastUser = User::latest('id')->first();

                        if (! $lastUser) {
                            return 'USER000001';
                        }

                        $number = (int) substr($lastUser->code, 4);

                        return 'USER' . str_pad($number + 1, 6, '0', STR_PAD_LEFT);
                    }),

                TextInput::make('full_name')
                    ->label('Họ và tên')
                   
                    ->required()
                    ->placeholder('VD: Nguyễn Văn A')
                    ->maxLength(255)
                    ->minLength(2)
                    ->rule('regex:/^[\p{L}\s]+$/u'),

                TextInput::make('email')
                    ->label('Email')
                   
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true, table: User::class, column: 'email')
                    ->validationMessages([
                        'unique' => 'Email này đã được sử dụng. Vui lòng chọn email khác.',
                    ])
                    ->placeholder('VD: nguyenvana@example.com')
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Số điện thoại')
                  
                    ->tel()
                    ->placeholder('VD: 0912345678')
                    ->regex('/^([0-9]{10,11})$/')
                    ->validationMessages([
                        'regex' => 'Số điện thoại phải có 10-11 chữ số.',
                    ]),
            ]);
    }

    private static function rightColumnComponents(): Section
    {
        return Section::make('Thông tin tài khoản')
            ->icon('heroicon-o-key')
            ->columnSpan(1)
            ->schema([
                TextInput::make('password')
                    ->label('Mật khẩu')
                    ->password()
                    
                    ->placeholder('VD: password123')
                    ->minLength(6)
                    ->validationMessages([
                        'min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
                    ])
                    ->maxLength(255)
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),

                Select::make('role')
                    ->label('Vai trò')
                    ->prefix('👑')
                    ->options([
                        0 => ' Khách hàng',
                        1 => ' Quản trị viên',
                    ])
                    ->default(0)
                    ->required()
                    ->placeholder('Chọn vai trò')
                    ->validationMessages([
                        'required' => 'Vui lòng chọn vai trò.',
                    ]),

                Toggle::make('status')
                    ->label('Kích hoạt')
                    ->default(true),
            ]);
    }
}