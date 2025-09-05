<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provincia extends Model
{
    protected $table = 'provincias';

    protected $fillable = [
        'name',
        'sigla',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class);
    }

    public function uebs(): HasMany
    {
        return $this->hasMany(Ueb::class);
    }
}
