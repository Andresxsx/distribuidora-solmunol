<?php

namespace App\Filament\Resources\Productos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codigo')
                    ->required(),
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('categoria')
                    ->required(),
                Textarea::make('descripcion')
                    ->columnSpanFull(),
                TextInput::make('stock_actual')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('stock_minimo')
                    ->required()
                    ->numeric()
                    ->default(5),
                TextInput::make('precio_compra')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('precio_venta')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('estado')
                    ->required()
                    ->default('Activo'),
            ]);
    }
}
