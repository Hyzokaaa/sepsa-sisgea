<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = ['name', 'acronym', 'address', 'active', 'description'];

    public function uebs(): HasMany
    {
        return $this->hasMany(Ueb::class);
    }
}
