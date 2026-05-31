<?php

namespace App\Filament\Resources\Clientes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
    TextColumn::make('cedula_ruc')
        ->label('Cédula / RUC')
        ->searchable(),

    TextColumn::make('nombre')
        ->label('Cliente')
        ->searchable(),

    TextColumn::make('telefono')
        ->label('Teléfono'),

    TextColumn::make('correo')
        ->label('Correo'),

    TextColumn::make('direccion')
        ->label('Dirección')
        ->limit(30),

    TextColumn::make('estado')
        ->label('Estado')
        ->badge(),

    TextColumn::make('created_at')
        ->label('Registrado')
        ->dateTime()
        ->sortable(),
])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
