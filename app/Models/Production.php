<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Production extends Model
{
    protected $table = 'productions';

    protected $primaryKey = 'operation_id';

    public $incrementing = false;

    protected $fillable = ['operation_id'];

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class, 'operation_id');
    }
}
