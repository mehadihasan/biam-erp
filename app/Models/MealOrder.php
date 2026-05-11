<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealOrder extends Model
{
    protected $fillable = [
        'cadre_reference',
        'reference',
        'order_date',
        'meal_type',
        'menu_item',
        'quantity',
        'unit_price',
        'total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'quantity' => 'integer',
            'unit_price' => 'integer',
            'total' => 'integer',
        ];
    }
}
