<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestPendingApproval extends Model
{
    protected $fillable = [
        'ref',
        'user_id',
        'room_id',
        'booking_id',
        'reviewed_by',
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
        'payment_amount',
        'payment_method',
        'status',
        'approval_level',
        'approval_notes',
        'rejection_reason',
        'reviewed_at',
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
            'payment_amount' => 'decimal:2',
            'reviewed_at' => 'datetime',
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

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
