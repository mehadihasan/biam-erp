<x-filament-panels::page>
    @php
        $items = $this->getItems();
        $categories = $this->getCategories();
        $columns = [
            'code' => 'Code',
            'name' => 'Name',
            'category' => 'Category',
            'unit' => 'Unit',
            'qty' => 'Qty',
            'min' => 'Min',
            'cost' => 'Cost (৳)',
            'location' => 'Location',
            'status' => 'Status',
        ];
    @endphp

    <style>
        .inventory-items-page {
            color: #001b33;
        }

        .inventory-items-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .inventory-items-title {
            margin: 0;
            color: #001b33;
            font-size: 28px;
            font-weight: 800;
            line-height: 1.15;
        }

        .inventory-items-subtitle {
            margin-top: 4px;
            color: #64748b;
            font-size: 16px;
        }

        .inventory-items-add {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 41px;
            border-radius: 8px;
            background: #007062;
            padding: 0 16px;
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
        }

        .inventory-items-filters {
            display: grid;
            grid-template-columns: minmax(260px, 1fr) minmax(170px, 0.22fr) minmax(130px, 0.16fr) minmax(170px, 0.2fr) auto;
            gap: 14px;
            align-items: center;
        }

        .inventory-items-search,
        .inventory-items-select,
        .inventory-items-columns-button {
            width: 100%;
            height: 44px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            color: #001b33;
            font-size: 16px;
            outline: none;
        }

        .inventory-items-search-wrap {
            position: relative;
            min-width: 0;
        }

        .inventory-items-search {
            padding: 0 14px 0 42px;
        }

        .inventory-items-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            width: 18px;
            height: 18px;
            color: #64748b;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .inventory-items-select {
            padding: 0 14px;
        }

        .inventory-items-columns {
            position: relative;
        }

        .inventory-items-columns-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 14px;
            white-space: nowrap;
        }

        .inventory-items-columns-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            z-index: 30;
            width: 180px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 8px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.14);
        }

        .inventory-items-column-option {
            display: flex;
            align-items: center;
            gap: 8px;
            border-radius: 6px;
            padding: 7px 8px;
            color: #001b33;
            font-size: 14px;
        }

        .inventory-items-table-wrap {
            position: relative;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
        }

        .inventory-items-table-wrap--loading .inventory-items-table {
            opacity: 0.55;
        }

        .inventory-items-loading {
            position: absolute;
            right: 16px;
            top: 14px;
            z-index: 20;
            display: none;
            align-items: center;
            gap: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.96);
            padding: 6px 12px;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.1);
        }

        .inventory-items-spinner {
            width: 14px;
            height: 14px;
            border: 2px solid #cbd5e1;
            border-top-color: #007062;
            border-radius: 999px;
            animation: inventory-items-spin 700ms linear infinite;
        }

        @keyframes inventory-items-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .inventory-items-table-scroll {
            overflow-x: auto;
        }

        .inventory-items-table {
            min-width: 1120px;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            color: #001b33;
            font-size: 16px;
        }

        .inventory-items-table thead {
            background: #f1f5f9;
        }

        .inventory-items-table th,
        .inventory-items-table td {
            border-bottom: 1px solid #cbd5e1;
            padding: 16px 24px;
            text-align: left;
            vertical-align: middle;
            white-space: nowrap;
        }

        .inventory-items-table th {
            color: #64748b;
            font-weight: 700;
        }

        .inventory-items-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .inventory-items-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 13px;
        }

        .inventory-items-qty--ok {
            color: #007062;
        }

        .inventory-items-qty--low {
            color: #d97706;
        }

        .inventory-items-qty--out {
            color: #dc2626;
            font-weight: 700;
        }

        .inventory-items-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .inventory-items-status--active {
            background: #d1fae5;
            color: #007a58;
        }

        .inventory-items-status--inactive {
            background: #e2e8f0;
            color: #475569;
        }

        .inventory-items-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .inventory-items-icon-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border: 0;
            background: transparent;
            color: #334155;
            padding: 0;
            cursor: pointer;
        }

        .inventory-items-icon-button--stock {
            color: #009b72;
        }

        .inventory-items-icon-button--delete {
            color: #ff1f3d;
        }

        .inventory-items-icon-button svg {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 1279px) {
            .inventory-items-filters {
                grid-template-columns: minmax(260px, 1fr) repeat(2, minmax(160px, 0.5fr));
            }
        }

        @media (max-width: 767px) {
            .inventory-items-header {
                flex-direction: column;
            }

            .inventory-items-add {
                width: 100%;
            }

            .inventory-items-filters {
                grid-template-columns: 1fr;
            }

            .inventory-items-columns-menu {
                left: 0;
                right: auto;
                width: 100%;
            }
        }

        .dark .inventory-items-page,
        .dark .inventory-items-title,
        .dark .inventory-items-table,
        .dark .inventory-items-column-option {
            color: #ffffff;
        }

        .dark .inventory-items-subtitle {
            color: #94a3b8;
        }

        .dark .inventory-items-search,
        .dark .inventory-items-select,
        .dark .inventory-items-columns-button,
        .dark .inventory-items-columns-menu,
        .dark .inventory-items-table-wrap,
        .dark .inventory-items-loading {
            border-color: #334155;
            background: #111827;
            color: #ffffff;
        }

        .dark .inventory-items-table thead {
            background: #1f2937;
        }

        .dark .inventory-items-table th,
        .dark .inventory-items-table td {
            border-bottom-color: #334155;
        }
    </style>

    <div
        class="inventory-items-page space-y-5"
        x-data="{
            columns: {
                code: true,
                name: true,
                category: true,
                unit: true,
                qty: true,
                min: true,
                cost: true,
                location: true,
                status: true,
            },
            openColumns: false,
        }"
    >
        <div class="inventory-items-header">
            <div>
                <h2 class="inventory-items-title">Item Inventory</h2>
                <p class="inventory-items-subtitle">Manage all inventory items</p>
            </div>

            <a
                href="{{ \App\Filament\Pages\Inventory\Items\CreateItem::getUrl(panel: 'admin') }}"
                wire:navigate
                class="inventory-items-add"
            >
                <x-filament::icon icon="heroicon-o-plus" class="h-5 w-5" />
                <span>Add New Item</span>
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="inventory-items-filters">
            <label class="inventory-items-search-wrap">
                <x-filament::icon icon="heroicon-o-magnifying-glass" class="inventory-items-search-icon" />
                <input
                    wire:model.live.debounce.400ms="search"
                    type="search"
                    placeholder="Search by name or code..."
                    class="inventory-items-search"
                >
            </label>

            <select wire:model.live="category" class="inventory-items-select">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="status" class="inventory-items-select">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>

            <select wire:model.live="stockLevel" class="inventory-items-select">
                <option value="">All Stock Levels</option>
                <option value="low">Low Stock</option>
                <option value="out">Out of Stock</option>
            </select>

            <div class="inventory-items-columns" x-on:click.outside="openColumns = false">
                <button type="button" class="inventory-items-columns-button" x-on:click="openColumns = ! openColumns">
                    <x-filament::icon icon="heroicon-o-view-columns" class="h-5 w-5" />
                    <span>Columns</span>
                </button>

                <div class="inventory-items-columns-menu" x-show="openColumns" x-cloak>
                    @foreach ($columns as $key => $label)
                        <label class="inventory-items-column-option">
                            <input type="checkbox" x-model="columns.{{ $key }}">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div
            class="inventory-items-table-wrap"
            wire:loading.class="inventory-items-table-wrap--loading"
            wire:target="search,category,status,stockLevel,gotoPage,nextPage,previousPage"
        >
            <div
                class="inventory-items-loading"
                wire:loading.delay.flex
                wire:target="search,category,status,stockLevel,gotoPage,nextPage,previousPage"
            >
                <span class="inventory-items-spinner"></span>
                <span>Loading</span>
            </div>

            <div class="inventory-items-table-scroll">
                <table class="inventory-items-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th x-show="columns.code">Code</th>
                            <th x-show="columns.name">Name</th>
                            <th x-show="columns.category">Category</th>
                            <th x-show="columns.unit">Unit</th>
                            <th x-show="columns.qty">Qty</th>
                            <th x-show="columns.min">Min</th>
                            <th x-show="columns.cost">Cost (৳)</th>
                            <th x-show="columns.location">Location</th>
                            <th x-show="columns.status">Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            @php
                                $qtyClass = match (true) {
                                    $item->current_stock_quantity <= 0 => 'inventory-items-qty--out',
                                    $item->current_stock_quantity <= $item->min_threshold => 'inventory-items-qty--low',
                                    default => 'inventory-items-qty--ok',
                                };
                            @endphp
                            <tr>
                                <td>{{ $items->firstItem() + $loop->index }}</td>
                                <td x-show="columns.code" class="inventory-items-code">{{ $item->item_code }}</td>
                                <td x-show="columns.name" class="font-semibold">{{ $item->name }}</td>
                                <td x-show="columns.category">{{ $item->category?->name ?: '-' }}</td>
                                <td x-show="columns.unit">{{ $item->unit?->name ?: '-' }}</td>
                                <td x-show="columns.qty" class="{{ $qtyClass }}">{{ $item->current_stock_quantity }}</td>
                                <td x-show="columns.min">{{ $item->min_threshold }}</td>
                                <td x-show="columns.cost">{{ rtrim(rtrim(number_format((float) $item->unit_cost, 2, '.', ''), '0'), '.') }}</td>
                                <td x-show="columns.location">{{ $item->location ?: '-' }}</td>
                                <td x-show="columns.status">
                                    <span class="inventory-items-status {{ $item->is_active ? 'inventory-items-status--active' : 'inventory-items-status--inactive' }}">
                                        {{ $item->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="inventory-items-actions">
                                        <a
                                            href="{{ route('inventory.items.edit', $item) }}"
                                            wire:navigate
                                            class="inventory-items-icon-button"
                                            title="Edit"
                                            aria-label="Edit {{ $item->name }}"
                                        >
                                            <x-filament::icon icon="heroicon-o-pencil-square" />
                                        </a>
                                        <a
                                            href="{{ route('inventory.items.show', $item) }}"
                                            wire:navigate
                                            class="inventory-items-icon-button inventory-items-icon-button--stock"
                                            title="Stock"
                                            aria-label="Stock {{ $item->name }}"
                                        >
                                            <x-filament::icon icon="heroicon-o-cube-transparent" />
                                        </a>
                                        <form method="post" action="{{ route('inventory.items.destroy', $item) }}" onsubmit="return confirm('Delete this item?')">
                                            @csrf
                                            @method('delete')
                                            <button
                                                type="submit"
                                                class="inventory-items-icon-button inventory-items-icon-button--delete"
                                                title="Delete"
                                                aria-label="Delete {{ $item->name }}"
                                            >
                                                <x-filament::icon icon="heroicon-o-trash" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-10 text-center text-gray-500">
                                    No items found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $items->links() }}
        </div>
    </div>
</x-filament-panels::page>
