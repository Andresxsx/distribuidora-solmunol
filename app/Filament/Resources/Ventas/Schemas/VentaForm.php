<?php

namespace App\Filament\Resources\Ventas\Schemas;

use App\Models\Producto;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class VentaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('numero_venta')
                    ->label('Número de venta')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Se generará automáticamente')
                    ->helperText('El sistema genera el número de venta.'),

                DatePicker::make('fecha')
                    ->label('Fecha de venta')
                    ->default(now())
                    ->maxDate(now())
                    ->required()
                    ->helperText('No se permiten fechas futuras.'),

                Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship(
                        name: 'cliente',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn ($query) => $query->where('estado', 'Activo')
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Solo se muestran clientes activos.'),

                Select::make('producto_id')
                    ->label('Producto')
                    ->relationship(
                        name: 'producto',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn ($query) => $query->where('estado', 'Activo')
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->helperText(function (Get $get): string {
                        $producto = Producto::find($get('producto_id'));

                        if (! $producto) {
                            return 'Seleccione un producto activo.';
                        }

                        return 'Stock disponible actual: ' . $producto->stock_actual . ' unidad(es).';
                    }),

                Text::make(function (Get $get): string {
                    $producto = Producto::find($get('producto_id'));

                    if (! $producto) {
                        return 'Seleccione un producto para consultar el stock disponible.';
                    }

                    if ((int) $producto->stock_actual <= 0) {
                        return 'Producto sin stock disponible. No se puede vender.';
                    }

                    return 'Stock disponible para vender: ' . $producto->stock_actual . ' unidad(es).';
                })
                    ->color(function (Get $get): string {
                        $producto = Producto::find($get('producto_id'));

                        if (! $producto) {
                            return 'gray';
                        }

                        return ((int) $producto->stock_actual <= 0) ? 'danger' : 'success';
                    })
                    ->badge(),

                TextInput::make('cantidad')
                    ->label('Cantidad vendida')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(999999)
                    ->live(onBlur: true)
                    ->extraInputAttributes([
                        'min' => 1,
                        'step' => 1,
                    ])
                    ->rules([
                        function (Get $get): Closure {
                            return function (string $attribute, $value, Closure $fail) use ($get): void {
                                $productoId = $get('producto_id');

                                if (! $productoId) {
                                    $fail('Primero debe seleccionar un producto.');
                                    return;
                                }

                                $producto = Producto::find($productoId);

                                if (! $producto) {
                                    $fail('El producto seleccionado no existe.');
                                    return;
                                }

                                $cantidad = (int) $value;
                                $stockDisponible = (int) $producto->stock_actual;

                                if ($cantidad <= 0) {
                                    $fail('La cantidad vendida debe ser mayor a cero.');
                                    return;
                                }

                                if ($cantidad > $stockDisponible) {
                                    $fail(
                                        'No hay stock suficiente. Disponible: ' .
                                        $stockDisponible .
                                        ' unidad(es). Intentó vender: ' .
                                        $cantidad .
                                        '.'
                                    );
                                }
                            };
                        },
                    ])
                    ->helperText(function (Get $get): string {
                        $producto = Producto::find($get('producto_id'));

                        if (! $producto) {
                            return 'Seleccione un producto antes de ingresar la cantidad.';
                        }

                        return 'No puede vender más de ' . $producto->stock_actual . ' unidad(es).';
                    }),

                TextInput::make('precio_unitario')
                    ->label('Precio unitario de venta')
                    ->required()
                    ->numeric()
                    ->minValue(0.01)
                    ->prefix('$')
                    ->extraInputAttributes([
                        'min' => 0.01,
                        'step' => '0.01',
                    ])
                    ->helperText('Precio real de venta al cliente.'),

                TextInput::make('total')
                    ->label('Total')
                    ->disabled()
                    ->dehydrated(false)
                    ->prefix('$')
                    ->helperText('Se calcula automáticamente al guardar.'),

                Textarea::make('observacion')
                    ->label('Observación')
                    ->maxLength(200)
                    ->extraInputAttributes([
                        'maxlength' => 200,
                    ])
                    ->helperText('Opcional. Ejemplo: venta al contado, factura o detalle de entrega.'),
            ]);
    }
}