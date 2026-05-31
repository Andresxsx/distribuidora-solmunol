<?php

namespace App\Filament\Resources\Proveedores\Schemas;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProveedorForm
{
   public static function configure(Schema $schema): Schema
{
    return $schema
        ->components([
            TextInput::make('ruc')
                ->label('RUC')
                ->required()
                ->minLength(13)
                ->maxLength(13)
                ->inputMode('numeric')
                ->rules([
                    'required',
                    'regex:/^[0-9]{13}$/',
                ])
                ->unique(table: 'proveedores', column: 'ruc', ignoreRecord: true)
                ->helperText('Debe tener exactamente 13 dígitos. Ejemplo: cédula válida + 001 para persona natural.'),

            TextInput::make('nombre')
                ->label('Nombre / Razón social')
                ->required()
                ->minLength(3)
                ->maxLength(150)
                ->rules([
                    'required',
                    'regex:/^[\pL\pN\s.,&\/()\-]+$/u',
                ])
                ->helperText('Máximo 150 caracteres. No use símbolos raros.'),

            TextInput::make('telefono')
                ->label('Teléfono')
                ->required()
                ->minLength(9)
                ->maxLength(10)
                ->inputMode('numeric')
                ->rules([
                    'required',
                    'regex:/^(09[0-9]{8}|0[2-7][0-9]{7})$/',
                ])
                ->helperText('Celular: 09XXXXXXXX. Convencional: 02XXXXXXX.'),

            TextInput::make('correo')
                ->label('Correo electrónico')
                ->required()
                ->email()
                ->maxLength(100)
                ->helperText('Ejemplo: proveedor@empresa.com'),

            TextInput::make('direccion')
                ->label('Dirección')
                ->required()
                ->minLength(5)
                ->maxLength(180),

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

    private static function validarRucEcuador(string $ruc): bool
    {
        $ruc = trim($ruc);

        if (! preg_match('/^[0-9]{13}$/', $ruc)) {
            return false;
        }

        if (preg_match('/^(\d)\1{12}$/', $ruc)) {
            return false;
        }

        $provincia = intval(substr($ruc, 0, 2));
        $tercerDigito = intval($ruc[2]);

        if (! self::provinciaValida($provincia)) {
            return false;
        }

        if ($tercerDigito >= 0 && $tercerDigito <= 5) {
            $cedula = substr($ruc, 0, 10);
            $establecimiento = substr($ruc, 10, 3);

            return self::validarCedulaEcuador($cedula) && intval($establecimiento) > 0;
        }

        if ($tercerDigito === 6) {
            return self::validarRucPublico($ruc);
        }

        if ($tercerDigito === 9) {
            return self::validarRucPrivado($ruc);
        }

        return false;
    }

    private static function provinciaValida(int $provincia): bool
    {
        return ($provincia >= 1 && $provincia <= 24) || $provincia === 30;
    }

    private static function validarCedulaEcuador(string $cedula): bool
    {
        if (! preg_match('/^[0-9]{10}$/', $cedula)) {
            return false;
        }

        if (preg_match('/^(\d)\1{9}$/', $cedula)) {
            return false;
        }

        $provincia = intval(substr($cedula, 0, 2));
        $tercerDigito = intval($cedula[2]);

        if (! self::provinciaValida($provincia)) {
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

        $digitoCalculado = (10 - ($suma % 10)) % 10;
        $digitoReal = intval($cedula[9]);

        return $digitoCalculado === $digitoReal;
    }

    private static function validarRucPrivado(string $ruc): bool
    {
        $establecimiento = substr($ruc, 10, 3);

        if (intval($establecimiento) <= 0) {
            return false;
        }

        $coeficientes = [4, 3, 2, 7, 6, 5, 4, 3, 2];
        $suma = 0;

        for ($i = 0; $i < 9; $i++) {
            $suma += intval($ruc[$i]) * $coeficientes[$i];
        }

        $residuo = $suma % 11;
        $digitoCalculado = 11 - $residuo;

        if ($digitoCalculado === 11) {
            $digitoCalculado = 0;
        }

        if ($digitoCalculado === 10) {
            return false;
        }

        $digitoReal = intval($ruc[9]);

        return $digitoCalculado === $digitoReal;
    }

    private static function validarRucPublico(string $ruc): bool
    {
        $establecimiento = substr($ruc, 9, 4);

        if (intval($establecimiento) <= 0) {
            return false;
        }

        $coeficientes = [3, 2, 7, 6, 5, 4, 3, 2];
        $suma = 0;

        for ($i = 0; $i < 8; $i++) {
            $suma += intval($ruc[$i]) * $coeficientes[$i];
        }

        $residuo = $suma % 11;
        $digitoCalculado = 11 - $residuo;

        if ($digitoCalculado === 11) {
            $digitoCalculado = 0;
        }

        if ($digitoCalculado === 10) {
            return false;
        }

        $digitoReal = intval($ruc[8]);

        return $digitoCalculado === $digitoReal;
    }
}