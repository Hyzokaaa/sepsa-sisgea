<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periodo extends Model
{
    protected $table = 'periodos';

    protected $fillable = [
        'ejercicio',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function planificaciones(): HasMany
    {
        return $this->hasMany(Planificacion::class);
    }

    public function demandas(): HasMany
    {
        return $this->hasMany(Demanda::class);
    }
}
