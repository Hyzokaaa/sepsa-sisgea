<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrupoProducto extends Model
{
    protected $table = 'grupo_productos';

    protected $fillable = [
        'unidad_medidas_id',
        'padre_id',
        'name',
        'codigo',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medidas_id');
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(GrupoProducto::class, 'padre_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(GrupoProducto::class, 'padre_id');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'grupo_productos_id');
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
