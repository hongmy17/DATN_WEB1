<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\Products\RelationManagers\VariantsRelationManager;
use App\Filament\Resources\Products\RelationManagers\CustomAttributesRelationManager;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;
    protected static ?string $navigationLabel = 'Sản phẩm';
    protected static ?string $modelLabel = 'Sản phẩm';
    protected static ?string $pluralModelLabel = 'Danh sách sản phẩm';
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
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ImagesRelationManager::class,
            VariantsRelationManager::class,
            CustomAttributesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit'   => EditProduct::route('/{record}/edit'),
        ];
    }
}
