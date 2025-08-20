<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeasurementUnit extends Model
{
    protected $fillable = ['name', 'abbreviation'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
