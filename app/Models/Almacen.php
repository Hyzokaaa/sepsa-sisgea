<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Almacen extends Model
{
    protected $table = 'almacens';

    protected $fillable = [
        'ueb_id',
        'nombre',
        'observacion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ueb(): BelongsTo
    {
        return $this->belongsTo(Ueb::class);
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }

    public function operaciones(): HasMany
    {
        return $this->hasMany(Operacion::class);
    }
}
