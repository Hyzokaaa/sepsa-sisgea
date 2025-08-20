<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Planning extends Model
{
    protected $fillable = ['business_unit_id', 'period_id', 'item_id', 'plan', 'forecast', 'status'];

    public function ueb(): BelongsTo
    {
        return $this->belongsTo(Ueb::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
