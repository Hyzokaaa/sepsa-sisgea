<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DemandaDetalle extends Model
{
    protected $table = 'demanda_detalles';

    protected $fillable = [
        'demanda_id',
        'item_id',
        'item_type',
        'demanda',
        'observacion',
    ];

    protected $casts = [
        'demanda' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function demanda(): BelongsTo
    {
        return $this->belongsTo(Demanda::class);
    }

    public function item(): MorphTo
    {
        return $this->morphTo();
    }
}
