<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\RelationManagers\ChildrenRelationManager;
use App\Filament\Resources\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Categories\Tables\CategoriesTable;
use App\Models\Category;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Danh mục';
    protected static ?string $modelLabel = 'Danh mục';
    protected static ?string $pluralModelLabel = 'Danh mục';
    protected static ?string $recordTitleAttribute = 'name';

    protected static UnitEnum|string|null $navigationGroup = 'Quản lý sản phẩm';
    protected static ?int $navigationSort = 1;


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
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ChildrenRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit'   => EditCategory::route('/{record}/edit'),
        ];
    }
}
