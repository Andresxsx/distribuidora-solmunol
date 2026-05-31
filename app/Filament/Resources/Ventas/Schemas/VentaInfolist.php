<?php

namespace App\Filament\Resources\Ventas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VentaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('cliente_id')
                    ->numeric(),
                TextEntry::make('producto_id')
                    ->numeric(),
                TextEntry::make('fecha')
                    ->date(),
                TextEntry::make('cantidad')
                    ->numeric(),
                TextEntry::make('precio_unitario')
                    ->numeric(),
                TextEntry::make('total')
                    ->numeric(),
                TextEntry::make('user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('observacion')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
