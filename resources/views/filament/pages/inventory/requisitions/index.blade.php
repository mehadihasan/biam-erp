<x-filament-panels::page>
    @php
        $requisitions = $this->getRequisitions();
        $columns = [
            'ref' => 'Ref',
            'date' => 'Date',
            'requester' => 'Requester',
            'department' => 'Department',
            'items' => 'Items',
            'total_qty' => 'Total Qty',
            'status' => 'Status',
        ];
    @endphp

    <style>
        .req-page { color: #001b33; }
        .req-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; }
        .req-title { color: #001b33; font-size: 28px; font-weight: 800; line-height: 1.15; }
        .req-subtitle { margin-top: 4px; color: #64748b; font-size: 16px; }
        .req-add { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 41px; border-radius: 8px; background: #007062; padding: 0 16px; color: #fff; font-size: 16px; font-weight: 600; text-decoration: none; white-space: nowrap; }
        .req-filters { display: grid; grid-template-columns: minmax(260px, 1fr) minmax(150px, 190px) auto; gap: 14px; align-items: center; }
        .req-search-wrap, .req-columns { position: relative; min-width: 0; }
        .req-search-icon { position: absolute; left: 14px; top: 50%; width: 18px; height: 18px; color: #64748b; transform: translateY(-50%); pointer-events: none; }
        .req-search, .req-select, .req-columns-button { width: 100%; height: 44px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; color: #001b33; font-size: 16px; outline: none; }
        .req-search { padding: 0 14px 0 42px; }
        .req-select { padding: 0 14px; }
        .req-columns-button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 14px; white-space: nowrap; }
        .req-columns-menu { position: absolute; right: 0; top: calc(100% + 8px); z-index: 30; width: 180px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; padding: 8px; box-shadow: 0 12px 28px rgba(15, 23, 42, .14); }
        .req-column-option { display: flex; align-items: center; gap: 8px; border-radius: 6px; padding: 7px 8px; color: #001b33; font-size: 14px; }
        .req-table-wrap { position: relative; overflow: hidden; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; }
        .req-table-wrap--loading .req-table { opacity: .55; }
        .req-loading { position: absolute; right: 16px; top: 14px; z-index: 20; display: none; align-items: center; gap: 8px; border: 1px solid #cbd5e1; border-radius: 999px; background: rgba(255,255,255,.96); padding: 6px 12px; color: #475569; font-size: 13px; font-weight: 600; box-shadow: 0 8px 18px rgba(15,23,42,.1); }
        .req-spinner { width: 14px; height: 14px; border: 2px solid #cbd5e1; border-top-color: #007062; border-radius: 999px; animation: req-spin 700ms linear infinite; }
        @keyframes req-spin { to { transform: rotate(360deg); } }
        .req-table-scroll { overflow-x: auto; }
        .req-table { min-width: 1050px; width: 100%; border-collapse: separate; border-spacing: 0; color: #001b33; font-size: 16px; }
        .req-table thead { background: #f1f5f9; }
        .req-table th, .req-table td { border-bottom: 1px solid #cbd5e1; padding: 16px 18px; text-align: left; vertical-align: middle; white-space: nowrap; }
        .req-table th { color: #64748b; font-weight: 700; }
        .req-table tbody tr:last-child td { border-bottom: 0; }
        .req-ref { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-size: 13px; }
        .req-status { display: inline-flex; align-items: center; border-radius: 999px; padding: 4px 12px; font-size: 14px; font-weight: 500; }
        .req-status--pending { background: #ffedd5; color: #c2410c; }
        .req-status--approved { background: #d1fae5; color: #007a58; }
        .req-status--rejected { background: #fee2e2; color: #dc2626; }
        .req-actions { display: flex; align-items: center; gap: 14px; }
        .req-icon-button { display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; border: 0; background: transparent; color: #334155; padding: 0; cursor: pointer; }
        .req-icon-button--edit { color: #173c63; }
        .req-icon-button--approve { color: #009b72; }
        .req-icon-button--reject { color: #ff1f3d; }
        .req-icon-button svg { width: 18px; height: 18px; }
        @media (max-width: 767px) { .req-header { flex-direction: column; } .req-add { width: 100%; } .req-filters { grid-template-columns: 1fr; } .req-columns-menu { left: 0; right: auto; width: 100%; } }
        .dark .req-page, .dark .req-title, .dark .req-table, .dark .req-column-option { color: #fff; }
        .dark .req-subtitle { color: #94a3b8; }
        .dark .req-search, .dark .req-select, .dark .req-columns-button, .dark .req-columns-menu, .dark .req-table-wrap, .dark .req-loading { border-color: #334155; background: #111827; color: #fff; }
        .dark .req-table thead { background: #1f2937; }
        .dark .req-table th, .dark .req-table td { border-bottom-color: #334155; }
    </style>

    <div
        class="req-page space-y-5"
        x-data="{
            columns: { ref: true, date: true, requester: true, department: true, items: true, total_qty: true, status: true },
            openColumns: false,
        }"
    >
        <div class="req-header">
            <div>
                <h2 class="req-title">Requisitions</h2>
                <p class="req-subtitle">All purchase requisitions</p>
            </div>
            <a href="{{ \App\Filament\Pages\Inventory\Requisitions\CreateRequisition::getUrl(panel: 'admin') }}" wire:navigate class="req-add">
                <x-filament::icon icon="heroicon-o-plus" class="h-5 w-5" />
                <span>Add Requisition</span>
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error') || $errors->has('approval'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') ?: $errors->first('approval') }}
            </div>
        @endif

        <div class="req-filters">
            <label class="req-search-wrap">
                <x-filament::icon icon="heroicon-o-magnifying-glass" class="req-search-icon" />
                <input wire:model.live.debounce.400ms="search" type="search" placeholder="Search ref, requester, dept..." class="req-search">
            </label>

            <select wire:model.live="status" class="req-select">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>

            <div class="req-columns" x-on:click.outside="openColumns = false">
                <button type="button" class="req-columns-button" x-on:click="openColumns = ! openColumns">
                    <x-filament::icon icon="heroicon-o-view-columns" class="h-5 w-5" />
                    <span>Columns</span>
                </button>
                <div class="req-columns-menu" x-show="openColumns" x-cloak>
                    @foreach ($columns as $key => $label)
                        <label class="req-column-option">
                            <input type="checkbox" x-model="columns.{{ $key }}">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="req-table-wrap" wire:loading.class="req-table-wrap--loading" wire:target="search,status,gotoPage,nextPage,previousPage,approve,reject">
            <div class="req-loading" wire:loading.delay.flex wire:target="search,status,gotoPage,nextPage,previousPage,approve,reject">
                <span class="req-spinner"></span>
                <span>Loading</span>
            </div>
            <div class="req-table-scroll">
                <table class="req-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th x-show="columns.ref">Ref</th>
                            <th x-show="columns.date">Date</th>
                            <th x-show="columns.requester">Requester</th>
                            <th x-show="columns.department">Department</th>
                            <th x-show="columns.items">Items</th>
                            <th x-show="columns.total_qty">Total Qty</th>
                            <th x-show="columns.status">Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requisitions as $requisition)
                            @php
                                $statusClass = match ($requisition->status) {
                                    'approved' => 'req-status--approved',
                                    'rejected' => 'req-status--rejected',
                                    default => 'req-status--pending',
                                };
                                $isPending = $requisition->status === \App\Models\InventoryRequisition::STATUS_PENDING;
                            @endphp
                            <tr>
                                <td>{{ $requisitions->firstItem() + $loop->index }}</td>
                                <td x-show="columns.ref" class="req-ref">{{ $requisition->ref_no }}</td>
                                <td x-show="columns.date">{{ $requisition->requisition_date?->format('d/m/Y') }}</td>
                                <td x-show="columns.requester">{{ $requisition->requester_name }}</td>
                                <td x-show="columns.department">{{ $requisition->department ?: '-' }}</td>
                                <td x-show="columns.items">{{ $requisition->items_count }}</td>
                                <td x-show="columns.total_qty">{{ $this->formatNumber((float) ($requisition->items_sum_quantity ?? 0)) }}</td>
                                <td x-show="columns.status">
                                    <span class="req-status {{ $statusClass }}">{{ $requisition->status }}</span>
                                </td>
                                <td>
                                    <div class="req-actions">
                                        <a href="{{ route('inventory.requisitions.show', $requisition) }}" wire:navigate class="req-icon-button" title="View" aria-label="View {{ $requisition->ref_no }}">
                                            <x-filament::icon icon="heroicon-o-eye" />
                                        </a>
                                        @if ($isPending)
                                            <a href="{{ route('inventory.requisitions.edit', $requisition) }}" wire:navigate class="req-icon-button req-icon-button--edit" title="Edit" aria-label="Edit {{ $requisition->ref_no }}">
                                                <x-filament::icon icon="heroicon-o-pencil-square" />
                                            </a>
                                            <button type="button" wire:click="approve({{ $requisition->id }})" class="req-icon-button req-icon-button--approve" title="Approve" aria-label="Approve {{ $requisition->ref_no }}">
                                                <x-filament::icon icon="heroicon-o-check" />
                                            </button>
                                            <button type="button" wire:click="reject({{ $requisition->id }})" class="req-icon-button req-icon-button--reject" title="Reject" aria-label="Reject {{ $requisition->ref_no }}">
                                                <x-filament::icon icon="heroicon-o-x-mark" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-10 text-center text-gray-500">No requisitions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $requisitions->links() }}</div>
    </div>
</x-filament-panels::page>
