<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Demand extends Model
{
    protected $fillable = ['client_id', 'period_id', 'item_id', 'quantity'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function ueb(): BelongsTo
    {
        return $this->belongsTo(Ueb::class);
    }
}

