<x-filament-panels::page>
    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">Booking Calendar</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Monthly booking occupancy snapshot</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-3 flex items-center justify-between">
                <button class="rounded border px-3 py-1 text-sm">Previous</button>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">May 2026</p>
                <button class="rounded border px-3 py-1 text-sm">Next</button>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center text-xs">
                @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                    <div class="rounded bg-gray-100 p-2 font-medium dark:bg-gray-800">{{ $day }}</div>
                @endforeach
                @for ($i = 1; $i <= 31; $i++)
                    <div class="min-h-16 rounded border border-gray-100 p-2 text-left dark:border-gray-800">
                        <p class="text-[10px] text-gray-500">{{ $i }}</p>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</x-filament-panels::page>
