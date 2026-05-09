<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">Room Inventory</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage all hostel rooms</p>
            </div>
            <x-filament::button
                tag="a"
                :href="\App\Filament\Pages\Hostel\Rooms\NewRoom::getUrl(panel: 'admin')"
            >
                + Add Room
            </x-filament::button>
        </div>

        <div class="flex flex-wrap gap-3">
            <select wire:model.live="filterType" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-800 dark:bg-gray-900">
                <option value="">All Types</option>
                @foreach ($this->typeOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterStatus" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-800 dark:bg-gray-900">
                <option value="">All Status</option>
                @foreach ($this->statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterFloor" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-800 dark:bg-gray-900">
                <option value="">All Floors</option>
                @foreach ($this->floorOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        @php
            $visibleRooms = $this->rooms->take(4);
        @endphp

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($visibleRooms as $room)
                @php
                    $typeLabel = match ($room->room_type) {
                        'vip' => 'VIP',
                        'ac' => 'AC',
                        'non_ac' => 'Non-AC',
                        default => $room->room_type,
                    };
                    $editUrl = \App\Filament\Pages\Hostel\Rooms\NewRoom::getUrl(panel: 'admin', parameters: ['record' => $room->id]);
                    $detailUrl = \App\Filament\Pages\Hostel\Rooms\RoomDetail::urlForRoom($room->id);
                    $thumbnailUrl = $room->listingThumbnailUrl();
                @endphp

                <div class="group flex flex-col overflow-hidden rounded-xl border bg-white bg-card dark:border-gray-800 dark:bg-gray-900">
                    <div class="relative">
                        <img
                            src="{{ $thumbnailUrl }}"
                            alt="{{ __(':type room :room', ['type' => $typeLabel, 'room' => $room->room_number]) }}"
                            loading="lazy"
                            width="768"
                            height="512"
                            class="h-44 w-full object-cover"
                        >

                        <div
                            class="absolute right-2 top-2 flex items-center gap-1 opacity-0 transition group-hover:opacity-100 focus-within:opacity-100"
                            aria-label="{{ __('Room management actions') }}"
                        >
                            <a
                                href="{{ $editUrl }}"
                                title="{{ __('Edit') }}"
                                aria-label="{{ __('Edit room') }}"
                                class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg bg-white/95 text-gray-700 shadow-sm ring-1 ring-gray-200 hover:bg-white dark:bg-gray-950/90 dark:text-gray-100 dark:ring-gray-700"
                            >
                                <x-filament::icon icon="heroicon-o-pencil-square" class="size-4" />
                            </a>

                            <button
                                type="button"
                                title="{{ __('Delete') }}"
                                aria-label="{{ __('Delete room') }}"
                                wire:click="deleteRoom({{ $room->id }})"
                                wire:confirm="{{ __('Delete this room?') }}"
                                class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg bg-white/95 text-red-600 shadow-sm ring-1 ring-gray-200 hover:bg-white dark:bg-gray-950/90 dark:text-red-400 dark:ring-gray-700"
                            >
                                <x-filament::icon icon="heroicon-o-trash" class="size-4" />
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col p-4">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="min-w-0 truncate text-lg font-semibold text-[#142d45] text-foreground dark:text-white">
                                Room {{ $room->room_number }}
                            </h3>
                            <span class="shrink-0 rounded-full bg-gray-100 bg-muted px-2 py-0.5 text-xs text-gray-500 text-muted-foreground dark:bg-gray-800 dark:text-gray-300">
                                {{ $typeLabel }}
                            </span>
                        </div>

                        <p class="mt-1 flex items-center gap-1 text-xs text-gray-500 text-muted-foreground dark:text-gray-400">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="12"
                                height="12"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-bed-double shrink-0"
                                aria-hidden="true"
                            >
                                <path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"></path>
                                <path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"></path>
                                <path d="M12 4v6"></path>
                                <path d="M2 18h20"></path>
                            </svg>
                            <span>Capacity {{ $room->capacity }} &bull; Floor {{ $room->floor }}</span>
                        </p>

                        @if (filled($room->description))
                            <p class="mt-2 line-clamp-2 text-xs text-gray-500 text-muted-foreground dark:text-gray-400">
                                {{ $room->description }}
                            </p>
                        @endif

                        <p class="mt-3 text-sm font-semibold text-[#142d45] text-foreground dark:text-white">
                            BDT {{ number_format((float) $room->base_rate, 0) }} / night
                        </p>

                        <a
                            class="mt-4 rounded-lg bg-[#1f4a73] bg-primary py-2 text-center text-sm font-medium text-white text-primary-foreground hover:opacity-90"
                            href="{{ $detailUrl }}"
                        >
                            Room Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-gray-200 bg-white p-6 text-sm text-gray-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 sm:col-span-2 lg:col-span-4">
                    No rooms found.
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
