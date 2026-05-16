<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'ref',
        'user_id',
        'room_id',
        'room_type',
        'number_of_rooms',
        'check_in_date',
        'check_out_date',
        'notes',
        'duration_nights',
        'rent_multiplier',
        'base_rate',
        'calculated_rent',
        'booking_money',
        'total_rent',
        'status',
        'checked_in_at',
        'checked_out_at',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'number_of_rooms' => 'integer',
            'duration_nights' => 'integer',
            'rent_multiplier' => 'integer',
            'base_rate' => 'decimal:2',
            'calculated_rent' => 'decimal:2',
            'booking_money' => 'decimal:2',
            'total_rent' => 'decimal:2',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function roomAvailability(): HasOne
    {
        return $this->hasOne(RoomAvailability::class);
    }
}
