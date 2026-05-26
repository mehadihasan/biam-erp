<div class="bhms-module-selector-content">
    <div class="bhms-module-selector-content__intro">
        <h1 class="bhms-module-selector-content__title">Select a Module</h1>
        <p class="bhms-module-selector-content__subtitle">Choose where you want to continue.</p>
    </div>

    <div class="bhms-module-selector-grid">
        <a
            href="{{ \App\Filament\Pages\HostelDashboard::getUrl(panel: 'admin') }}"
            wire:navigate
            class="bhms-module-card group"
        >
            <div class="bhms-module-card__icon bhms-module-card__icon--hostel">
                <x-filament::icon icon="heroicon-o-building-office-2" class="h-6 w-6" />
            </div>
            <h2 class="bhms-module-card__title">Hostel Management</h2>
            <p class="bhms-module-card__description">Booking, rooms, approvals, and meals</p>
        </a>

        <a
            href="{{ \App\Filament\Pages\InventoryDashboard::getUrl(panel: 'admin') }}"
            wire:navigate
            class="bhms-module-card group"
        >
            <div class="bhms-module-card__icon bhms-module-card__icon--inventory">
                <x-filament::icon icon="heroicon-o-cube" class="h-6 w-6" />
            </div>
            <h2 class="bhms-module-card__title">Inventory Management</h2>
            <p class="bhms-module-card__description">Stock, suppliers, alerts, and reports</p>
        </a>
    </div>
</div>
