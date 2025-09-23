<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventario extends Model
{
    protected $table = 'inventarios';

    protected $fillable = [
        'almacen_id',
        'producto_id',
        'cantidad',
        'precio_costo',
        'precio_venta',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_costo' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }



        public static function verificarProductos($almacenId, $productoId, $cantidadR)
    {

        $inventario = self::with('producto')
            ->where('almacen_id', $almacenId)
            ->where('producto_id', $productoId)
            ->first();

        if (!$inventario) {
            return [
                'suficiente' => false,
                'existencia' => 0,
                'msg' => 'El producto no existe en el almacen seleccionado',
            ];
        }

        $nombreProducto = $inventario->producto ? $inventario->producto->name : 'Producto';

        if ($inventario->cantidad < $cantidadR) {
            return [
                'suficiente' => false,
                'existencia' => $inventario->cantidad,
                'msg' => "Solo hay {$inventario->cantidad} unidades del producto '{$nombreProducto}' en existencia"
            ];
        }

        return [
            'suficiente' => true,
            'existencia' => $inventario->cantidad,
            'inventario' => $inventario
        ];
    }

    public static function restarProductos($almacenId, $productoId, $cantidad)
    {
        $verificacion = self::verificarProductos($almacenId, $productoId, $cantidad);

        if (!$verificacion['suficiente']) {
            throw new \Exception($verificacion['msg']);
        }

        $verificacion['inventario']->decrement('cantidad', $cantidad);

        return $verificacion['inventario'];
    }

    public static function aggProductos($almacenId, $productoId, $cantidad, $precioCosto = null, $precioVenta = null)
    {
        $inventario = self::firstOrCreate(
            [
                'almacen_id' => $almacenId,
                'producto_id' => $productoId
            ],
            [
                'cantidad' => 0,
                'precio_costo' => $precioCosto ?? 0,
                'precio_venta' => $precioVenta ?? 0
            ]
        );

        $inventario->increment('cantidad', $cantidad);


        if ($precioCosto !== null) {
            $inventario->update(['precio_costo' => $precioCosto]);
        }
        if ($precioVenta !== null) {
            $inventario->update(['precio_venta' => $precioVenta]);
        }

        return $inventario;
    }

    public function procesarTraslado($data)
    {
        foreach ($data['products'] as $producto) {

            Inventario::restarProductos(
                $data['almacen_origen_id'],
                $producto['producto_id'],
                $producto['cantidad']
            );

            Inventario::sumarProductos(
                $data['almacen_destino_id'],
                $producto['producto_id'],
                $producto['cantidad']
            );
        }
    }
}
