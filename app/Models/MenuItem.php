<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'name',
        'meal_type',
        'price_bcs',
        'price_guest',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_bcs' => 'decimal:2',
            'price_guest' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function mealOrders(): HasMany
    {
        return $this->hasMany(MealOrder::class);
    }
}
