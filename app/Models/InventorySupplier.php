<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventorySupplier extends Model
{
    protected $fillable = [
        'company_name',
        'contact_person',
        'phone',
        'email',
        'category_id',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function categoryName(string $fallback = 'N/A'): string
    {
        $category = $this->relationLoaded('category')
            ? $this->getRelation('category')
            : $this->category()->first();

        if ($category instanceof InventoryCategory) {
            return $category->name;
        }

        $legacyCategory = $this->getRawOriginal('category');

        return filled($legacyCategory) ? (string) $legacyCategory : $fallback;
    }

    public function legacyCategoryName(): ?string
    {
        $legacyCategory = $this->getRawOriginal('category');

        return filled($legacyCategory) ? (string) $legacyCategory : null;
    }
}
