<?php

namespace App\Filament\Resources\MovimientoBodegas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MovimientoBodegasTable
{
    public static function configure(Table $table): Table
    {
        return $table
             ->columns([
            TextColumn::make('codigo_movimiento')
                ->label('Código')
                ->searchable()
                ->sortable(),

            TextColumn::make('fecha')
                ->label('Fecha')
                ->dateTime()
                ->sortable(),

            TextColumn::make('producto.nombre')
                ->label('Producto')
                ->searchable(),

            TextColumn::make('tipo_movimiento')
                ->label('Tipo')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Entrada' => 'success',
                    'Salida' => 'danger',
                    'Ajuste' => 'warning',
                    default => 'gray',
                }),

            TextColumn::make('origen')
                ->label('Origen')
                ->badge(),

            TextColumn::make('documento_referencia')
                ->label('Documento')
                ->searchable(),

            TextColumn::make('cantidad')
                ->label('Cantidad')
                ->sortable(),

            TextColumn::make('stock_anterior')
                ->label('Stock anterior')
                ->sortable(),

            TextColumn::make('stock_nuevo')
                ->label('Stock nuevo')
                ->sortable(),

            TextColumn::make('user.name')
                ->label('Usuario'),

            TextColumn::make('observacion')
                ->label('Observación')
                ->limit(40),
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
