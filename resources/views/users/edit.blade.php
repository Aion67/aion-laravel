<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Edit User" subtitle="Update staff details and permissions" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @include('users._form', [
                    'action' => route('users.update', $user),
                    'method' => 'PUT',
                    'user' => $user,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
