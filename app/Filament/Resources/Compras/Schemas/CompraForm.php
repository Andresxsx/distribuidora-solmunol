<?php

namespace App\Filament\Resources\Compras\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('proveedor_id')
                    ->required()
                    ->numeric(),
                TextInput::make('producto_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('fecha')
                    ->required(),
                TextInput::make('cantidad')
                    ->required()
                    ->numeric(),
                TextInput::make('precio_unitario')
                    ->required()
                    ->numeric(),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('observacion'),
            ]);
    }
}
