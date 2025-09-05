<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'provincia_id',
        'name',
        'siglas',
        'direccion',
        'activo',
        'descripcion',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }

    public function uebs(): HasMany
    {
        return $this->hasMany(Ueb::class);
    }

    public static function rules($empresaId = null)
    {
        return [
            'provincia_id' => 'nullable|exists:provincias,id',
            'name' => 'required|string|max:255|unique:empresas,name,' . $empresaId,
            'siglas' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'activo' => 'boolean',
            'descripcion' => 'nullable|string',
        ];
    }
}
