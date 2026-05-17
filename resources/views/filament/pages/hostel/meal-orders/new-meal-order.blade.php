<x-filament-panels::page>
    <style>
        .meal-form-card {
            max-width: 768px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 40px 36px 36px;
        }

        .meal-field {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 24px;
            color: #001b33;
            font-size: 16px;
            font-weight: 500;
        }

        .meal-inline-fields {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(220px, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .meal-inline-fields .meal-field {
            margin-bottom: 0;
        }

        .meal-control {
            width: 100%;
            height: 52px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #f8fafc;
            padding: 10px 24px;
            color: #001b33;
            font-size: 16px;
            outline: none;
        }

        .meal-control:focus {
            border-color: #111827;
            box-shadow: 0 0 0 1px #111827;
        }

        .meal-date-field {
            cursor: pointer;
        }

        .meal-date-field .meal-control {
            cursor: pointer;
        }

        .meal-checkbox-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .meal-checkbox {
            display: grid;
            grid-template-columns: auto minmax(120px, 1fr) minmax(160px, 192px);
            align-items: center;
            gap: 16px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #f8fafc;
            padding: 14px 16px;
            color: #001b33;
            font-size: 16px;
            font-weight: 500;
        }

        .meal-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border-radius: 4px;
        }

        .meal-qty {
            width: 100%;
            min-height: 48px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #ffffff;
            padding: 10px 16px;
            color: #001b33;
            font-size: 16px;
            outline: none;
        }

        .meal-qty:focus {
            border-color: #111827;
            box-shadow: 0 0 0 1px #111827;
        }

        .meal-error {
            color: #dc2626;
            font-size: 12px;
            font-weight: 400;
        }

        .meal-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin-top: 6px;
        }

        .meal-primary,
        .meal-cancel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 140px;
            height: 56px;
            border-radius: 10px;
            padding: 0 28px;
            font-size: 16px;
            font-weight: 500;
        }

        .meal-primary {
            border: 1px solid #173c63;
            background: #173c63;
            color: #ffffff;
        }

        .meal-cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #001b33;
            text-decoration: none;
        }

        @media (max-width: 767px) {
            .meal-form-card {
                padding: 24px 18px;
            }

            .meal-inline-fields {
                grid-template-columns: 1fr;
            }

            .meal-checkbox-grid {
                grid-template-columns: 1fr;
            }

            .meal-checkbox {
                grid-template-columns: auto minmax(0, 1fr);
                gap: 12px 14px;
            }

            .meal-qty {
                grid-column: 1 / -1;
                width: 100%;
            }

            .meal-primary,
            .meal-cancel {
                width: 100%;
            }
        }
    </style>

    <div class="space-y-8">
        @php
            $availableMealOrderDates = $this->selectedMealOrderDates();
            $selectedOrderDateIsAvailable = $this->date && in_array($this->date, $availableMealOrderDates, true);
        @endphp

        <form wire:submit="save" class="meal-form-card dark:border-gray-800 dark:bg-gray-900">
            <div class="meal-inline-fields">
                <label class="meal-field">
                    <span>Guest (checked-in only)</span>
                    <select wire:model.live="guestId" class="meal-control">
                        <option value="">Select...</option>
                        @foreach ($guests as $guest)
                            <option value="{{ $guest->id }}">
                                {{ $guest->name }}{{ $guest->activeBooking?->room?->room_number ? ' - Room ' . $guest->activeBooking->room->room_number : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('guestId') <span class="meal-error">{{ $message }}</span> @enderror
                </label>

                <label
                    class="meal-field meal-date-field"
                    tabindex="0"
                >
                    <span>Date</span>
                    <select
                        wire:model.live="date"
                        class="meal-control"
                        required
                        @disabled($availableMealOrderDates === [])
                    >
                        @if ($availableMealOrderDates === [])
                            <option value="" selected>Select a checked-in guest first...</option>
                        @elseif (! $selectedOrderDateIsAvailable)
                            <option value="" selected>Select a booking date...</option>
                        @endif
                        @foreach ($availableMealOrderDates as $availableMealOrderDate)
                            <option value="{{ $availableMealOrderDate }}">
                                {{ \Illuminate\Support\Carbon::parse($availableMealOrderDate)->format('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('date') <span class="meal-error">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="meal-field">
                <span>Meal Type</span>
                <div class="meal-checkbox-grid">
                    <div class="meal-checkbox">
                        <input id="meal-breakfast" wire:model.live="mealTypes" type="checkbox" value="breakfast">
                        <label for="meal-breakfast">Breakfast</label>
                        <input wire:model.live="mealQuantities.breakfast" type="number" min="1" max="100" placeholder="Quantity" class="meal-qty" aria-label="Breakfast quantity">
                    </div>
                    <div class="meal-checkbox">
                        <input id="meal-lunch" wire:model.live="mealTypes" type="checkbox" value="lunch">
                        <label for="meal-lunch">Lunch</label>
                        <input wire:model.live="mealQuantities.lunch" type="number" min="1" max="100" placeholder="Quantity" class="meal-qty" aria-label="Lunch quantity">
                    </div>
                    <div class="meal-checkbox">
                        <input id="meal-dinner" wire:model.live="mealTypes" type="checkbox" value="dinner">
                        <label for="meal-dinner">Dinner</label>
                        <input wire:model.live="mealQuantities.dinner" type="number" min="1" max="100" placeholder="Quantity" class="meal-qty" aria-label="Dinner quantity">
                    </div>
                </div>
                @error('mealTypes') <span class="meal-error">{{ $message }}</span> @enderror
                @error('mealTypes.*') <span class="meal-error">{{ $message }}</span> @enderror
                @error('mealQuantities.breakfast') <span class="meal-error">{{ $message }}</span> @enderror
                @error('mealQuantities.lunch') <span class="meal-error">{{ $message }}</span> @enderror
                @error('mealQuantities.dinner') <span class="meal-error">{{ $message }}</span> @enderror
            </div>

            <div class="meal-actions">
                <button type="submit" class="meal-primary">Create Order</button>
                <a href="{{ \App\Filament\Pages\Hostel\MealOrders\TodayMealOrders::getUrl(panel: 'admin') }}" class="meal-cancel">Cancel</a>
            </div>
        </form>
    </div>
</x-filament-panels::page>
