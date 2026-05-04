<?php

namespace App\Filament\Pages\Hostel;

use Filament\Pages\Page;

abstract class BaseHostelPage extends Page
{
    protected static string | \UnitEnum | null $navigationGroup = 'Hostel Management';

    protected static ?int $navigationSort = 10;
}
