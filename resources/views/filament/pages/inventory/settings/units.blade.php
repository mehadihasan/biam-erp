<x-filament-panels::page>
    @php
        $units = $this->getUnits();
    @endphp

    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">Units</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage inventory units</p>
            </div>
            <x-filament::button
                tag="a"
                :href="\App\Filament\Pages\Inventory\Settings\CreateUnit::getUrl(panel: 'admin')"
                wire:navigate
                style="background-color: #173c63; color: #ffffff;"
            >
                Create Unit
            </x-filament::button>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-950/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Details</th>
                            <th class="px-4 py-3 text-center text-xs text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($units as $unit)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $unit->name }}</td>
                                <td class="px-4 py-3">{{ $unit->details ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap justify-center gap-2">
                                        <a
                                            href="{{ \App\Filament\Pages\Inventory\Settings\EditUnit::urlForUnit($unit->id) }}"
                                            wire:navigate
                                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-center text-xs font-medium dark:border-gray-700"
                                        >
                                            Edit
                                        </a>
                                        <form method="post" action="{{ route('inventory.units.destroy', $unit) }}" onsubmit="return confirm('Delete this unit?')">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-red-700 dark:border-gray-700">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                    No units found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $units->links() }}
        </div>
    </div>
</x-filament-panels::page>
