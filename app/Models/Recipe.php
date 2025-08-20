<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = ['product_id', 'name', 'description'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'item_id');
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    public function manufactures(): HasMany
    {
        return $this->hasMany(Manufacture::class);
    }
}
