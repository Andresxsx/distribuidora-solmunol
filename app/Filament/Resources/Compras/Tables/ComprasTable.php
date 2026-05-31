<?php

namespace App\Filament\Resources\Compras\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ComprasTable
{
    public static function configure(Table $table): Table
{
    return $table
        ->columns([
    TextColumn::make('numero_compra')
        ->label('N° Compra')
        ->searchable()
        ->sortable(),

    TextColumn::make('fecha')
        ->label('Fecha')
        ->date()
        ->sortable(),

    TextColumn::make('proveedor.nombre')
        ->label('Proveedor')
        ->searchable(),

    TextColumn::make('producto.nombre')
        ->label('Producto')
        ->searchable(),

    TextColumn::make('cantidad')
        ->label('Cantidad')
        ->sortable(),

    TextColumn::make('precio_unitario')
        ->label('Precio unitario')
        ->money('USD')
        ->sortable(),

    TextColumn::make('total')
        ->label('Total')
        ->money('USD')
        ->sortable(),

    TextColumn::make('user.name')
        ->label('Registrado por'),

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
