<x-filament-panels::page>
    @php
        $users = $this->getUsers();
    @endphp

    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">User Management</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage all system users</p>
            </div>
            <x-filament::button
                tag="a"
                :href="\App\Filament\Pages\Hostel\Users\NewUser::getUrl(panel: 'admin')"
                style="background-color: #173c63; color: #ffffff;"
            >
                Add New User
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
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Email</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Phone</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Role</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Designation</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Office ID</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="px-4 py-3">{{ $user->phone ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded bg-slate-100 px-2 py-1 text-xs dark:bg-slate-800">{{ $user->role ?: '-' }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $user->designation?->name ?: '-' }}</td>
                                <td class="px-4 py-3">{{ $user->cadre_number ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ url('/admin/hostel/user/' . $user->id . '/edit') }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium dark:border-gray-700">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $users->links() }}
        </div>
    </div>
</x-filament-panels::page>
