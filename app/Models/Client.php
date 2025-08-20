<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = ['name', 'description', 'address', 'acronym', 'active'];

    public function demands(): HasMany
    {
        return $this->hasMany(Demand::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
