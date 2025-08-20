<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    protected $fillable = ['name', 'description', 'measurement_unit_id', 'type', 'active'];

    public function measurementUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function itemType(): HasOne
    {
        if ($this->type == ItemTy'group') {
            return $this->hasOne(Group::class);
        } else {
            return $this->hasOne(Product::class);
        }
    }

    public function plannings(): HasMany
    {
        return $this->hasMany(Planning::class);
    }

    public function demands(): HasMany
    {
        return $this->hasMany(Demand::class);
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }
}
