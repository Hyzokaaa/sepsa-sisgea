<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadMedida extends Model
{

     //La unidad de medida va a ser asociada al producto y a grupo de producto??

    protected $table = 'unidad_medidas';

    protected $fillable = [
        'name',
        'siglas',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function grupoProductos(): HasMany
    {
        return $this->hasMany(GrupoProducto::class, 'unidad_medidas_id');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'unidad_medidas_id');
    }
}
