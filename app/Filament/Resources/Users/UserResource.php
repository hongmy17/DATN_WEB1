<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static ?string $navigationLabel = 'Người dùng';
    protected static ?string $modelLabel = 'Người dùng';
    protected static ?string $pluralModelLabel = 'Người dùng';


    protected static ?string $recordTitleAttribute = 'name';


    /**
     * Cho phép trang quản trị NHÌN THẤY các bản ghi đã xóa mềm.
     *
     * Mặc định, trait SoftDeletes gắn một "global scope" tự thêm điều kiện
     * `WHERE deleted_at IS NULL` vào mọi câu truy vấn — kể cả truy vấn của
     * Filament. Hệ quả: bản ghi đã xóa biến mất hoàn toàn, và nút "Khôi phục"
     * trở nên vô dụng vì không có cách nào mở được bản ghi đó ra.
     *
     * Gỡ scope này ở đây KHÔNG làm lộ bản ghi đã xóa: bộ lọc "Thùng rác"
     * (TrashedFilter) trong bảng mặc định vẫn chỉ hiện bản ghi còn hoạt động.
     * Admin phải chủ động chuyển bộ lọc mới thấy chúng.
     *
     * Lưu ý: chỉ áp dụng cho panel admin. Phía khách hàng vẫn dùng truy vấn
     * bình thường nên không bao giờ thấy dữ liệu đã xóa.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
