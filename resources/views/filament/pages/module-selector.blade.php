<x-filament-panels::page>
    <div class="space-y-8">
        <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/biam-logo.png') }}" alt="BIAM" class="h-10 w-10 object-contain">
                <p class="hidden text-sm font-medium text-gray-700 sm:block dark:text-gray-200">
                    Bangladesh Institute of Administration and Management
                </p>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ filament()->auth()->user()?->name }}
            </div>
        </div>

        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-950 dark:text-white">Select a Module</h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Choose where you want to continue.
            </p>
        </div>

        <div class="mx-auto grid max-w-4xl grid-cols-1 gap-6 sm:grid-cols-2">
            <a href="{{ \App\Filament\Pages\HostelDashboard::getUrl(panel: 'admin') }}"
               class="group rounded-2xl border border-gray-200 bg-white p-8 transition hover:-translate-y-0.5 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-amber-500 text-white">
                    <x-filament::icon icon="heroicon-o-building-office-2" class="h-6 w-6" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Hostel Management</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Booking, rooms, approvals, and meals</p>
            </a>

            <a href="{{ \App\Filament\Pages\InventoryDashboard::getUrl(panel: 'admin') }}"
               class="group rounded-2xl border border-gray-200 bg-white p-8 transition hover:-translate-y-0.5 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-teal-700 text-white">
                    <x-filament::icon icon="heroicon-o-cube" class="h-6 w-6" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Inventory Management</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Stock, suppliers, alerts, and reports</p>
            </a>
        </div>
    </div>
</x-filament-panels::page>
