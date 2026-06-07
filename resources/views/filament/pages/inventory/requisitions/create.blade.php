<x-filament-panels::page>
    @php
        $items = $this->getItems();
        $units = $this->getUnits();
    @endphp

    <style>
        .req-form-page { color: #001b33; }
        .req-form-title { color: #001b33; font-size: 28px; font-weight: 800; line-height: 1.15; }
        .req-form-subtitle { margin-top: 4px; color: #64748b; font-size: 16px; }
        .req-form-card { border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; padding: 26px 24px 28px; }
        .req-top-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; }
        .req-item-grid { display: grid; grid-template-columns: minmax(260px, 1fr) minmax(150px, .36fr) minmax(120px, .32fr) minmax(180px, .44fr) auto; gap: 14px; align-items: end; margin-top: 16px; }
        .req-form-field { display: flex; min-width: 0; flex-direction: column; gap: 8px; color: #001b33; font-size: 16px; font-weight: 500; line-height: 1.25; }
        .req-required { color: #dc2626; }
        .req-form-control { width: 100%; min-width: 0; height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; padding: 8px 14px; color: #001b33; font-size: 16px; line-height: 1.4; outline: none; }
        .req-form-control:focus { border-color: #173c63; box-shadow: 0 0 0 1px #173c63; }
        .req-form-error { color: #dc2626; font-size: 12px; line-height: 1.35; }
        .req-form-section { margin-top: 28px; color: #001b33; font-size: 18px; font-weight: 800; }
        .req-form-add, .req-form-submit, .req-form-cancel { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; border-radius: 8px; padding: 10px 24px; font-size: 16px; font-weight: 600; line-height: 1; }
        .req-form-add, .req-form-submit { border: 1px solid #007062; background: #007062; color: #fff; }
        .req-form-submit:disabled { border-color: #7db4aa; background: #7db4aa; cursor: not-allowed; }
        .req-form-cancel { border: 1px solid #cbd5e1; background: #fff; color: #001b33; text-decoration: none; }
        .req-items { overflow: hidden; margin-top: 18px; border: 1px solid #cbd5e1; border-radius: 8px; }
        .req-items-scroll { overflow-x: auto; }
        .req-items-table { min-width: 760px; width: 100%; border-collapse: separate; border-spacing: 0; color: #001b33; font-size: 16px; }
        .req-items-table thead { background: #f1f5f9; }
        .req-items-table th, .req-items-table td { border-bottom: 1px solid #cbd5e1; padding: 14px 18px; text-align: left; vertical-align: middle; white-space: nowrap; }
        .req-items-table th { color: #64748b; font-weight: 700; }
        .req-items-table tbody tr:last-child td { border-bottom: 0; }
        .req-remove { display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; border: 0; background: transparent; color: #dc2626; cursor: pointer; }
        .req-remove svg { width: 18px; height: 18px; }
        .req-form-actions { display: flex; gap: 14px; margin-top: 26px; }
        @media (max-width: 1180px) { .req-top-grid, .req-item-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .req-form-add { width: 100%; } }
        @media (max-width: 767px) { .req-form-card { padding: 22px 18px; } .req-top-grid, .req-item-grid { grid-template-columns: 1fr; } .req-form-actions { flex-direction: column; } .req-form-submit, .req-form-cancel { width: 100%; } }
        .dark .req-form-page, .dark .req-form-title, .dark .req-form-field, .dark .req-form-section, .dark .req-items-table { color: #fff; }
        .dark .req-form-subtitle { color: #94a3b8; }
        .dark .req-form-card, .dark .req-form-control, .dark .req-form-cancel, .dark .req-items { border-color: #334155; background: #111827; color: #fff; }
        .dark .req-items-table thead { background: #1f2937; }
        .dark .req-items-table th, .dark .req-items-table td { border-bottom-color: #334155; }
    </style>

    <div class="req-form-page space-y-6">
        <div>
            <h2 class="req-form-title">Add Requisition</h2>
            <p class="req-form-subtitle">Submit a new purchase requisition</p>
        </div>

        <form wire:submit="submitRequisition" class="req-form-card">
            <div class="req-top-grid">
                <label class="req-form-field">
                    <span>Requisition Date <span class="req-required">*</span></span>
                    <input
                        wire:model="requisitionDate"
                        type="date"
                        class="req-form-control"
                        onclick="try { this.showPicker?.() } catch (e) {}"
                        onfocus="try { this.showPicker?.() } catch (e) {}"
                    >
                    @error('requisitionDate') <span class="req-form-error">{{ $message }}</span> @enderror
                </label>
                <label class="req-form-field">
                    <span>Requester <span class="req-required">*</span></span>
                    <input wire:model="requesterName" type="text" class="req-form-control">
                    @error('requesterName') <span class="req-form-error">{{ $message }}</span> @enderror
                </label>
                <label class="req-form-field">
                    <span>Department</span>
                    <input wire:model="department" type="text" class="req-form-control">
                    @error('department') <span class="req-form-error">{{ $message }}</span> @enderror
                </label>
                <label class="req-form-field">
                    <span>Purpose</span>
                    <input wire:model="purpose" type="text" class="req-form-control">
                    @error('purpose') <span class="req-form-error">{{ $message }}</span> @enderror
                </label>
            </div>

            <h3 class="req-form-section">Items <span class="req-required">*</span></h3>
            <div class="req-item-grid">
                <label class="req-form-field">
                    <span>Item</span>
                    <select wire:model.live="selectedItemId" class="req-form-control">
                        <option value="">Choose item...</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }} (Available: {{ $this->formatNumber((float) $item->available_stock) }} {{ $item->unit?->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('selectedItemId') <span class="req-form-error">{{ $message }}</span> @enderror
                </label>
                <label class="req-form-field">
                    <span>Unit</span>
                    <select wire:model="selectedUnitId" class="req-form-control">
                        <option value="">Select unit</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedUnitId') <span class="req-form-error">{{ $message }}</span> @enderror
                </label>
                <label class="req-form-field">
                    <span>Qty</span>
                    <input wire:model="quantity" type="number" min="1" step="0.01" class="req-form-control">
                    @error('quantity') <span class="req-form-error">{{ $message }}</span> @enderror
                </label>
                <label class="req-form-field">
                    <span>Notes</span>
                    <input wire:model="notes" type="text" class="req-form-control">
                    @error('notes') <span class="req-form-error">{{ $message }}</span> @enderror
                </label>
                <button type="button" wire:click="addSelectedItem" class="req-form-add">
                    <x-filament::icon icon="heroicon-o-plus" class="h-5 w-5" />
                    <span>Add</span>
                </button>
            </div>

            @error('selectedItems') <div class="req-form-error mt-3">{{ $message }}</div> @enderror

            @if (count($selectedItems))
                <div class="req-items">
                    <div class="req-items-scroll">
                        <table class="req-items-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Notes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedItems as $selectedItem)
                                    <tr wire:key="requisition-selected-{{ $selectedItem['inventory_item_id'] }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $selectedItem['item_name'] }}</td>
                                        <td>{{ $selectedItem['unit_name'] }}</td>
                                        <td>{{ $selectedItem['quantity'] }}</td>
                                        <td>{{ $selectedItem['notes'] ?: '-' }}</td>
                                        <td>
                                            <button type="button" wire:click="removeSelectedItem({{ $loop->index }})" class="req-remove" title="Remove" aria-label="Remove {{ $selectedItem['item_name'] }}">
                                                <x-filament::icon icon="heroicon-o-trash" />
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="req-form-actions">
                <button type="submit" class="req-form-submit" @disabled(! count($selectedItems)) wire:loading.attr="disabled" wire:target="submitRequisition">
                    Submit Requisition
                </button>
                <a href="{{ \App\Filament\Pages\Inventory\Requisitions\AllRequisitions::getUrl(panel: 'admin') }}" wire:navigate class="req-form-cancel">Cancel</a>
            </div>
        </form>
    </div>
</x-filament-panels::page>
