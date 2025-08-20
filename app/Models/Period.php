<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Period extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date'];

    public function plannings(): HasMany
    {
        return $this->hasMany(Planning::class);
    }

    public function demands(): HasMany
    {
        return $this->hasMany(Demand::class);
    }
}
