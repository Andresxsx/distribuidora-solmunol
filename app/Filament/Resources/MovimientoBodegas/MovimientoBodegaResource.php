<?php

namespace App\Filament\Resources\MovimientoBodegas;

use App\Filament\Resources\MovimientoBodegas\Pages\CreateMovimientoBodega;
use App\Filament\Resources\MovimientoBodegas\Pages\EditMovimientoBodega;
use App\Filament\Resources\MovimientoBodegas\Pages\ListMovimientoBodegas;
use App\Filament\Resources\MovimientoBodegas\Pages\ViewMovimientoBodega;
use App\Filament\Resources\MovimientoBodegas\Schemas\MovimientoBodegaForm;
use App\Filament\Resources\MovimientoBodegas\Schemas\MovimientoBodegaInfolist;
use App\Filament\Resources\MovimientoBodegas\Tables\MovimientoBodegasTable;
use App\Models\MovimientoBodega;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MovimientoBodegaResource extends Resource
{
    protected static ?string $model = MovimientoBodega::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'codigo_movimiento';

    public static function form(Schema $schema): Schema
    {
        return MovimientoBodegaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MovimientoBodegaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MovimientoBodegasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMovimientoBodegas::route('/'),
            'create' => CreateMovimientoBodega::route('/create'),
            'view' => ViewMovimientoBodega::route('/{record}'),
            'edit' => EditMovimientoBodega::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
{
    return false;
}

public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
{
    return false;
}

public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
{
    return false;
}

public static function canDeleteAny(): bool
{
    return false;
}
}
