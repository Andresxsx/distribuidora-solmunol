<?php

namespace App\Filament\Resources\MovimientoBodegas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MovimientoBodegaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('codigo_movimiento')
                    ->placeholder('-'),
                TextEntry::make('producto_id')
                    ->numeric(),
                TextEntry::make('tipo_movimiento'),
                TextEntry::make('origen'),
                TextEntry::make('documento_referencia')
                    ->placeholder('-'),
                TextEntry::make('cantidad')
                    ->numeric(),
                TextEntry::make('stock_anterior')
                    ->numeric(),
                TextEntry::make('stock_nuevo')
                    ->numeric(),
                TextEntry::make('user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('fecha')
                    ->dateTime(),
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
