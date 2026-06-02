<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'inventory_category_id',
        'inventory_unit_id',
        'unit_cost',
        'location',
        'min_threshold',
        'initial_stock_quantity',
        'current_stock_quantity',
        'description',
        'is_active',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'min_threshold' => 'integer',
        'initial_stock_quantity' => 'integer',
        'current_stock_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getItemCodeAttribute(): string
    {
        return 'ITM-' . str_pad(strtoupper(dechex($this->id)), 6, '0', STR_PAD_LEFT);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(InventoryUnit::class, 'inventory_unit_id');
    }
}
