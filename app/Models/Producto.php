<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'grupo_productos_id',
        'unidad_medidas_id',
        'name',
        'codigo',
        'imagen',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function grupoProducto(): BelongsTo
    {
        return $this->belongsTo(GrupoProducto::class, 'grupo_productos_id');
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medidas_id');
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }

    public function operacionDetalles(): HasMany
    {
        return $this->hasMany(OperacionDetalle::class);
    }

    public function ingredientes(): HasMany
    {
        return $this->hasMany(ProductoIngrediente::class, 'producto_id');

    }

//    public function ingredientes(): HasMany
//    {
//        return $this->hasMany(ProductoIngrediente::class, 'ingrediente_id');
//
//    }

    public function ingredientess()
    {
        return $this->belongsToMany(Producto::class, 'producto_ingredientes', 'producto_id','ingrediente_id')
            ->withPivot(['cantidad']) // Cantidad requerida del ingrediente
            ->withTimestamps();
    }


    public function planificacionDetalles()
    {
        return $this->morphMany(PlanificacionDetalle::class, 'item');
    }

    public function demandaDetalles()
    {
        return $this->morphMany(DemandaDetalle::class, 'item');
    }
}
