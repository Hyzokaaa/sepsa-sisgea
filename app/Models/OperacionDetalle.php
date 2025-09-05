<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperacionDetalle extends Model
{
    protected $table = 'operacion_detalles';

    protected $fillable = [
        'operacion_id',
        'producto_id',
        'cantidad',
        'precio_costo',
        'precio_venta',
        'observacion',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_costo' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function operacion(): BelongsTo
    {
        return $this->belongsTo(Operacion::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
