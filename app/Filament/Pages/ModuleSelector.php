<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ModuleSelector extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Select a Module';

    protected ?string $heading = 'Select a Module';

    protected string $view = 'filament.pages.module-selector';
}
