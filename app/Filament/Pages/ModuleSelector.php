<?php

namespace App\Filament\Pages;

use App\Support\AdminModule;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ModuleSelector extends Page
{
    protected static string $layout = 'filament.layouts.module-selector';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'module-selector';

    protected static ?string $title = 'Select a Module';

    protected ?string $heading = '';

    protected string $view = 'filament.pages.module-selector';

    public function mount(): void
    {
        AdminModule::set(null);
    }

    public function getHeading(): string | Htmlable | null
    {
        return null;
    }
}
