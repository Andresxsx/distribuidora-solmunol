<?php

namespace App\Filament\Resources\Productos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
    TextColumn::make('codigo')
        ->label('Código')
        ->searchable()
        ->sortable(),

    TextColumn::make('nombre')
        ->label('Producto')
        ->searchable()
        ->sortable(),

    TextColumn::make('categoria')
        ->label('Categoría')
        ->searchable(),

    TextColumn::make('stock_actual')
        ->label('Stock actual')
        ->badge()
        ->sortable(),

    TextColumn::make('stock_minimo')
        ->label('Stock mínimo')
        ->sortable(),

    TextColumn::make('precio_compra')
        ->label('Precio compra')
        ->money('USD')
        ->sortable(),

    TextColumn::make('precio_venta')
        ->label('Precio venta')
        ->money('USD')
        ->sortable(),

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
