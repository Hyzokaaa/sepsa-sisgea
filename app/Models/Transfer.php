<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    protected $table = 'transfers';

    protected $primaryKey = 'operation_id';

    public $incrementing = false;

    protected $fillable = ['operation_id', 'destination_business_unit_id'];

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class, 'operation_id');
    }

    public function destinationBusinessUnit(): BelongsTo
    {
        return $this->belongsTo(Ueb::class, 'destination_business_unit_id');
    }
}
