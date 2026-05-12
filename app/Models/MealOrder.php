<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealOrder extends Model
{
    protected $fillable = [
        'ref',
        'guest_id',
        'cadre_reference',
        'reference',
        'order_date',
        'meal_type',
        'menu_item_id',
        'menu_item',
        'quantity',
        'unit_price',
        'total_price',
        'total',
        'coupon_code',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getDisplayRefAttribute(): string
    {
        return $this->ref ?: $this->reference;
    }

    public function getDisplayTotalAttribute(): float
    {
        return (float) ($this->total_price ?: $this->total);
    }
}
