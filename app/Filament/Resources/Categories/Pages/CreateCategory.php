<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    // Danh mục mới tạo luôn xếp cuối danh sách thay vì mặc định sort_order = 0
    // (nếu không sẽ chồng lên vị trí đầu tiên, phải kéo tay lại mỗi lần tạo mới).
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sort_order'] = (int) \App\Models\Category::max('sort_order') + 1;
        return $data;
    }
}