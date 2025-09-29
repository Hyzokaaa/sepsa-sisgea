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
}
