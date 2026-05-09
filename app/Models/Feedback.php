<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'cadre_reference',
        'options',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }
}
