<?php

namespace App\Filament\Pages\Hostel\MealOrders;

use App\Filament\Pages\Hostel\BaseHostelPage;
use App\Models\MenuItem;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;

class MenuManagement extends BaseHostelPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-list-bullet';

    protected static string | \UnitEnum | null $navigationGroup = 'Meal Order';

    protected static ?string $title = 'Menu Management';

    protected static ?string $navigationLabel = 'Menu Management';

    protected static ?string $slug = 'hostel/meal-orders/menu';

    protected static ?int $navigationSort = 43;

    protected string $view = 'filament.pages.hostel.meal-orders.menu-management';

    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $mealType = 'breakfast';

    public ?string $priceBcs = null;

    public ?string $priceGuest = null;

    public function getMenuItemsProperty(): Collection
    {
        return MenuItem::query()
            ->orderByRaw("CASE meal_type WHEN 'breakfast' THEN 1 WHEN 'lunch' THEN 2 WHEN 'supper' THEN 3 ELSE 4 END")
            ->orderBy('name')
            ->get();
    }

    public function addItem(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editItem(int $id): void
    {
        $item = MenuItem::query()->findOrFail($id);
        $this->editingId = $item->id;
        $this->name = $item->name;
        $this->mealType = $item->meal_type;
        $this->priceBcs = (string) $item->price_bcs;
        $this->priceGuest = (string) $item->price_guest;
        $this->showModal = true;
    }

    public function saveItem(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'mealType' => ['required', Rule::in(['breakfast', 'lunch', 'supper'])],
            'priceBcs' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'priceGuest' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        MenuItem::query()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $validated['name'],
                'meal_type' => $validated['mealType'],
                'price_bcs' => $validated['priceBcs'],
                'price_guest' => $validated['priceGuest'],
            ],
        );

        $this->showModal = false;
        $this->resetForm();

        Notification::make()
            ->title('Menu item saved')
            ->success()
            ->send();
    }

    public function toggleActive(int $id): void
    {
        $item = MenuItem::query()->findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);
    }

    public function deleteItem(int $id): void
    {
        MenuItem::query()->findOrFail($id)->delete();

        Notification::make()
            ->title('Menu item deleted')
            ->success()
            ->send();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->name = '';
        $this->mealType = 'breakfast';
        $this->priceBcs = null;
        $this->priceGuest = null;
    }
}
