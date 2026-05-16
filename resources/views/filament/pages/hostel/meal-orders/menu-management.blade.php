<x-filament-panels::page>
    <style>
        .meal-table-card {
            overflow: hidden;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #ffffff;
        }

        .meal-table {
            min-width: 920px;
            width: 100%;
            border-collapse: collapse;
            color: #001b33;
            font-size: 16px;
        }

        .meal-table th {
            background: #f1f5f9;
            color: #526783;
            font-weight: 600;
        }

        .meal-table th,
        .meal-table td {
            border-bottom: 1px solid #cbd5e1;
            padding: 18px 24px;
            text-align: left;
        }

        .meal-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            background: #e9edf3;
            padding: 4px 14px;
            color: #526783;
            font-size: 14px;
            font-weight: 600;
        }

        .meal-add,
        .meal-save,
        .meal-modal-cancel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 54px;
            border-radius: 9px;
            padding: 0 24px;
            font-size: 16px;
            font-weight: 500;
        }

        .meal-add,
        .meal-save {
            border: 1px solid #173c63;
            background: #173c63;
            color: #ffffff;
        }

        .meal-modal-cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #001b33;
        }

        .meal-switch {
            position: relative;
            width: 58px;
            height: 28px;
            border-radius: 999px;
            background: #94a3b8;
        }

        .meal-switch::after {
            content: "";
            position: absolute;
            top: 4px;
            left: 4px;
            width: 20px;
            height: 20px;
            border-radius: 999px;
            background: #ffffff;
            transition: transform .15s ease;
        }

        .meal-switch--active {
            background: #24823c;
        }

        .meal-switch--active::after {
            transform: translateX(30px);
        }

        .meal-icon {
            width: 24px;
            height: 24px;
            color: #001b33;
        }

        .meal-icon--delete {
            color: #dc2626;
        }

        .meal-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            background: rgba(15, 23, 42, .28);
            padding: 8px;
        }

        .meal-modal {
            width: min(660px, calc(100vw - 16px));
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 36px 32px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .22);
        }

        .meal-modal-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .meal-modal-control {
            width: 100%;
            height: 54px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: #f8fafc;
            padding: 10px 18px;
            color: #001b33;
            font-size: 16px;
            outline: none;
        }

        .meal-modal-wide {
            grid-column: 1 / -1;
        }

        .meal-error {
            color: #dc2626;
            font-size: 12px;
        }

        @media (max-width: 767px) {
            .meal-modal-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="space-y-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <button type="button" wire:click="addItem" class="meal-add">
                <span class="mr-3 text-2xl leading-none">+</span>
                Add Item
            </button>
        </div>

        <div class="meal-table-card dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="meal-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Meal Type</th>
                            <th>Price (BCS)</th>
                            <th>Price (Guest/Others)</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->menuItems as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td><span class="meal-badge">{{ $item->meal_type }}</span></td>
                                <td>৳{{ number_format((float) $item->price_bcs, 0) }}</td>
                                <td>৳{{ number_format((float) $item->price_guest, 0) }}</td>
                                <td>
                                    <button type="button" wire:click="toggleActive({{ $item->id }})" class="meal-switch {{ $item->is_active ? 'meal-switch--active' : '' }}" aria-label="Toggle active"></button>
                                </td>
                                <td>
                                    <div class="flex items-center gap-4">
                                        <button type="button" wire:click="editItem({{ $item->id }})" aria-label="Edit">
                                            <x-filament::icon icon="heroicon-o-pencil" class="meal-icon" />
                                        </button>
                                        <button type="button" wire:click="deleteItem({{ $item->id }})" wire:confirm="Delete this menu item?" aria-label="Delete">
                                            <x-filament::icon icon="heroicon-o-trash" class="meal-icon meal-icon--delete" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-500">No menu items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($showModal)
        <div class="meal-modal-backdrop">
            <form wire:submit="saveItem" class="meal-modal">
                <h2 class="mb-6 text-2xl font-bold text-gray-800">Add Menu Item</h2>

                <div class="meal-modal-grid">
                    <div class="meal-modal-wide">
                        <input wire:model.live="name" type="text" placeholder="Item Name" class="meal-modal-control">
                        @error('name') <span class="meal-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="meal-modal-wide">
                        <select wire:model.live="mealType" class="meal-modal-control">
                            <option value="breakfast">breakfast</option>
                            <option value="lunch">lunch</option>
                            <option value="dinner">dinner</option>
                        </select>
                        @error('mealType') <span class="meal-error">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <input wire:model.live="priceBcs" type="number" min="0" step="0.01" placeholder="Price BCS" class="meal-modal-control">
                        @error('priceBcs') <span class="meal-error">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <input wire:model.live="priceGuest" type="number" min="0" step="0.01" placeholder="Price Guest/Others" class="meal-modal-control">
                        @error('priceGuest') <span class="meal-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap gap-4">
                    <button type="submit" class="meal-save">Save</button>
                    <button type="button" wire:click="closeModal" class="meal-modal-cancel">Cancel</button>
                </div>
            </form>
        </div>
    @endif
</x-filament-panels::page>
