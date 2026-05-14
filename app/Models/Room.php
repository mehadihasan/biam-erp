<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'floor',
        'room_type',
        'capacity',
        'base_rate',
        'status',
        'description',
        'images',
    ];

    protected function casts(): array
    {
        return [
            'floor' => 'integer',
            'capacity' => 'integer',
            'base_rate' => 'decimal:2',
            'images' => 'array',
        ];
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(RoomAvailability::class);
    }

    /**
     * Normalized list of storage-relative paths (e.g. rooms/photo.jpg) from the JSON column.
     *
     * @return list<string>
     */
    public function normalizedImagePaths(): array
    {
        $images = $this->images;
        if (! is_array($images)) {
            return [];
        }

        $paths = [];
        foreach ($images as $item) {
            if (is_string($item) && $item !== '') {
                $paths[] = $item;

                continue;
            }

            if (is_array($item)) {
                foreach (['path', 'file', 'name', 'key'] as $key) {
                    if (! empty($item[$key]) && is_string($item[$key])) {
                        $paths[] = $item[$key];

                        break;
                    }
                }
            }
        }

        return array_values(array_filter(
            $paths,
            static fn (string $p): bool => $p !== '',
        ));
    }

    /** Alias for callers that assumed "imagePaths"; prefer normalized paths. */
    public function imagePaths(): array
    {
        return $this->normalizedImagePaths();
    }

    public function publicUrlForStoredPath(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        $path = str_replace('\\', '/', trim($path));
        if ($path === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    /**
     * All image URLs for slideshow/detail (order preserved; listing card uses {@see cardThumbnailUrl}).
     *
     * @return list<string>
     */
    public function imageUrls(): array
    {
        $urls = [];
        foreach ($this->normalizedImagePaths() as $path) {
            $url = $this->publicUrlForStoredPath($path);
            if ($url !== null) {
                $urls[] = $url;
            }
        }

        return $urls;
    }

    /** URL of the last stored image for grid cards (most recently appended in Filament reorder). */
    public function listingThumbnailUrl(): string
    {
        $paths = $this->normalizedImagePaths();
        if ($paths === []) {
            return static::fallbackRoomImageUrl();
        }

        $lastPath = end($paths);
        $url = $this->publicUrlForStoredPath(is_string($lastPath) ? $lastPath : null);

        return $url ?? static::fallbackRoomImageUrl();
    }

    /** @deprecated use listingThumbnailUrl() */
    public function primaryImageUrl(): ?string
    {
        $paths = $this->normalizedImagePaths();
        if ($paths === []) {
            return null;
        }

        $lastPath = end($paths);

        return $this->publicUrlForStoredPath(is_string($lastPath) ? $lastPath : null);
    }

    public static function fallbackRoomImageUrl(): string
    {
        if (file_exists(public_path('img/room-placeholder.png'))) {
            return asset('img/room-placeholder.png');
        }

        return asset('img/room-placeholder.svg');
    }
}
