<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'categoria',
        'descripcion',
        'stock_actual',
        'stock_minimo',
        'precio_compra',
        'precio_venta',
        'estado',
    ];

    protected static function booted(): void
    {
        static::creating(function (Producto $producto) {
            if (empty($producto->codigo)) {
                $ultimoId = (int) self::max('id');
                $producto->codigo = 'PROD-' . str_pad((string) ($ultimoId + 1), 6, '0', STR_PAD_LEFT);
            }

            if ($producto->stock_actual === null) {
                $producto->stock_actual = 0;
            }
        });

        static::saving(function (Producto $producto) {
            $producto->nombre = trim(preg_replace('/\s+/', ' ', (string) $producto->nombre));
            $producto->categoria = trim((string) $producto->categoria);
            $producto->descripcion = trim((string) $producto->descripcion);

            if (mb_strlen($producto->nombre) < 2 || mb_strlen($producto->nombre) > 100) {
                throw ValidationException::withMessages([
                    'nombre' => 'El nombre del producto debe tener entre 2 y 100 caracteres.',
                ]);
            }

            if (! preg_match('/^[\pL\pN\s.,&\/()\-]+$/u', $producto->nombre)) {
                throw ValidationException::withMessages([
                    'nombre' => 'El nombre solo puede contener letras, números, espacios y signos básicos.',
                ]);
            }

            if (mb_strlen($producto->categoria) < 2 || mb_strlen($producto->categoria) > 80) {
                throw ValidationException::withMessages([
                    'categoria' => 'Seleccione una categoría válida.',
                ]);
            }

            if ($producto->stock_minimo < 0) {
                throw ValidationException::withMessages([
                    'stock_minimo' => 'El stock mínimo no puede ser negativo.',
                ]);
            }

            if ($producto->precio_compra < 0) {
                throw ValidationException::withMessages([
                    'precio_compra' => 'El precio de compra no puede ser negativo.',
                ]);
            }

            if ($producto->precio_venta < 0) {
                throw ValidationException::withMessages([
                    'precio_venta' => 'El precio de venta no puede ser negativo.',
                ]);
            }

            if ($producto->precio_venta < $producto->precio_compra) {
                throw ValidationException::withMessages([
                    'precio_venta' => 'El precio de venta no debe ser menor al precio de compra.',
                ]);
            }

            if (! in_array($producto->estado, ['Activo', 'Inactivo'])) {
                throw ValidationException::withMessages([
                    'estado' => 'El estado solo puede ser Activo o Inactivo.',
                ]);
            }
        });
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
    public function movimientosBodega()
{
    return $this->hasMany(MovimientoBodega::class);
}
}