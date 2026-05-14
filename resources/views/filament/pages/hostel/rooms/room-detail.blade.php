<x-filament-panels::page>
    @php
        $typeLabel = match ($room->room_type) {
            'vip' => 'VIP',
            'ac' => 'AC',
            'non_ac' => 'Non-AC',
            default => $room->room_type,
        };
        $typeBadge = match ($room->room_type) {
            'vip' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-200',
            'ac' => 'bg-sky-100 text-sky-800 dark:bg-sky-500/15 dark:text-sky-200',
            default => 'bg-slate-100 text-slate-700 dark:bg-slate-500/15 dark:text-slate-200',
        };
        $statusLabel = ucfirst($room->status);
        $cadreView = request()->session()->get('cadre_auth') === true && request()->boolean('cadre');
    @endphp

    <div class="lg:grid lg:grid-cols-2 lg:items-start lg:gap-10">
        {{-- Left: image slideshow --}}
        <div class="mb-8 lg:mb-0">
            @if (count($this->imageUrls))
                <div
                    x-data="{ idx: 0, urls: @js($this->imageUrls) }"
                    class="relative overflow-hidden rounded-2xl border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="relative aspect-[4/3] w-full">
                        <template x-for="(url, i) in urls" :key="i">
                            <img
                                x-show="idx === i"
                                x-transition.opacity.duration.200ms
                                :src="url"
                                alt=""
                                class="absolute inset-0 size-full object-cover"
                            />
                        </template>
                    </div>

                    <button
                        type="button"
                        class="absolute left-3 top-1/2 z-10 inline-flex size-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-gray-800 shadow ring-1 ring-gray-200 backdrop-blur hover:bg-white dark:bg-gray-900/90 dark:text-white dark:ring-gray-700"
                        aria-label="Previous photo"
                        x-on:click="idx = (idx - 1 + urls.length) % urls.length"
                    >
                        <x-filament::icon icon="heroicon-o-chevron-left" class="size-5" />
                    </button>
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 z-10 inline-flex size-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-gray-800 shadow ring-1 ring-gray-200 backdrop-blur hover:bg-white dark:bg-gray-900/90 dark:text-white dark:ring-gray-700"
                        aria-label="Next photo"
                        x-on:click="idx = (idx + 1) % urls.length"
                    >
                        <x-filament::icon icon="heroicon-o-chevron-right" class="size-5" />
                    </button>

                    <div class="flex justify-center gap-1.5 pb-3 pt-2">
                        <template x-for="(url, i) in urls" :key="'dot-' + i">
                            <button
                                type="button"
                                class="size-2 rounded-full transition"
                                x-bind:class="idx === i ? 'bg-[#153a57]' : 'bg-gray-300 dark:bg-gray-600'"
                                x-on:click="idx = i"
                            ></button>
                        </template>
                    </div>
                </div>
            @else
                <div class="flex aspect-[4/3] items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-gray-50 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                    No room photos uploaded
                </div>
            @endif
        </div>

        {{-- Right: details --}}
        <div class="space-y-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-gray-950 dark:text-white">Room {{ $room->room_number }}</h2>
                    <p class="mt-2 flex flex-wrap items-center gap-x-1 text-sm text-gray-600 dark:text-gray-300">
                        <span class="inline-flex items-center gap-1">
                            <x-filament::icon icon="heroicon-o-user-group" class="size-4 text-gray-400" />
                            Capacity {{ $room->capacity }}
                        </span>
                        <span class="text-gray-400">•</span>
                        <span>Floor {{ $room->floor }}</span>
                    </p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-medium {{ $typeBadge }}">
                    {{ $typeLabel }}
                </span>
            </div>

            @if ($room->description)
                <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-300">{{ $room->description }}</p>
            @endif

            <div class="text-lg font-bold text-gray-950 dark:text-white">
                BDT {{ number_format((float) $room->base_rate, 0) }}
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">/ night</span>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/40">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</p>
                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $statusLabel }}</p>
            </div>

            <div class="flex flex-wrap gap-3">
                @if (! $cadreView)
                    <x-filament::button
                        tag="a"
                        :href="\App\Filament\Pages\Hostel\Rooms\NewRoom::getUrl(panel: 'admin', parameters: ['record' => $room->id])"
                        style="background-color: #173c63; color: #ffffff;"
                    >
                        Edit room
                    </x-filament::button>
                @endif
                <x-filament::button color="gray" tag="a" :href="$cadreView ? route('cadre.booking') : \App\Filament\Pages\Hostel\Rooms\RoomInventory::getUrl(panel: 'admin')">
                    {{ $cadreView ? 'Back to rooms' : 'Back to inventory' }}
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
