<?php

namespace App\Services;

use App\Models\Almacen;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventarioService
{
    /**
     * Actualiza o crea el inventario para un producto en un área determinada.
     *
     * @throws \Exception
     */
    public function updateOrCreateInventory(int $almacenId, int $productId, float $quantity, float $nuevoCosto, float $nuevoVenta): Inventario
    {

        return DB::transaction(function () use ($almacenId, $productId, $quantity, $nuevoCosto, $nuevoVenta) {

            $inventory = Inventario::lockForUpdate()->firstOrNew([
                'almacen_id' => $almacenId,
                'producto_id' => $productId,
            ]);

            $cantidadActual = $inventory->exists ? $inventory->cantidad : 0;
            $nuevaCantidad = $cantidadActual + $quantity;

            // Validar que la cantidad no sea negativa
            if ($nuevaCantidad < 0) {
                Log::error("No existe la cantidad seleccionada del $productId en el Almacen $almacenId");
                throw new \Exception('No se puede tener cantidad negativa en inventario');
            }

            // Manejar casos donde la cantidad total es cero
            $cantidadParaCalculo = max($nuevaCantidad, 0.001);

            // Cálculo de nuevo costo ponderado solo si hay cantidad
            if ($cantidadActual > 0 || $quantity > 0) {
                $inventory->precio_costo = round(
                    (($cantidadActual * $inventory->precio_costo) + ($quantity * $nuevoCosto)) / $cantidadParaCalculo,
                    2
                );
            } else {
                $inventory->precio_costo = $nuevoCosto;
            }

            // Actualizar valores
            $inventory->cantidad = $nuevaCantidad;
            $inventory->precio_venta = $nuevoVenta;
            $inventory->save();

            return $inventory;
        });
    }

    public function revertInventoryUpdate($almacenId, $productoId, $cantidad)
    {
        DB::transaction(function () use ($almacenId, $productoId, $cantidad) {
            $inventario = Inventario::where('almacen_id', $almacenId)
                ->where('producto_id', $productoId)
                ->lockForUpdate()
                ->first();



            if ($inventario) {
                $nuevaCantidad = $inventario->cantidad - $cantidad;
                // Validar que no quede cantidad negativa
                if ($nuevaCantidad < 0) {
                    Log::error("Intento de revertir a cantidad negativa para producto $productoId en el  área $almacenId");
                    throw new \Exception('No se puede tener cantidad negativa en inventario');
                }

                $inventario->cantidad = $nuevaCantidad;
                $inventario->save();

            }
        });
    }





}
