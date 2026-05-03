<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Users" subtitle="Admin-only staff and role management" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="md:col-span-2">
                        <x-input-label for="search" value="Search" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="$search" placeholder="Name or email" />
                    </div>
                    <div>
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All roles</option>
                            <option value="admin" @selected($role === 'admin')>Admin</option>
                            <option value="pharmacist" @selected($role === 'pharmacist')>Pharmacist</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <x-primary-button>Filter</x-primary-button>
                        <a href="{{ route('users.index') }}">
                            <x-secondary-button type="button">Reset</x-secondary-button>
                        </a>
                    </div>
                </form>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('users.create') }}">
                    <x-primary-button type="button">New User</x-primary-button>
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <x-status-badge :status="$user->role === 'admin' ? 'active' : 'pending'" />
                                    <span class="ml-2 text-gray-600">{{ ucfirst($user->role) }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
