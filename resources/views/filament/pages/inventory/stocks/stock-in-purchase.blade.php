<x-filament-panels::page>
    @php
        $items = $this->getItems();
        $units = $this->getUnits();
        $suppliers = $this->getSuppliers();
    @endphp

    <style>
        .stock-in-page {
            color: #001b33;
        }

        .stock-in-title {
            color: #001b33;
            font-size: 28px;
            font-weight: 800;
            line-height: 1.15;
        }

        .stock-in-subtitle {
            margin-top: 4px;
            color: #64748b;
            font-size: 16px;
        }

        .stock-in-card {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 26px 28px 28px;
        }

        .stock-in-section-title {
            color: #001b33;
            font-size: 18px;
            font-weight: 800;
        }

        .stock-in-required {
            color: #dc2626;
        }

        .stock-in-picker {
            display: grid;
            grid-template-columns: minmax(260px, 1fr) minmax(170px, 0.38fr) minmax(140px, 0.38fr) minmax(170px, 0.38fr) auto;
            gap: 14px;
            align-items: end;
            margin-top: 16px;
        }

        .stock-in-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-top: 24px;
        }

        .stock-in-field {
            display: flex;
            min-width: 0;
            flex-direction: column;
            gap: 8px;
            color: #001b33;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.25;
        }

        .stock-in-field--wide {
            grid-column: 1 / -1;
        }

        .stock-in-control {
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

        .stock-in-date-control {
            cursor: pointer;
        }

        textarea.stock-in-control {
            min-height: 64px;
            height: auto;
            resize: vertical;
        }

        .stock-in-control:focus {
            border-color: #173c63;
            box-shadow: 0 0 0 1px #173c63;
        }

        .stock-in-control::placeholder {
            color: #94a3b8;
        }

        .stock-in-error {
            color: #dc2626;
            font-size: 12px;
            line-height: 1.35;
        }

        .stock-in-add,
        .stock-in-submit,
        .stock-in-file {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            border-radius: 8px;
            padding: 10px 24px;
            font-size: 16px;
            font-weight: 600;
            line-height: 1;
        }

        .stock-in-add,
        .stock-in-submit {
            border: 1px solid #007062;
            background: #007062;
            color: #ffffff;
        }

        .stock-in-submit:disabled {
            border-color: #7db4aa;
            background: #7db4aa;
            cursor: not-allowed;
        }

        .stock-in-submit {
            margin-top: 26px;
            margin-bottom: 2px;
            padding-inline: 26px;
        }

        .stock-in-attachment {
            gap: 10px;
            padding-top: 6px;
            padding-bottom: 8px;
        }

        .stock-in-file {
            width: fit-content;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #001b33;
            cursor: pointer;
        }

        .stock-in-items {
            overflow: hidden;
            margin-top: 18px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
        }

        .stock-in-items-scroll {
            overflow-x: auto;
        }

        .stock-in-items-table {
            min-width: 840px;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            color: #001b33;
            font-size: 16px;
        }

        .stock-in-items-table thead,
        .stock-in-items-table tfoot {
            background: #f1f5f9;
        }

        .stock-in-items-table th,
        .stock-in-items-table td {
            border-bottom: 1px solid #cbd5e1;
            padding: 14px 18px;
            text-align: left;
            vertical-align: middle;
            white-space: nowrap;
        }

        .stock-in-items-table th {
            color: #64748b;
            font-weight: 700;
        }

        .stock-in-items-table tfoot td {
            border-bottom: 0;
            font-weight: 800;
        }

        .stock-in-remove {
            display: inline-flex;
            width: 18px;
            height: 18px;
            align-items: center;
            justify-content: center;
            border: 0;
            background: transparent;
            color: #dc2626;
            cursor: pointer;
        }

        .stock-in-remove svg {
            width: 18px;
            height: 18px;
        }

        .stock-in-hidden-file {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
        }

        @media (max-width: 1180px) {
            .stock-in-picker {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .stock-in-add {
                width: 100%;
            }
        }

        @media (max-width: 767px) {
            .stock-in-card {
                padding: 22px 18px;
            }

            .stock-in-picker,
            .stock-in-grid {
                grid-template-columns: 1fr;
            }

            .stock-in-field--wide {
                grid-column: auto;
            }

            .stock-in-submit,
            .stock-in-file {
                width: 100%;
            }
        }

        .dark .stock-in-page,
        .dark .stock-in-title,
        .dark .stock-in-section-title,
        .dark .stock-in-field,
        .dark .stock-in-items-table {
            color: #ffffff;
        }

        .dark .stock-in-subtitle {
            color: #94a3b8;
        }

        .dark .stock-in-card,
        .dark .stock-in-control,
        .dark .stock-in-file,
        .dark .stock-in-items {
            border-color: #334155;
            background: #111827;
            color: #ffffff;
        }

        .dark .stock-in-items-table thead,
        .dark .stock-in-items-table tfoot {
            background: #1f2937;
        }

        .dark .stock-in-items-table th,
        .dark .stock-in-items-table td {
            border-bottom-color: #334155;
        }
    </style>

    <div class="stock-in-page space-y-6">
        <div>
            <h2 class="stock-in-title">Record Stock In</h2>
            <p class="stock-in-subtitle">Add received items to inventory (multiple items supported)</p>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit="recordStockIn" class="stock-in-card">
            <h3 class="stock-in-section-title">Select Items <span class="stock-in-required">*</span></h3>

            <div class="stock-in-picker">
                <label class="stock-in-field">
                    <span>Item</span>
                    <select wire:model.live="selectedItemId" class="stock-in-control">
                        <option value="">Choose item...</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->item_code }} &mdash; {{ $item->name }} ({{ $item->current_stock_quantity }} {{ $item->unit?->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('selectedItemId') <span class="stock-in-error">{{ $message }}</span> @enderror
                </label>

                <label class="stock-in-field">
                    <span>Unit Type</span>
                    <select wire:model="selectedUnitId" class="stock-in-control">
                        <option value="">Select unit</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedUnitId') <span class="stock-in-error">{{ $message }}</span> @enderror
                </label>

                <label class="stock-in-field">
                    <span>Quantity</span>
                    <input wire:model="quantity" type="number" min="1" step="0.01" class="stock-in-control">
                    @error('quantity') <span class="stock-in-error">{{ $message }}</span> @enderror
                </label>

                <label class="stock-in-field">
                    <span>Unit Cost (&#2547;)</span>
                    <input wire:model="unitCost" type="number" min="0" step="0.01" class="stock-in-control">
                    @error('unitCost') <span class="stock-in-error">{{ $message }}</span> @enderror
                </label>

                <button type="button" wire:click="addSelectedItem" class="stock-in-add">
                    <x-filament::icon icon="heroicon-o-plus" class="h-5 w-5" />
                    <span>Add</span>
                </button>
            </div>

            @error('selectedItems') <div class="stock-in-error mt-3">{{ $message }}</div> @enderror

            @if (count($selectedItems))
                <div class="stock-in-items">
                    <div class="stock-in-items-scroll">
                        <table class="stock-in-items-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Unit Cost</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedItems as $selectedItem)
                                    <tr wire:key="stock-in-selected-{{ $selectedItem['inventory_item_id'] }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $selectedItem['item_name'] }}</td>
                                        <td>{{ $selectedItem['unit_name'] }}</td>
                                        <td>{{ $selectedItem['quantity'] }}</td>
                                        <td>&#2547;{{ $selectedItem['unit_cost'] }}</td>
                                        <td>&#2547;{{ $selectedItem['total'] }}</td>
                                        <td>
                                            <button type="button" wire:click="removeSelectedItem({{ $loop->index }})" class="stock-in-remove" title="Remove" aria-label="Remove {{ $selectedItem['item_name'] }}">
                                                <x-filament::icon icon="heroicon-o-trash" />
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right">Grand Total</td>
                                    <td>&#2547;{{ rtrim(rtrim(number_format($this->grandTotal(), 2, '.', ''), '0'), '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            <div class="stock-in-grid">
                <label class="stock-in-field">
                    <span>Stock-in Date <span class="stock-in-required">*</span></span>
                    <input
                        wire:model="stockInDate"
                        type="date"
                        class="stock-in-control stock-in-date-control"
                        onclick="try { this.showPicker?.() } catch (e) {}"
                        onfocus="try { this.showPicker?.() } catch (e) {}"
                    >
                    @error('stockInDate') <span class="stock-in-error">{{ $message }}</span> @enderror
                </label>

                <label class="stock-in-field">
                    <span>Expiry / Warranty Date (Optional)</span>
                    <input
                        wire:model="expiryWarrantyDate"
                        type="date"
                        class="stock-in-control stock-in-date-control"
                        onclick="try { this.showPicker?.() } catch (e) {}"
                        onfocus="try { this.showPicker?.() } catch (e) {}"
                    >
                    @error('expiryWarrantyDate') <span class="stock-in-error">{{ $message }}</span> @enderror
                </label>

                <label class="stock-in-field">
                    <span>Reference Number</span>
                    <input wire:model="referenceNumber" type="text" placeholder="PO number, invoice no., delivery note..." class="stock-in-control">
                    @error('referenceNumber') <span class="stock-in-error">{{ $message }}</span> @enderror
                </label>

                <label class="stock-in-field">
                    <span>Supplier</span>
                    <select wire:model="supplierId" class="stock-in-control">
                        <option value="">None</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                        @endforeach
                    </select>
                    @error('supplierId') <span class="stock-in-error">{{ $message }}</span> @enderror
                </label>

                <label class="stock-in-field stock-in-field--wide">
                    <span>Purpose / Notes</span>
                    <textarea wire:model="purposeNotes" rows="3" class="stock-in-control"></textarea>
                    @error('purposeNotes') <span class="stock-in-error">{{ $message }}</span> @enderror
                </label>

                <div class="stock-in-field stock-in-field--wide stock-in-attachment">
                    <span>Attachment</span>
                    <label class="stock-in-file">
                        <x-filament::icon icon="heroicon-o-arrow-up-tray" class="h-5 w-5" />
                        <span>{{ $attachment?->getClientOriginalName() ?: 'Choose file' }}</span>
                        <input wire:model="attachment" type="file" class="stock-in-hidden-file">
                    </label>
                    @error('attachment') <span class="stock-in-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <button
                type="submit"
                class="stock-in-submit"
                @disabled(! count($selectedItems))
                wire:loading.attr="disabled"
                wire:target="recordStockIn,attachment"
            >
                Record Stock In
            </button>
        </form>
    </div>
</x-filament-panels::page>
