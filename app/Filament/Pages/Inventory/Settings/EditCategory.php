<?php

namespace App\Filament\Pages\Inventory\Settings;

use App\Filament\Pages\Inventory\BaseInventoryPage;
use App\Models\InventoryCategory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditCategory extends BaseInventoryPage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Edit Category';

    protected static ?string $slug = 'inventory/categories/edit';

    protected string $view = 'filament.pages.inventory.settings.category-edit';

    public InventoryCategory $category;

    public static function getNavigationUrl(): string
    {
        return Categories::getUrl(panel: 'admin');
    }

    public function mount(): void
    {
        $categoryId = (int) request()->query('id', 0);

        if ($categoryId <= 0) {
            throw new NotFoundHttpException('Category not found.');
        }

        $this->category = InventoryCategory::query()->findOrFail($categoryId);
    }

    public static function urlForCategory(int $categoryId, string $panel = 'admin'): string
    {
        return static::getUrl(panel: $panel) . '?id=' . $categoryId;
    }
}
