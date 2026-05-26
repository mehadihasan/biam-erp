<?php

namespace App\Filament\Pages\Inventory;

use App\Support\AdminModule;
use Filament\Pages\Page;

abstract class BaseInventoryPage extends Page
{
    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return static::$shouldRegisterNavigation && AdminModule::isInventory();
    }
}
