<x-filament-panels::page>
    @php
        $suppliers = $this->getSuppliers();
    @endphp

    <style>
        .suppliers-page {
            color: #001b33;
        }

        .suppliers-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .suppliers-title {
            color: #001b33;
            font-size: 28px;
            font-weight: 800;
            line-height: 1.15;
        }

        .suppliers-subtitle {
            margin-top: 4px;
            color: #64748b;
            font-size: 16px;
        }

        .suppliers-add {
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

        .suppliers-filters {
            display: grid;
            grid-template-columns: minmax(260px, 1fr) minmax(150px, 220px);
            gap: 14px;
            align-items: center;
        }

        .suppliers-search-wrap {
            position: relative;
            min-width: 0;
        }

        .suppliers-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            width: 18px;
            height: 18px;
            color: #64748b;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .suppliers-search,
        .suppliers-select {
            width: 100%;
            height: 44px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            color: #001b33;
            font-size: 16px;
            outline: none;
        }

        .suppliers-search {
            padding: 0 14px 0 42px;
        }

        .suppliers-select {
            padding: 0 14px;
        }

        .suppliers-table-wrap {
            position: relative;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
        }

        .suppliers-table-wrap--loading .suppliers-table {
            opacity: 0.55;
        }

        .suppliers-loading {
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

        .suppliers-spinner {
            width: 14px;
            height: 14px;
            border: 2px solid #cbd5e1;
            border-top-color: #007062;
            border-radius: 999px;
            animation: suppliers-spin 700ms linear infinite;
        }

        @keyframes suppliers-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .suppliers-table-scroll {
            overflow-x: auto;
        }

        .suppliers-table {
            min-width: 980px;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            color: #001b33;
            font-size: 16px;
        }

        .suppliers-table thead {
            background: #f1f5f9;
        }

        .suppliers-table th,
        .suppliers-table td {
            border-bottom: 1px solid #cbd5e1;
            padding: 16px 20px;
            text-align: left;
            vertical-align: middle;
            white-space: nowrap;
        }

        .suppliers-table th {
            color: #64748b;
            font-weight: 700;
        }

        .suppliers-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .suppliers-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .suppliers-status--active {
            background: #d1fae5;
            color: #007a58;
        }

        .suppliers-status--inactive {
            background: #e2e8f0;
            color: #475569;
        }

        .suppliers-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .suppliers-action,
        .suppliers-delete {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 7px 10px;
            color: #334155;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .suppliers-delete {
            color: #dc2626;
        }

        @media (max-width: 767px) {
            .suppliers-header {
                flex-direction: column;
            }

            .suppliers-add {
                width: 100%;
            }

            .suppliers-filters {
                grid-template-columns: 1fr;
            }
        }

        .dark .suppliers-page,
        .dark .suppliers-title,
        .dark .suppliers-table {
            color: #ffffff;
        }

        .dark .suppliers-subtitle {
            color: #94a3b8;
        }

        .dark .suppliers-search,
        .dark .suppliers-select,
        .dark .suppliers-table-wrap,
        .dark .suppliers-loading,
        .dark .suppliers-action,
        .dark .suppliers-delete {
            border-color: #334155;
            background: #111827;
            color: #ffffff;
        }

        .dark .suppliers-delete {
            color: #f87171;
        }

        .dark .suppliers-table thead {
            background: #1f2937;
        }

        .dark .suppliers-table th,
        .dark .suppliers-table td {
            border-bottom-color: #334155;
        }
    </style>

    <div class="suppliers-page space-y-5">
        <div class="suppliers-header">
            <div>
                <h2 class="suppliers-title">Suppliers</h2>
                <p class="suppliers-subtitle">Manage supplier information</p>
            </div>

            <a
                href="{{ \App\Filament\Pages\Inventory\Settings\CreateSupplier::getUrl(panel: 'admin') }}"
                wire:navigate
                class="suppliers-add"
            >
                <x-filament::icon icon="heroicon-o-plus" class="h-5 w-5" />
                <span>Add Supplier</span>
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="suppliers-filters">
            <label class="suppliers-search-wrap">
                <x-filament::icon icon="heroicon-o-magnifying-glass" class="suppliers-search-icon" />
                <input
                    wire:model.live.debounce.400ms="search"
                    type="search"
                    placeholder="Search suppliers..."
                    class="suppliers-search"
                >
            </label>

            <select wire:model.live="status" class="suppliers-select">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>

        <div
            class="suppliers-table-wrap"
            wire:loading.class="suppliers-table-wrap--loading"
            wire:target="search,status,gotoPage,nextPage,previousPage"
        >
            <div
                class="suppliers-loading"
                wire:loading.delay.flex
                wire:target="search,status,gotoPage,nextPage,previousPage"
            >
                <span class="suppliers-spinner"></span>
                <span>Loading</span>
            </div>

            <div class="suppliers-table-scroll">
                <table class="suppliers-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td>{{ $suppliers->firstItem() + $loop->index }}</td>
                                <td class="font-semibold">{{ $supplier->company_name }}</td>
                                <td>{{ $supplier->contact_person ?: '-' }}</td>
                                <td>{{ $supplier->phone ?: '-' }}</td>
                                <td>{{ $supplier->email ?: '-' }}</td>
                                <td>{{ $supplier->category?->name ?: '-' }}</td>
                                <td>
                                    <span class="suppliers-status {{ $supplier->is_active ? 'suppliers-status--active' : 'suppliers-status--inactive' }}">
                                        {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="suppliers-actions">
                                        <a
                                            href="{{ route('inventory.suppliers.edit', $supplier) }}"
                                            wire:navigate
                                            class="suppliers-action"
                                        >
                                            Edit
                                        </a>
                                        <form method="post" action="{{ route('inventory.suppliers.destroy', $supplier) }}" onsubmit="return confirm('Delete this supplier?')">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="suppliers-delete">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-gray-500">
                                    No suppliers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $suppliers->links() }}
        </div>
    </div>
</x-filament-panels::page>
