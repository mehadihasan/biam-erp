<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStockOut extends Model
{
    protected $fillable = [
        'inventory_requisition_id',
        'inventory_requisition_item_id',
        'inventory_item_id',
        'quantity',
        'issued_to_department',
        'purpose_reason',
        'reference_document',
        'transaction_date',
        'stock_before',
        'stock_after',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'transaction_date' => 'date',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(InventoryRequisition::class, 'inventory_requisition_id');
    }

    public function requisitionItem(): BelongsTo
    {
        return $this->belongsTo(InventoryRequisitionItem::class, 'inventory_requisition_item_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
