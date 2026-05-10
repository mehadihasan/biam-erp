<x-filament-panels::page>
    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-950 dark:text-white">Add New Room</h2>
        </div>

        <div class="max-w-3xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}

                <div class="flex gap-3">
                    <x-filament::button type="submit" style="background-color: #173c63; color: #ffffff;">
                        {{ $record ? 'Update Room' : 'Create Room' }}
                    </x-filament::button>

                    <x-filament::button
                        color="gray"
                        tag="a"
                        :href="\App\Filament\Pages\Hostel\Rooms\RoomInventory::getUrl(panel: 'admin')"
                    >
                        Cancel
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
