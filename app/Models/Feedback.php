<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feedback extends Model
{
    protected $table = 'feedback';

    public const CATEGORIES = [
        'Front Desk Service',
        'Canteen Food Quality',
        'Canteen Staff Behavior',
        'Room Boys Service',
        'Cleanliness of the Room',
        'Overall cleanliness of Surroundings',
        'Washroom, AC, Lights and Fans',
    ];

    public const RATINGS = [
        'Excellent',
        'Good',
        'Fair',
        'Average',
    ];

    protected $fillable = [
        'guest_id',
        'cadre_reference',
        'submitter_type',
        'options',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(FeedbackRating::class);
    }

    public function ratingMap(): array
    {
        return $this->ratings
            ->mapWithKeys(fn (FeedbackRating $rating): array => [$rating->category => $rating->rating])
            ->all();
    }

    public function getSubmitterNameAttribute(): string
    {
        return $this->guest?->name ?: ($this->cadre_reference ?: 'Guest');
    }
}
