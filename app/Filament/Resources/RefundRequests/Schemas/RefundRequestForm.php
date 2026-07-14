<?php

namespace App\Filament\Resources\RefundRequests\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RefundRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reason')->disabled(),
                Textarea::make('reason_detail')->disabled()->columnSpanFull(),
                TextInput::make('refund_amount')->disabled()->numeric(),
                TextInput::make('status')->disabled()->numeric(),
            ]);
    }
}
