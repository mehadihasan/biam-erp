<?php

namespace App\Filament\Resources;

use App\Models\MealOrder;
use Filament\Resources\Resource;

class MealOrderResource extends Resource
{
    protected static ?string $model = MealOrder::class;

    protected static bool $shouldRegisterNavigation = false;
}
