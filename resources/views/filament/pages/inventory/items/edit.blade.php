<x-filament-panels::page>
    @php
        $categories = $this->getCategories();
        $units = $this->getUnits();
    @endphp

    @include('filament.pages.inventory.items._form-styles')

    <div class="space-y-6">
        <div>
            <h2 class="text-3xl font-bold leading-tight text-gray-950 dark:text-white">Edit Item</h2>
            <p class="mt-1 text-base text-gray-500 dark:text-gray-400">Update inventory item details</p>
        </div>

        @if ($errors->any())
            <div class="max-w-4xl rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ __('Please fix the highlighted fields and try again.') }}
            </div>
        @endif

        <form method="post" action="{{ route('inventory.items.update', $item) }}" class="inventory-item-form">
            @csrf
            @method('put')

            @include('filament.pages.inventory.items._form-fields', [
                'categories' => $categories,
                'units' => $units,
                'item' => $item,
            ])

            <div class="inventory-item-form__actions">
                <button class="inventory-item-form__button">Update Item</button>
                <a href="{{ \App\Filament\Pages\Inventory\Items\AllItems::getUrl(panel: 'admin') }}" wire:navigate class="inventory-item-form__cancel">Cancel</a>
            </div>
        </form>
    </div>
</x-filament-panels::page>
