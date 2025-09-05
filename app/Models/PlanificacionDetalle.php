<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PlanificacionDetalle extends Model
{
    protected $table = 'planificacion_detalles';

    protected $fillable = [
        'planificacion_id',
        'item_id',
        'item_type',
        'plan',
        'pronostico',
        'observacion',
    ];

    protected $casts = [
        'plan' => 'decimal:2',
        'pronostico' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function planificacion(): BelongsTo
    {
        return $this->belongsTo(Planificacion::class);
    }

    public function item(): MorphTo
    {
        return $this->morphTo();
    }
}
