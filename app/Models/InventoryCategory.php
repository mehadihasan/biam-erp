<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryCategory extends Model
{
    protected $fillable = [
        'name',
        'details',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(InventorySupplier::class, 'category_id');
    }
}
