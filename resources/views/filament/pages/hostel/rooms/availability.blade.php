<x-filament-panels::page>
    <style>
        .availability-card {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 32px 28px 28px;
        }

        .availability-form {
            display: flex;
            align-items: end;
            gap: 18px;
        }

        .availability-field {
            display: flex;
            width: 172px;
            flex-direction: column;
            gap: 8px;
            color: #001b33;
            font-size: 16px;
            font-weight: 500;
        }

        .availability-input {
            width: 100%;
            height: 44px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
            padding: 8px 16px;
            color: #001b33;
            font-size: 16px;
            outline: none;
        }

        .availability-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: auto;
            height: 40px;
            border-radius: 8px;
            background: #173c63;
            padding: 0 20px;
            color: #ffffff;
            font-size: 16px;
            font-weight: 500;
            white-space: nowrap;
        }

        @media (max-width: 767px) {
            .availability-card {
                padding: 24px 18px;
            }

            .availability-form {
                align-items: stretch;
                flex-direction: column;
            }

            .availability-field,
            .availability-button {
                width: 100%;
            }
        }

        .dark .availability-card {
            border-color: #334155;
            background: #111827;
        }

        .dark .availability-field {
            color: #ffffff;
        }

        .dark .availability-input {
            border-color: #475569;
            background-color: #1f2937;
            color: #ffffff;
        }
    </style>

    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">Room Availability</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Check available rooms for your dates</p>
        </div>

        <div class="availability-card">
            <form wire:submit="checkAvailability" class="availability-form">
                <label class="availability-field">
                    <span>Check-in Date</span>
                    <input wire:model="checkInDate" type="date" class="availability-input">
                    @error('checkInDate') <span class="block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>

                <label class="availability-field">
                    <span>Check-out Date</span>
                    <input wire:model="checkOutDate" type="date" class="availability-input">
                    @error('checkOutDate') <span class="block text-xs text-red-600">{{ $message }}</span> @enderror
                </label>

                <button class="availability-button">
                    <x-filament::icon icon="heroicon-o-magnifying-glass" class="size-5" />
                    Check Availability
                </button>
            </form>
        </div>

        @if ($searched)
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-950/40">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs text-gray-500">Room</th>
                                <th class="px-4 py-3 text-left text-xs text-gray-500">Type</th>
                                <th class="px-4 py-3 text-left text-xs text-gray-500">Price</th>
                                <th class="px-4 py-3 text-left text-xs text-gray-500">Capacity</th>
                                <th class="px-4 py-3 text-left text-xs text-gray-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($this->availableRooms as $room)
                                <tr>
                                    <td class="px-4 py-3 font-medium">Room {{ $room->room_number }}</td>
                                    <td class="px-4 py-3">{{ $this->roomTypeLabel($room->room_type) }}</td>
                                    <td class="px-4 py-3">BDT {{ number_format((float) $room->base_rate, 0) }}/night</td>
                                    <td class="px-4 py-3">{{ $room->capacity }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($room->status) }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ $this->bookNowUrl($room) }}" class="inline-flex rounded-lg px-3 py-1.5 text-xs font-medium text-white" style="background-color: #173c63;">
                                            Book Now
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No available rooms found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
