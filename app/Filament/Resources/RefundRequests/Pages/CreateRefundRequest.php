<?php

namespace App\Filament\Resources\RefundRequests\Pages;

use App\Filament\Resources\RefundRequests\RefundRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRefundRequest extends CreateRecord
{
    protected static string $resource = RefundRequestResource::class;
}
