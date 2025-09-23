<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Operacion extends Model
{
    protected $table = 'operacions';

    protected $fillable = [
        'fecha',
        'tipo_operacion',
        'almacen_id',
        'origen_destino_tipo',
        'origen_destino_id',
        'observacion',
        'cerrado',
        'importe',
        'user_id'

    ];

    protected $casts = [
        'fecha' => 'date',
        'cerrado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'user_id' => 'integer',
    ];

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(OperacionDetalle::class);
    }

    public function origenDestino(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'origen_destino_tipo', 'origen_destino_id');
    }
}
