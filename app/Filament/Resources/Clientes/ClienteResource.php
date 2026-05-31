<?php

namespace App\Filament\Resources\Clientes;

use App\Filament\Resources\Clientes\Pages\CreateCliente;
use App\Filament\Resources\Clientes\Pages\EditCliente;
use App\Filament\Resources\Clientes\Pages\ListClientes;
use App\Filament\Resources\Clientes\Pages\ViewCliente;
use App\Filament\Resources\Clientes\Schemas\ClienteForm;
use App\Filament\Resources\Clientes\Schemas\ClienteInfolist;
use App\Filament\Resources\Clientes\Tables\ClientesTable;
use App\Models\Cliente;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nombre';

   public static function form(Schema $schema): Schema
{
    return $schema
        ->components([
            TextInput::make('cedula_ruc')
                ->label('Cédula / RUC')
                ->required()
                ->maxLength(13)
                ->minLength(10)
                ->inputMode('numeric')
                ->rules([
                    'regex:/^[0-9]+$/',
                    function () {
                        return function (string $attribute, $value, \Closure $fail) {
                            $documento = trim((string) $value);

                            if (! static::validarCedulaRucEcuador($documento)) {
                                $fail('La cédula o RUC ingresado no es válido para Ecuador.');
                            }
                        };
                    },
                ])
                ->unique(ignoreRecord: true)
                ->helperText('Ingrese una cédula válida de 10 dígitos o RUC natural de 13 dígitos.'),

            TextInput::make('nombre')
                ->label('Nombre del cliente')
                ->required()
                ->maxLength(120)
                ->rules([
                    'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u',
                ])
                ->helperText('Solo letras y espacios. Máximo 120 caracteres.'),

            TextInput::make('telefono')
                ->label('Teléfono')
                ->required()
                ->maxLength(10)
                ->minLength(10)
                ->inputMode('numeric')
                ->rules([
                    'regex:/^[0-9]{10}$/',
                ])
                ->helperText('Debe tener exactamente 10 dígitos.'),

            TextInput::make('correo')
                ->label('Correo electrónico')
                ->required()
                ->email()
                ->maxLength(100)
                ->helperText('Ejemplo: cliente@correo.com'),

            TextInput::make('direccion')
                ->label('Dirección')
                ->maxLength(150),

            Select::make('estado')
                ->label('Estado')
                ->options([
                    'Activo' => 'Activo',
                    'Inactivo' => 'Inactivo',
                ])
                ->default('Activo')
                ->required(),
        ]);
}

    public static function infolist(Schema $schema): Schema
    {
        return ClienteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientesTable::configure($table);
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
            'index' => ListClientes::route('/'),
            'create' => CreateCliente::route('/create'),
            'view' => ViewCliente::route('/{record}'),
            'edit' => EditCliente::route('/{record}/edit'),
        ];
    }
    private static function validarCedulaRucEcuador(string $documento): bool
{
    $documento = trim($documento);

    if (! preg_match('/^[0-9]+$/', $documento)) {
        return false;
    }

    if (strlen($documento) === 10) {
        return self::validarCedulaEcuador($documento);
    }

    if (strlen($documento) === 13) {
        $cedula = substr($documento, 0, 10);
        $establecimiento = substr($documento, 10, 3);

        return self::validarCedulaEcuador($cedula) && $establecimiento === '001';
    }

    return false;
}

private static function validarCedulaEcuador(string $cedula): bool
{
    if (! preg_match('/^[0-9]{10}$/', $cedula)) {
        return false;
    }

    $provincia = intval(substr($cedula, 0, 2));
    $tercerDigito = intval($cedula[2]);

    if ($provincia < 1 || $provincia > 24) {
        return false;
    }

    if ($tercerDigito > 5) {
        return false;
    }

    $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
    $suma = 0;

    for ($i = 0; $i < 9; $i++) {
        $valor = intval($cedula[$i]) * $coeficientes[$i];

        if ($valor >= 10) {
            $valor -= 9;
        }

        $suma += $valor;
    }

    $digitoVerificadorCalculado = (10 - ($suma % 10)) % 10;
    $digitoVerificadorReal = intval($cedula[9]);

    return $digitoVerificadorCalculado === $digitoVerificadorReal;
}
}
