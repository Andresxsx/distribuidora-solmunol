<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Compra extends Model
{
    protected $fillable = [
        'numero_compra',
        'proveedor_id',
        'producto_id',
        'fecha',
        'cantidad',
        'precio_unitario',
        'total',
        'user_id',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Compra $compra) {
            if (empty($compra->numero_compra)) {
                $compra->numero_compra = self::generarNumeroCompra();
            }

            if (!$compra->user_id && Auth::check()) {
                $compra->user_id = Auth::id();
            }
        });

        static::saving(function (Compra $compra) {
            if (!$compra->fecha) {
                throw ValidationException::withMessages([
                    'fecha' => 'La fecha de compra es obligatoria.',
                ]);
            }

            if ($compra->fecha->isFuture()) {
                throw ValidationException::withMessages([
                    'fecha' => 'La fecha de compra no puede ser futura.',
                ]);
            }

            $proveedor = Proveedor::find($compra->proveedor_id);

            if (!$proveedor || $proveedor->estado !== 'Activo') {
                throw ValidationException::withMessages([
                    'proveedor_id' => 'Seleccione un proveedor activo.',
                ]);
            }

            $producto = Producto::find($compra->producto_id);

            if (!$producto || $producto->estado !== 'Activo') {
                throw ValidationException::withMessages([
                    'producto_id' => 'Seleccione un producto activo.',
                ]);
            }

            if ((int) $compra->cantidad <= 0) {
                throw ValidationException::withMessages([
                    'cantidad' => 'La cantidad debe ser mayor a cero.',
                ]);
            }

            if ((float) $compra->precio_unitario <= 0) {
                throw ValidationException::withMessages([
                    'precio_unitario' => 'El precio unitario debe ser mayor a cero.',
                ]);
            }

            if ($compra->exists) {
                $productoAnterior = Producto::find($compra->getOriginal('producto_id'));
                $cantidadAnterior = (int) $compra->getOriginal('cantidad');

                if ($productoAnterior && ($productoAnterior->stock_actual - $cantidadAnterior) < 0) {
                    throw ValidationException::withMessages([
                        'cantidad' => 'No se puede modificar esta compra porque dejaría el stock del producto en negativo.',
                    ]);
                }
            }

            $compra->total = round((int) $compra->cantidad * (float) $compra->precio_unitario, 2);

            if ($compra->observacion) {
                $compra->observacion = trim(preg_replace('/\s+/', ' ', $compra->observacion));
            }
        });

        static::created(function (Compra $compra) {
            $producto = Producto::find($compra->producto_id);

            if ($producto) {
                $stockAnterior = (int) $producto->stock_actual;

                $producto->increment('stock_actual', $compra->cantidad);

                $producto->update([
                    'precio_compra' => $compra->precio_unitario,
                ]);

                $producto->refresh();

                MovimientoBodega::registrar([
                    'producto_id' => $producto->id,
                    'tipo_movimiento' => 'Entrada',
                    'origen' => 'Compra',
                    'documento_referencia' => $compra->numero_compra,
                    'cantidad' => $compra->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => (int) $producto->stock_actual,
                    'user_id' => $compra->user_id,
                    'observacion' => 'Entrada por compra registrada.',
                ]);
            }
        });

        static::updated(function (Compra $compra) {
            $productoAnteriorId = $compra->getOriginal('producto_id');
            $cantidadAnterior = (int) $compra->getOriginal('cantidad');

            if ((int) $productoAnteriorId === (int) $compra->producto_id) {
                $producto = Producto::find($compra->producto_id);

                if ($producto) {
                    $diferencia = (int) $compra->cantidad - $cantidadAnterior;

                    if ($diferencia > 0) {
                        $stockAnterior = (int) $producto->stock_actual;

                        $producto->increment('stock_actual', $diferencia);
                        $producto->refresh();

                        MovimientoBodega::registrar([
                            'producto_id' => $producto->id,
                            'tipo_movimiento' => 'Ajuste',
                            'origen' => 'Compra',
                            'documento_referencia' => $compra->numero_compra,
                            'cantidad' => $diferencia,
                            'stock_anterior' => $stockAnterior,
                            'stock_nuevo' => (int) $producto->stock_actual,
                            'user_id' => $compra->user_id,
                            'observacion' => 'Ajuste por aumento de cantidad en compra.',
                        ]);
                    }

                    if ($diferencia < 0) {
                        $stockAnterior = (int) $producto->stock_actual;

                        $producto->decrement('stock_actual', abs($diferencia));
                        $producto->refresh();

                        MovimientoBodega::registrar([
                            'producto_id' => $producto->id,
                            'tipo_movimiento' => 'Ajuste',
                            'origen' => 'Compra',
                            'documento_referencia' => $compra->numero_compra,
                            'cantidad' => abs($diferencia),
                            'stock_anterior' => $stockAnterior,
                            'stock_nuevo' => (int) $producto->stock_actual,
                            'user_id' => $compra->user_id,
                            'observacion' => 'Ajuste por reducción de cantidad en compra.',
                        ]);
                    }

                    $producto->update([
                        'precio_compra' => $compra->precio_unitario,
                    ]);
                }

                return;
            }

            $productoAnterior = Producto::find($productoAnteriorId);

            if ($productoAnterior) {
                $stockAnterior = (int) $productoAnterior->stock_actual;

                $productoAnterior->decrement('stock_actual', $cantidadAnterior);
                $productoAnterior->refresh();

                MovimientoBodega::registrar([
                    'producto_id' => $productoAnterior->id,
                    'tipo_movimiento' => 'Ajuste',
                    'origen' => 'Compra',
                    'documento_referencia' => $compra->numero_compra,
                    'cantidad' => $cantidadAnterior,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => (int) $productoAnterior->stock_actual,
                    'user_id' => $compra->user_id,
                    'observacion' => 'Ajuste por cambio de producto en compra.',
                ]);
            }

            $productoNuevo = Producto::find($compra->producto_id);

            if ($productoNuevo) {
                $stockAnterior = (int) $productoNuevo->stock_actual;

                $productoNuevo->increment('stock_actual', $compra->cantidad);

                $productoNuevo->update([
                    'precio_compra' => $compra->precio_unitario,
                ]);

                $productoNuevo->refresh();

                MovimientoBodega::registrar([
                    'producto_id' => $productoNuevo->id,
                    'tipo_movimiento' => 'Ajuste',
                    'origen' => 'Compra',
                    'documento_referencia' => $compra->numero_compra,
                    'cantidad' => $compra->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => (int) $productoNuevo->stock_actual,
                    'user_id' => $compra->user_id,
                    'observacion' => 'Ajuste de entrada por nuevo producto en compra.',
                ]);
            }
        });

        static::deleting(function (Compra $compra) {
            $producto = Producto::find($compra->producto_id);

            if ($producto && ($producto->stock_actual - $compra->cantidad) < 0) {
                throw ValidationException::withMessages([
                    'cantidad' => 'No se puede eliminar esta compra porque dejaría el stock del producto en negativo.',
                ]);
            }
        });

        static::deleted(function (Compra $compra) {
            $producto = Producto::find($compra->producto_id);

            if ($producto) {
                $stockAnterior = (int) $producto->stock_actual;

                $producto->decrement('stock_actual', $compra->cantidad);
                $producto->refresh();

                MovimientoBodega::registrar([
                    'producto_id' => $producto->id,
                    'tipo_movimiento' => 'Salida',
                    'origen' => 'Corrección',
                    'documento_referencia' => $compra->numero_compra,
                    'cantidad' => $compra->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => (int) $producto->stock_actual,
                    'user_id' => $compra->user_id,
                    'observacion' => 'Salida por eliminación de compra.',
                ]);
            }
        });
    }

    private static function generarNumeroCompra(): string
    {
        $siguiente = ((int) self::max('id')) + 1;

        do {
            $numero = 'COMP-' . str_pad((string) $siguiente, 6, '0', STR_PAD_LEFT);
            $siguiente++;
        } while (self::where('numero_compra', $numero)->exists());

        return $numero;
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}