<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ueb extends Model
{
    protected $fillable = ['name', 'company_id', 'province_id', 'active'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function plannings(): HasMany
    {
        return $this->hasMany(Planning::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }
}
