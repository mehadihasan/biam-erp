<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryRequisitionItem extends Model
{
    protected $fillable = [
        'inventory_requisition_id',
        'inventory_item_id',
        'inventory_unit_id',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(InventoryRequisition::class, 'inventory_requisition_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(InventoryUnit::class, 'inventory_unit_id');
    }

    public function stockOuts(): HasMany
    {
        return $this->hasMany(InventoryStockOut::class);
    }
}
