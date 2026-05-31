<?php

namespace App\Filament\Resources\Empleados\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmpleadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
    TextColumn::make('codigo_empleado')
        ->label('Código')
        ->searchable()
        ->sortable(),

    TextColumn::make('cedula')
        ->label('Cédula')
        ->searchable(),

    TextColumn::make('nombres')
        ->label('Nombres')
        ->searchable(),

    TextColumn::make('apellidos')
        ->label('Apellidos')
        ->searchable(),

    TextColumn::make('cargo')
        ->label('Cargo')
        ->searchable(),

    TextColumn::make('departamento')
        ->label('Departamento')
        ->searchable(),

    TextColumn::make('telefono')
        ->label('Celular'),

    TextColumn::make('correo')
        ->label('Correo'),

    TextColumn::make('sueldo')
        ->label('Sueldo')
        ->money('USD')
        ->sortable(),

    TextColumn::make('fecha_ingreso')
        ->label('Fecha ingreso')
        ->date()
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
