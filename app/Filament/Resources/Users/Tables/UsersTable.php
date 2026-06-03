<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                TextColumn::make('code')
                    ->label('Mã')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('name')
                    ->label('Họ và tên')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('SĐT')
                    ->searchable(),

                TextColumn::make('role')
                    ->label('Vai trò')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        1 => 'Admin',
                        default => 'Khách hàng',
                    })
                    ->color(fn($state) => match ($state) {
                        1 => 'danger',
                        default => 'success',
                    }),

                IconColumn::make('status')
                    ->label('Trạng thái')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('Asia/Ho_Chi_Minh'),

                TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('Asia/Ho_Chi_Minh'),
            ])

            ->defaultSort('id', 'desc')

            ->filters([
                //
            ])

            ->recordActions([
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
