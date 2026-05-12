<?php

namespace App\Filament\Resources;

use App\Models\MenuItem;
use Filament\Resources\Resource;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static bool $shouldRegisterNavigation = false;
}
