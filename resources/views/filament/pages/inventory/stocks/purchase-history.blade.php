<x-filament-panels::page>
    @php
        $items = $this->getItems();
        $selectedItem = $this->selectedItem();
        $rows = $this->historyRows();
        $summary = $this->summary();
    @endphp

    <style>
        .purchase-history-page {
            color: #001b33;
        }

        .purchase-history-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .purchase-history-title {
            color: #001b33;
            font-size: 28px;
            font-weight: 800;
            line-height: 1.15;
        }

        .purchase-history-subtitle {
            margin-top: 4px;
            color: #64748b;
            font-size: 16px;
        }

        .purchase-history-export {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 0 16px;
            color: #001b33;
            font-size: 16px;
            font-weight: 500;
        }

        .purchase-history-filter-card,
        .purchase-history-empty,
        .purchase-history-table-wrap,
        .purchase-history-summary-card {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
        }

        .purchase-history-filter-card {
            padding: 20px 18px;
        }

        .purchase-history-filters {
            display: grid;
            grid-template-columns: minmax(280px, 1fr) minmax(150px, 180px) minmax(150px, 180px);
            gap: 14px;
            align-items: end;
        }

        .purchase-history-field {
            display: flex;
            min-width: 0;
            flex-direction: column;
            gap: 8px;
            color: #001b33;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.25;
        }

        .purchase-history-control {
            width: 100%;
            min-width: 0;
            height: 42px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 8px 14px;
            color: #001b33;
            font-size: 16px;
            line-height: 1.4;
            outline: none;
        }

        .purchase-history-date {
            cursor: pointer;
        }

        .purchase-history-control:focus {
            border-color: #173c63;
            box-shadow: 0 0 0 1px #173c63;
        }

        .purchase-history-empty {
            display: flex;
            min-height: 244px;
            align-items: center;
            justify-content: center;
            margin-top: 18px;
            padding: 42px 20px;
            text-align: center;
        }

        .purchase-history-empty-icon {
            width: 52px;
            height: 52px;
            margin: 0 auto 18px;
            color: #64748b;
        }

        .purchase-history-empty-text {
            color: #001b33;
            font-size: 22px;
            font-weight: 500;
        }

        .purchase-history-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-top: 18px;
        }

        .purchase-history-summary-card {
            padding: 26px 28px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
        }

        .purchase-history-summary-label {
            color: #64748b;
            font-size: 16px;
        }

        .purchase-history-summary-value {
            margin-top: 4px;
            color: #001b33;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.2;
        }

        .purchase-history-summary-value--green {
            color: #007062;
        }

        .purchase-history-table-wrap {
            position: relative;
            overflow: hidden;
            margin-top: 18px;
        }

        .purchase-history-table-wrap--loading .purchase-history-table {
            opacity: 0.55;
        }

        .purchase-history-loading {
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

        .purchase-history-spinner {
            width: 14px;
            height: 14px;
            border: 2px solid #cbd5e1;
            border-top-color: #007062;
            border-radius: 999px;
            animation: purchase-history-spin 700ms linear infinite;
        }

        @keyframes purchase-history-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .purchase-history-table-scroll {
            overflow-x: auto;
        }

        .purchase-history-table {
            min-width: 1180px;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            color: #001b33;
            font-size: 16px;
        }

        .purchase-history-table thead {
            background: #f1f5f9;
        }

        .purchase-history-table th,
        .purchase-history-table td {
            border-bottom: 1px solid #cbd5e1;
            padding: 16px 24px;
            text-align: left;
            vertical-align: middle;
            white-space: nowrap;
        }

        .purchase-history-table th {
            color: #64748b;
            font-weight: 700;
        }

        .purchase-history-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .purchase-history-ref {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 13px;
        }

        @media (max-width: 1023px) {
            .purchase-history-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .purchase-history-filters {
                grid-template-columns: 1fr 1fr;
            }

            .purchase-history-field:first-child {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 767px) {
            .purchase-history-header {
                flex-direction: column;
            }

            .purchase-history-export {
                width: 100%;
            }

            .purchase-history-filters,
            .purchase-history-summary {
                grid-template-columns: 1fr;
            }

            .purchase-history-field:first-child {
                grid-column: auto;
            }
        }

        .dark .purchase-history-page,
        .dark .purchase-history-title,
        .dark .purchase-history-field,
        .dark .purchase-history-empty-text,
        .dark .purchase-history-summary-value,
        .dark .purchase-history-table {
            color: #ffffff;
        }

        .dark .purchase-history-subtitle,
        .dark .purchase-history-summary-label {
            color: #94a3b8;
        }

        .dark .purchase-history-filter-card,
        .dark .purchase-history-empty,
        .dark .purchase-history-table-wrap,
        .dark .purchase-history-summary-card,
        .dark .purchase-history-control,
        .dark .purchase-history-export,
        .dark .purchase-history-loading {
            border-color: #334155;
            background: #111827;
            color: #ffffff;
        }

        .dark .purchase-history-table thead {
            background: #1f2937;
        }

        .dark .purchase-history-table th,
        .dark .purchase-history-table td {
            border-bottom-color: #334155;
        }
    </style>

    <div class="purchase-history-page space-y-5">
        <div class="purchase-history-header">
            <div>
                <h2 class="purchase-history-title">Item Purchase History</h2>
                <p class="purchase-history-subtitle">View item-wise stock-in purchase history</p>
            </div>

            @if ($selectedItem)
                <button
                    type="button"
                    wire:click="exportCsv"
                    wire:loading.attr="disabled"
                    wire:target="exportCsv"
                    class="purchase-history-export"
                >
                    <x-filament::icon icon="heroicon-o-arrow-down-tray" class="h-5 w-5" />
                    <span>Export CSV</span>
                </button>
            @endif
        </div>

        <div class="purchase-history-filter-card">
            <div class="purchase-history-filters">
                <label class="purchase-history-field">
                    <span>Select Item</span>
                    <select wire:model.live="selectedItemId" class="purchase-history-control">
                        <option value="">Choose item...</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}">{{ $item->item_code }} &mdash; {{ $item->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="purchase-history-field">
                    <span>From</span>
                    <input
                        wire:model.live="fromDate"
                        type="date"
                        class="purchase-history-control purchase-history-date"
                        onclick="try { this.showPicker?.() } catch (e) {}"
                        onfocus="try { this.showPicker?.() } catch (e) {}"
                    >
                </label>

                <label class="purchase-history-field">
                    <span>To</span>
                    <input
                        wire:model.live="toDate"
                        type="date"
                        class="purchase-history-control purchase-history-date"
                        onclick="try { this.showPicker?.() } catch (e) {}"
                        onfocus="try { this.showPicker?.() } catch (e) {}"
                    >
                </label>
            </div>
        </div>

        @if (! $selectedItem)
            <div class="purchase-history-empty">
                <div>
                    <x-filament::icon icon="heroicon-o-cube" class="purchase-history-empty-icon" />
                    <p class="purchase-history-empty-text">Select an item to see purchase history</p>
                </div>
            </div>
        @else
            <div class="purchase-history-summary">
                <div class="purchase-history-summary-card">
                    <p class="purchase-history-summary-label">Purchases</p>
                    <p class="purchase-history-summary-value">{{ $summary['purchases'] }}</p>
                </div>
                <div class="purchase-history-summary-card">
                    <p class="purchase-history-summary-label">Total Qty</p>
                    <p class="purchase-history-summary-value purchase-history-summary-value--green">{{ $this->formatNumber($summary['totalQty']) }}</p>
                </div>
                <div class="purchase-history-summary-card">
                    <p class="purchase-history-summary-label">Total Cost</p>
                    <p class="purchase-history-summary-value">&#2547;{{ $this->formatNumber($summary['totalCost']) }}</p>
                </div>
                <div class="purchase-history-summary-card">
                    <p class="purchase-history-summary-label">Avg Rate</p>
                    <p class="purchase-history-summary-value">&#2547;{{ number_format($summary['avgRate'], 2) }}</p>
                </div>
            </div>

            <div
                class="purchase-history-table-wrap"
                wire:loading.class="purchase-history-table-wrap--loading"
                wire:target="selectedItemId,fromDate,toDate"
            >
                <div
                    class="purchase-history-loading"
                    wire:loading.delay.flex
                    wire:target="selectedItemId,fromDate,toDate"
                >
                    <span class="purchase-history-spinner"></span>
                    <span>Loading</span>
                </div>

                <div class="purchase-history-table-scroll">
                    <table class="purchase-history-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Ref</th>
                                <th>Supplier</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Unit Cost</th>
                                <th>Total</th>
                                <th>Reference No.</th>
                                <th>Expiry</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row->stockIn?->stock_in_date?->toDateString() ?: '-' }}</td>
                                    <td class="purchase-history-ref">{{ $this->transactionRef($row) }}</td>
                                    <td>{{ $row->stockIn?->supplier?->company_name ?: '-' }}</td>
                                    <td>{{ $this->formatNumber((float) $row->quantity) }}</td>
                                    <td>{{ $row->unit?->name ?: '-' }}</td>
                                    <td>&#2547;{{ $this->formatNumber((float) $row->unit_cost) }}</td>
                                    <td>&#2547;{{ $this->formatNumber((float) $row->total) }}</td>
                                    <td>{{ $row->stockIn?->reference_number ?: '-' }}</td>
                                    <td>{{ $row->stockIn?->expiry_warranty_date?->toDateString() ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-10 text-center text-gray-500">
                                        No purchase history found for the selected filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
