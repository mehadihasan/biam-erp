<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Designations') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-950">
    <main class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ __('Designations') }}</h1>
                <p class="text-sm text-gray-500">{{ __('Manage user designations.') }}</p>
            </div>
            <a href="{{ route('designations.create') }}" class="rounded-lg bg-blue-950 px-5 py-2 text-center text-sm font-medium text-white">{{ __('Create Designation') }}</a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">{{ __('Name') }}</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">{{ __('Description') }}</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($designations as $designation)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $designation->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $designation->description ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                                        <a href="{{ route('designations.edit', $designation) }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-center text-xs font-medium">{{ __('Edit') }}</a>
                                        <form method="post" action="{{ route('designations.destroy', $designation) }}" onsubmit="return confirm('{{ __('Delete this designation?') }}')">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="w-full rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 sm:w-auto">{{ __('Delete') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">{{ __('No designations found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $designations->links() }}
        </div>
    </main>
</body>
</html>
