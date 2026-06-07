<x-filament-panels::page>
    @php
        $supplier = null;
        $categories = $this->getCategories();
    @endphp

    @include('filament.pages.inventory.settings._supplier-form-styles')

    <div class="space-y-6">
        <div>
            <h2 class="text-3xl font-bold leading-tight text-gray-950 dark:text-white">Add New Supplier</h2>
            <p class="mt-1 text-base text-gray-500 dark:text-gray-400">Manage supplier information</p>
        </div>

        @if ($errors->any())
            <div class="max-w-4xl rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ __('Please fix the highlighted fields and try again.') }}
            </div>
        @endif

        <form method="post" action="{{ route('inventory.suppliers.store') }}" class="supplier-form">
            @csrf

            @include('filament.pages.inventory.settings._supplier-form-fields', ['supplier' => $supplier])

            <div class="supplier-form__actions">
                <button class="supplier-form__button">Add Supplier</button>
                <a href="{{ \App\Filament\Pages\Inventory\Settings\Suppliers::getUrl(panel: 'admin') }}" wire:navigate class="supplier-form__cancel">Cancel</a>
            </div>
        </form>
    </div>
</x-filament-panels::page>
