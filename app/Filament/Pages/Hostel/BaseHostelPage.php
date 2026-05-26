<?php

namespace App\Filament\Pages\Hostel;

use App\Support\AdminModule;
use Filament\Pages\Page;

abstract class BaseHostelPage extends Page
{
    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return static::$shouldRegisterNavigation && AdminModule::isHostel();
    }
}
