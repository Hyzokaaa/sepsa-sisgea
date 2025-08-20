<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = ['ueb_id', 'product_id', 'quantity', 'unit_cost'];

    public function ueb(): BelongsTo
    {
        return $this->belongsTo(Ueb::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
