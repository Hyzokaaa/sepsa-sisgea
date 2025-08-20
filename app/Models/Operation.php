<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Operation extends Model
{
    protected $fillable = ['business_unit_id', 'type', 'date', 'notes'];

    public function ueb(): BelongsTo
    {
        return $this->belongsTo(Ueb::class);
    }

    public function OperationType(): HasOne
    {
        return $this->hasOne(Transfer::class, 'operation_id');
    }

    public function operationItems(): HasMany
    {
        return $this->hasMany(OperationItem::class);
    }
}
