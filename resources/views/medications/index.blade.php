<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Medications" subtitle="Medication catalog and status management" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('medications.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div class="md:col-span-3">
                        <x-input-label for="search" value="Search" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="$search" placeholder="Name or SKU" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All statuses</option>
                            <option value="active" @selected($status === 'active')>Active</option>
                            <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <x-primary-button>Filter</x-primary-button>
                        <a href="{{ route('medications.index') }}">
                            <x-secondary-button type="button">Reset</x-secondary-button>
                        </a>
                    </div>
                </form>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('medications.create') }}">
                    <x-primary-button type="button">New Medication</x-primary-button>
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Unit Price</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($medications as $medication)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $medication->sku }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    <div>{{ $medication->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $medication->strength ?: 'No strength' }} • {{ $medication->unit_type }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ number_format((float) $medication->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <x-status-badge :status="$medication->status" />
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('medications.edit', $medication) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                        <form method="POST" action="{{ route('medications.destroy', $medication) }}" onsubmit="return confirm('Delete this medication?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No medications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $medications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
