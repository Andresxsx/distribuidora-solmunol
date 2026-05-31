<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('cedula_ruc')
                    ->required(),
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('telefono')
                    ->tel(),
                TextInput::make('correo'),
                TextInput::make('direccion'),
                TextInput::make('estado')
                    ->required()
                    ->default('Activo'),
            ]);
    }
}
