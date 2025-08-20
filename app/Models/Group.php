<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = ['item_id', 'parent_group_id'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'parent_group_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Group::class, 'parent_group_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'group_id');
    }
}
