<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Empleado extends Model
{
    protected $fillable = [
        'codigo_empleado',
        'cedula',
        'nombres',
        'apellidos',
        'cargo',
        'departamento',
        'telefono',
        'correo',
        'sueldo',
        'fecha_ingreso',
        'estado',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'sueldo' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Empleado $empleado) {
            if (empty($empleado->codigo_empleado)) {
                $empleado->codigo_empleado = self::generarCodigoEmpleado();
            }
        });

        static::saving(function (Empleado $empleado) {
            $empleado->cedula = preg_replace('/\D/', '', (string) $empleado->cedula);
            $empleado->telefono = preg_replace('/\D/', '', (string) $empleado->telefono);
            $empleado->nombres = trim(preg_replace('/\s+/', ' ', (string) $empleado->nombres));
            $empleado->apellidos = trim(preg_replace('/\s+/', ' ', (string) $empleado->apellidos));
            $empleado->correo = trim(strtolower((string) $empleado->correo));

            if (! self::validarCedulaEcuador($empleado->cedula)) {
                throw ValidationException::withMessages([
                    'cedula' => 'La cédula ingresada no es válida para Ecuador.',
                ]);
            }

            if (! preg_match('/^[\pL\s]+$/u', $empleado->nombres) || mb_strlen($empleado->nombres) < 2 || mb_strlen($empleado->nombres) > 80) {
                throw ValidationException::withMessages([
                    'nombres' => 'Los nombres deben contener solo letras y tener entre 2 y 80 caracteres.',
                ]);
            }

            if (! preg_match('/^[\pL\s]+$/u', $empleado->apellidos) || mb_strlen($empleado->apellidos) < 2 || mb_strlen($empleado->apellidos) > 80) {
                throw ValidationException::withMessages([
                    'apellidos' => 'Los apellidos deben contener solo letras y tener entre 2 y 80 caracteres.',
                ]);
            }

            if (! self::cargoValido($empleado->cargo)) {
                throw ValidationException::withMessages([
                    'cargo' => 'Seleccione un cargo válido.',
                ]);
            }

            if (! self::departamentoValido($empleado->departamento)) {
                throw ValidationException::withMessages([
                    'departamento' => 'Seleccione un departamento válido.',
                ]);
            }

            if (! preg_match('/^09[0-9]{8}$/', $empleado->telefono)) {
                throw ValidationException::withMessages([
                    'telefono' => 'El teléfono debe ser un celular ecuatoriano válido. Ejemplo: 09XXXXXXXX.',
                ]);
            }

            if (! filter_var($empleado->correo, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages([
                    'correo' => 'Ingrese un correo electrónico válido.',
                ]);
            }

            if ((float) $empleado->sueldo <= 0) {
                throw ValidationException::withMessages([
                    'sueldo' => 'El sueldo debe ser mayor a cero.',
                ]);
            }

            if ($empleado->fecha_ingreso && $empleado->fecha_ingreso->isFuture()) {
                throw ValidationException::withMessages([
                    'fecha_ingreso' => 'La fecha de ingreso no puede ser futura.',
                ]);
            }

            if (! in_array($empleado->estado, ['Activo', 'Inactivo', 'Suspendido', 'Retirado'])) {
                throw ValidationException::withMessages([
                    'estado' => 'Seleccione un estado válido.',
                ]);
            }
        });
    }

    private static function generarCodigoEmpleado(): string
    {
        $siguiente = ((int) self::max('id')) + 1;

        do {
            $codigo = 'EMP-' . str_pad((string) $siguiente, 6, '0', STR_PAD_LEFT);
            $siguiente++;
        } while (self::where('codigo_empleado', $codigo)->exists());

        return $codigo;
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

        $digitoCalculado = (10 - ($suma % 10)) % 10;
        $digitoReal = intval($cedula[9]);

        return $digitoCalculado === $digitoReal;
    }

    private static function cargoValido(string $cargo): bool
    {
        return in_array($cargo, [
            'Gerente',
            'Administrador',
            'Vendedor',
            'Bodeguero',
            'Comprador',
            'Contador',
            'Asistente administrativo',
            'Jefe de talento humano',
            'Analista de sistemas',
        ]);
    }

    private static function departamentoValido(string $departamento): bool
    {
        return in_array($departamento, [
            'Dirección',
            'Administración',
            'Talento Humano',
            'Bodega',
            'Compras',
            'Ventas',
            'Contabilidad',
            'Sistemas',
        ]);
    }
}