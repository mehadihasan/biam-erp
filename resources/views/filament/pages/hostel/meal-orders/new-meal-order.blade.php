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

        .meal-checkbox-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .meal-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 52px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #f8fafc;
            padding: 10px 16px;
            color: #001b33;
            font-size: 16px;
            font-weight: 500;
        }

        .meal-checkbox input {
            width: 18px;
            height: 18px;
            border-radius: 4px;
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

            .meal-checkbox-grid {
                grid-template-columns: 1fr;
            }

            .meal-primary,
            .meal-cancel {
                width: 100%;
            }
        }
    </style>

    <div class="space-y-8">

        <form wire:submit="save" class="meal-form-card dark:border-gray-800 dark:bg-gray-900">
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

            <label class="meal-field">
                <span>Meal Type</span>
                <div class="meal-checkbox-grid">
                    <label class="meal-checkbox">
                        <input wire:model.live="mealTypes" type="checkbox" value="breakfast">
                        <span>Breakfast</span>
                    </label>
                    <label class="meal-checkbox">
                        <input wire:model.live="mealTypes" type="checkbox" value="lunch">
                        <span>Lunch</span>
                    </label>
                    <label class="meal-checkbox">
                        <input wire:model.live="mealTypes" type="checkbox" value="supper">
                        <span>Supper</span>
                    </label>
                </div>
                @error('mealTypes') <span class="meal-error">{{ $message }}</span> @enderror
                @error('mealTypes.*') <span class="meal-error">{{ $message }}</span> @enderror
            </label>

            <label class="meal-field">
                <span>Quantity</span>
                <input wire:model.live="quantity" type="number" min="1" class="meal-control">
                @error('quantity') <span class="meal-error">{{ $message }}</span> @enderror
            </label>

            <div class="meal-actions">
                <button type="submit" class="meal-primary">Create Order</button>
                <a href="{{ \App\Filament\Pages\Hostel\MealOrders\TodayMealOrders::getUrl(panel: 'admin') }}" class="meal-cancel">Cancel</a>
            </div>
        </form>
    </div>
</x-filament-panels::page>
