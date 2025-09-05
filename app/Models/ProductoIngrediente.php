<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoIngrediente extends Model
{
    protected $table = 'producto_ingredientes';

    protected $fillable = [
        'producto_id',
        'ingrediente_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function ingrediente(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'ingrediente_id');
    }
}
