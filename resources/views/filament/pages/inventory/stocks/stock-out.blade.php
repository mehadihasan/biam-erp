<x-filament-panels::page>
    @php
        $requisitions = $this->getEligibleRequisitions();
        $selectedRequisition = $this->selectedRequisition();
    @endphp

    <style>
        .stock-out-page { color: #001b33; }
        .stock-out-title { color: #001b33; font-size: 28px; font-weight: 800; line-height: 1.15; }
        .stock-out-subtitle { margin-top: 4px; color: #64748b; font-size: 16px; }
        .stock-out-layout { display: grid; grid-template-columns: minmax(0, 2fr) minmax(340px, .95fr); gap: 24px; align-items: start; }
        .stock-out-card, .stock-out-preview { border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; padding: 28px 26px; }
        .stock-out-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 26px 18px; }
        .stock-out-field { display: flex; min-width: 0; flex-direction: column; gap: 8px; color: #001b33; font-size: 16px; font-weight: 500; line-height: 1.25; }
        .stock-out-field--wide { grid-column: 1 / -1; }
        .stock-out-required { color: #dc2626; }
        .stock-out-control { width: 100%; min-width: 0; height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; padding: 8px 14px; color: #001b33; font-size: 16px; line-height: 1.4; outline: none; }
        .stock-out-date { cursor: pointer; }
        textarea.stock-out-control { min-height: 64px; height: auto; resize: vertical; }
        .stock-out-control:focus { border-color: #173c63; box-shadow: 0 0 0 1px #173c63; }
        .stock-out-control::placeholder { color: #94a3b8; }
        .stock-out-error { color: #dc2626; font-size: 12px; line-height: 1.35; }
        .stock-out-items { grid-column: 1 / -1; overflow: hidden; border: 1px solid #cbd5e1; border-radius: 8px; }
        .stock-out-items-scroll { overflow-x: auto; }
        .stock-out-items-table { min-width: 900px; width: 100%; border-collapse: separate; border-spacing: 0; color: #001b33; font-size: 16px; }
        .stock-out-items-table thead { background: #f1f5f9; }
        .stock-out-items-table th, .stock-out-items-table td { border-bottom: 1px solid #cbd5e1; padding: 14px 16px; text-align: left; vertical-align: middle; white-space: nowrap; }
        .stock-out-items-table th { color: #64748b; font-weight: 700; }
        .stock-out-items-table tbody tr:last-child td { border-bottom: 0; }
        .stock-out-row-control { width: 130px; height: 38px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; padding: 7px 10px; color: #001b33; font-size: 15px; outline: none; }
        .stock-out-bottom-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(170px, 220px); gap: 18px; grid-column: 1 / -1; }
        .stock-out-actions { margin-top: 28px; padding-top: 2px; }
        .stock-out-submit { display: inline-flex; align-items: center; justify-content: center; min-height: 42px; width: fit-content; border: 1px solid #007062; border-radius: 8px; background: #007062; padding: 10px 26px; color: #fff; font-size: 16px; font-weight: 600; line-height: 1; }
        .stock-out-submit:disabled { border-color: #7db4aa; background: #7db4aa; cursor: not-allowed; }
        .stock-out-preview-title { color: #001b33; font-size: 20px; font-weight: 800; }
        .stock-out-preview-muted { margin-top: 16px; color: #64748b; font-size: 16px; }
        .stock-out-preview-list { margin-top: 16px; display: grid; gap: 10px; color: #001b33; font-size: 16px; }
        @media (max-width: 1100px) { .stock-out-layout { grid-template-columns: 1fr; } }
        @media (max-width: 767px) { .stock-out-card, .stock-out-preview { padding: 22px 18px; } .stock-out-form-grid, .stock-out-bottom-grid { grid-template-columns: 1fr; } .stock-out-field--wide, .stock-out-items, .stock-out-bottom-grid { grid-column: auto; } .stock-out-submit { width: 100%; } }
        .dark .stock-out-page, .dark .stock-out-title, .dark .stock-out-field, .dark .stock-out-items-table, .dark .stock-out-preview-title, .dark .stock-out-preview-list { color: #fff; }
        .dark .stock-out-subtitle, .dark .stock-out-preview-muted { color: #94a3b8; }
        .dark .stock-out-card, .dark .stock-out-preview, .dark .stock-out-control, .dark .stock-out-items, .dark .stock-out-row-control { border-color: #334155; background: #111827; color: #fff; }
        .dark .stock-out-items-table thead { background: #1f2937; }
        .dark .stock-out-items-table th, .dark .stock-out-items-table td { border-bottom-color: #334155; }
    </style>

    <div class="stock-out-page space-y-6">
        <div>
            <h2 class="stock-out-title">Issue Stock</h2>
            <p class="stock-out-subtitle">Issue stock against approved requisitions only</p>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="stock-out-layout">
            <form wire:submit="issueStock" class="stock-out-card">
                <div class="stock-out-form-grid">
                    <label class="stock-out-field stock-out-field--wide">
                        <span>Approved Requisition <span class="stock-out-required">*</span></span>
                        <select wire:model.live="selectedRequisitionId" class="stock-out-control">
                            <option value="">Choose approved requisition...</option>
                            @foreach ($requisitions as $requisition)
                                <option value="{{ $requisition->id }}">
                                    {{ $requisition->ref_no }} - {{ $requisition->requester_name }}{{ $requisition->department ? ' (' . $requisition->department . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('selectedRequisitionId') <span class="stock-out-error">{{ $message }}</span> @enderror
                    </label>

                    @if (count($stockOutItems))
                        <div class="stock-out-items">
                            <div class="stock-out-items-scroll">
                                <table class="stock-out-items-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Item</th>
                                            <th>Unit</th>
                                            <th>Approved</th>
                                            <th>Remaining</th>
                                            <th>Available Stock</th>
                                            <th>Qty to Issue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stockOutItems as $stockOutItem)
                                            <tr wire:key="stock-out-item-{{ $stockOutItem['inventory_requisition_item_id'] }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $stockOutItem['item_name'] }}</td>
                                                <td>{{ $stockOutItem['unit_name'] ?: '-' }}</td>
                                                <td>{{ $stockOutItem['approved_quantity'] }}</td>
                                                <td>{{ $stockOutItem['remaining_quantity'] }}</td>
                                                <td>{{ $stockOutItem['available_stock'] }}</td>
                                                <td>
                                                    <input
                                                        wire:model.live="stockOutItems.{{ $loop->index }}.quantity_to_issue"
                                                        type="number"
                                                        min="0"
                                                        step="0.01"
                                                        max="{{ min((float) $stockOutItem['remaining_quantity'], (float) $stockOutItem['available_stock']) }}"
                                                        class="stock-out-row-control"
                                                        aria-label="Quantity to issue for {{ $stockOutItem['item_name'] }}"
                                                    >
                                                    @error('stockOutItems.' . $loop->index . '.quantity_to_issue') <span class="stock-out-error">{{ $message }}</span> @enderror
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @error('stockOutItems') <div class="stock-out-error stock-out-field--wide">{{ $message }}</div> @enderror

                    <label class="stock-out-field stock-out-field--wide">
                        <span>Issued To Department <span class="stock-out-required">*</span></span>
                        <input wire:model.live="issuedToDepartment" type="text" placeholder="e.g. Hostel Block A, Training Division" class="stock-out-control">
                        @error('issuedToDepartment') <span class="stock-out-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="stock-out-field stock-out-field--wide">
                        <span>Purpose / Reason <span class="stock-out-required">*</span></span>
                        <textarea wire:model="purposeReason" rows="3" class="stock-out-control"></textarea>
                        @error('purposeReason') <span class="stock-out-error">{{ $message }}</span> @enderror
                    </label>

                    <div class="stock-out-bottom-grid">
                        <label class="stock-out-field">
                            <span>Reference Document</span>
                            <input wire:model="referenceDocument" type="text" placeholder="Requisition number..." class="stock-out-control">
                            @error('referenceDocument') <span class="stock-out-error">{{ $message }}</span> @enderror
                        </label>

                        <label class="stock-out-field">
                            <span>Transaction Date</span>
                            <input
                                wire:model="transactionDate"
                                type="date"
                                class="stock-out-control stock-out-date"
                                onclick="try { this.showPicker?.() } catch (e) {}"
                                onfocus="try { this.showPicker?.() } catch (e) {}"
                            >
                            @error('transactionDate') <span class="stock-out-error">{{ $message }}</span> @enderror
                        </label>
                    </div>
                </div>

                <div class="stock-out-actions">
                    <button
                        type="submit"
                        class="stock-out-submit"
                        @disabled(! $this->canIssue())
                        wire:loading.attr="disabled"
                        wire:target="issueStock"
                    >
                        Issue Stock
                    </button>
                </div>
            </form>

            <aside class="stock-out-preview">
                <h3 class="stock-out-preview-title">Issue Preview</h3>

                @if (! $selectedRequisition)
                    <p class="stock-out-preview-muted">Select an approved requisition to see preview</p>
                @else
                    <div class="stock-out-preview-list">
                        <p><strong>Requisition:</strong> {{ $selectedRequisition->ref_no }}</p>
                        <p><strong>Requester:</strong> {{ $selectedRequisition->requester_name }}</p>
                        <p><strong>Department:</strong> {{ $selectedRequisition->department ?: '-' }}</p>
                        <p><strong>Total Qty to Issue:</strong> {{ $this->formatNumber($this->issueTotal()) }}</p>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-filament-panels::page>
