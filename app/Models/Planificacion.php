<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Planificacion extends Model
{
    protected $table = 'planificacions';

    protected $fillable = [
        'periodo_id',
        'ueb_id',
        'observacion',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    
    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }

    public function ueb(): BelongsTo
    {
        return $this->belongsTo(Ueb::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(PlanificacionDetalle::class);
    }
}
