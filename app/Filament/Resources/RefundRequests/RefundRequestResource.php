<?php

namespace App\Filament\Resources\RefundRequests;

use App\Filament\Resources\RefundRequests\Pages\ListRefundRequests;
use App\Filament\Resources\RefundRequests\Schemas\RefundRequestForm;
use App\Filament\Resources\RefundRequests\Tables\RefundRequestsTable;
use App\Models\RefundRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RefundRequestResource extends Resource
{
    protected static ?string $model = RefundRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptRefund;

    protected static ?string $navigationLabel = 'Yêu cầu hoàn tiền';
    protected static ?string $modelLabel = 'Yêu cầu hoàn tiền';
    protected static ?string $pluralModelLabel = 'Yêu cầu hoàn tiền';

    // Admin không tự tạo yêu cầu hoàn tiền — chỉ khách hàng mới tạo được
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return RefundRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RefundRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRefundRequests::route('/'),
        ];
    }
}
