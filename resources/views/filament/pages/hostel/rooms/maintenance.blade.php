<x-filament-panels::page>
    <style>
        .maintenance-form-card {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #ffffff;
            padding: 18px 16px 16px;
        }

        .maintenance-form-label {
            display: block;
            margin-bottom: 8px;
            color: #001b33;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.25;
        }

        .maintenance-form-row {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }

        .maintenance-form-select {
            flex: 1 1 auto;
            min-width: 0;
            height: 38px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
            padding: 7px 16px;
            color: #001b33;
            font-size: 14px;
            outline: none;
        }

        .maintenance-form-confirm,
        .maintenance-form-cancel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            height: 38px;
            border-radius: 8px;
            padding: 0 18px;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
        }

        .maintenance-form-confirm {
            border: 1px solid #173c63;
            background: #173c63;
            color: #ffffff;
        }

        .maintenance-form-cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #001b33;
        }

        @media (max-width: 767px) {
            .maintenance-form-row {
                align-items: stretch;
                flex-direction: column;
            }

            .maintenance-form-select,
            .maintenance-form-confirm,
            .maintenance-form-cancel {
                width: 100%;
            }
        }

        .dark .maintenance-form-card {
            border-color: #334155;
            background: #111827;
        }

        .dark .maintenance-form-label {
            color: #ffffff;
        }

        .dark .maintenance-form-select {
            border-color: #475569;
            background-color: #1f2937;
            color: #ffffff;
        }

        .dark .maintenance-form-cancel {
            border-color: #475569;
            background: #111827;
            color: #ffffff;
        }
    </style>

    <div class="space-y-4" x-data="{ showForm: false }">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">Maintenance</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Rooms currently under maintenance</p>
            </div>
            <button
                type="button"
                x-on:click="showForm = true"
                class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90"
                style="background-color: #173c63; color: #ffffff;"
            >
                Add to Maintenance
            </button>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ __('Please select a room before confirming.') }}
            </div>
        @endif

        <form
            x-show="showForm"
            x-cloak
            method="post"
            action="{{ route('maintenance.store') }}"
            class="maintenance-form-card"
        >
            @csrf

            <div>
                <label class="maintenance-form-label" for="maintenance_room_id">Select Room</label>

                <div class="maintenance-form-row">
                    <select id="maintenance_room_id" name="room_id" class="maintenance-form-select">
                        <option value="">Select...</option>
                        @foreach ($this->availableRooms as $room)
                            <option value="{{ $room->id }}">
                                {{ $room->room_number }} - {{ $this->roomTypeLabel($room->room_type) }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="maintenance-form-confirm">
                        Confirm
                    </button>
                    <button
                        type="button"
                        x-on:click="showForm = false"
                        class="maintenance-form-cancel"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-950/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Room No</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Floor</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Type</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Description</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($this->maintenanceRooms as $room)
                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ $room->room_number }}</td>
                                <td class="px-4 py-3">{{ $room->floor }}</td>
                                <td class="px-4 py-3">{{ $this->roomTypeLabel($room->room_type) }}</td>
                                <td class="px-4 py-3">{{ $room->description ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <form method="post" action="{{ route('maintenance.available', $room) }}">
                                        @csrf
                                        @method('patch')
                                        <button type="submit" class="rounded px-3 py-1.5 text-xs font-medium text-white hover:opacity-90" style="background-color: #173c63; color: #ffffff;">
                                            Mark Available
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    No rooms are currently under maintenance.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
