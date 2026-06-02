<x-filament-panels::page>
    <style>
        .req-view-card { border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; padding: 24px; color: #001b33; }
        .req-view-head { display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; }
        .req-view-title { font-size: 28px; font-weight: 800; line-height: 1.15; }
        .req-view-meta { margin-top: 12px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .req-view-label { color: #64748b; font-size: 13px; }
        .req-view-value { margin-top: 3px; font-weight: 700; }
        .req-view-status { display: inline-flex; border-radius: 999px; padding: 4px 12px; font-size: 14px; }
        .req-view-status--pending { background: #ffedd5; color: #c2410c; }
        .req-view-status--approved { background: #d1fae5; color: #007a58; }
        .req-view-status--rejected { background: #fee2e2; color: #dc2626; }
        .req-view-table-scroll { overflow-x: auto; margin-top: 24px; border: 1px solid #cbd5e1; border-radius: 8px; }
        .req-view-table { min-width: 720px; width: 100%; border-collapse: separate; border-spacing: 0; font-size: 16px; }
        .req-view-table thead { background: #f1f5f9; }
        .req-view-table th, .req-view-table td { border-bottom: 1px solid #cbd5e1; padding: 14px 18px; text-align: left; white-space: nowrap; }
        .req-view-table tbody tr:last-child td { border-bottom: 0; }
        .req-view-back { display: inline-flex; margin-top: 18px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 18px; color: #001b33; text-decoration: none; }
        @media (max-width: 767px) { .req-view-head { flex-direction: column; } .req-view-meta { grid-template-columns: 1fr; } .req-view-back { width: 100%; justify-content: center; } }
        .dark .req-view-card, .dark .req-view-back { border-color: #334155; background: #111827; color: #fff; }
        .dark .req-view-label { color: #94a3b8; }
        .dark .req-view-table-scroll { border-color: #334155; }
        .dark .req-view-table thead { background: #1f2937; }
        .dark .req-view-table th, .dark .req-view-table td { border-bottom-color: #334155; }
    </style>

    @php
        $statusClass = match ($requisition->status) {
            'approved' => 'req-view-status--approved',
            'rejected' => 'req-view-status--rejected',
            default => 'req-view-status--pending',
        };
    @endphp

    <div class="req-view-card">
        <div class="req-view-head">
            <div>
                <h2 class="req-view-title">{{ $requisition->ref_no }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Requisition details and requested items</p>
            </div>
            <span class="req-view-status {{ $statusClass }}">{{ $requisition->status }}</span>
        </div>

        <div class="req-view-meta">
            <div>
                <p class="req-view-label">Date</p>
                <p class="req-view-value">{{ $requisition->requisition_date?->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="req-view-label">Requester</p>
                <p class="req-view-value">{{ $requisition->requester_name }}</p>
            </div>
            <div>
                <p class="req-view-label">Department</p>
                <p class="req-view-value">{{ $requisition->department ?: '-' }}</p>
            </div>
            <div>
                <p class="req-view-label">Purpose</p>
                <p class="req-view-value">{{ $requisition->purpose ?: '-' }}</p>
            </div>
            <div>
                <p class="req-view-label">Items</p>
                <p class="req-view-value">{{ $requisition->items->count() }}</p>
            </div>
            <div>
                <p class="req-view-label">Total Qty</p>
                <p class="req-view-value">{{ $this->formatNumber((float) $requisition->items->sum('quantity')) }}</p>
            </div>
        </div>

        <div class="req-view-table-scroll">
            <table class="req-view-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Qty</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requisition->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->item?->name ?: '-' }}</td>
                            <td>{{ $item->unit?->name ?: '-' }}</td>
                            <td>{{ $this->formatNumber((float) $item->quantity) }}</td>
                            <td>{{ $item->notes ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <a href="{{ \App\Filament\Pages\Inventory\Requisitions\AllRequisitions::getUrl(panel: 'admin') }}" wire:navigate class="req-view-back">Back to Requisitions</a>
    </div>
</x-filament-panels::page>
