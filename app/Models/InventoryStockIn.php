<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryStockIn extends Model
{
    protected $fillable = [
        'stock_in_date',
        'expiry_warranty_date',
        'reference_number',
        'inventory_supplier_id',
        'purpose_notes',
        'attachment_path',
        'grand_total',
        'created_by',
    ];

    protected $casts = [
        'stock_in_date' => 'date',
        'expiry_warranty_date' => 'date',
        'grand_total' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(InventorySupplier::class, 'inventory_supplier_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryStockInItem::class);
    }
}
