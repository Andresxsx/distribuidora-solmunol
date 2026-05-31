<?php

namespace App\Filament\Resources\Empleados\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EmpleadoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('cedula'),
                TextEntry::make('nombres'),
                TextEntry::make('apellidos'),
                TextEntry::make('cargo'),
                TextEntry::make('departamento'),
                TextEntry::make('telefono')
                    ->placeholder('-'),
                TextEntry::make('correo')
                    ->placeholder('-'),
                TextEntry::make('sueldo')
                    ->numeric(),
                TextEntry::make('fecha_ingreso')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('estado'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
