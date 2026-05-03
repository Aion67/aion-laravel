<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Create User" subtitle="Add a new staff account" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @include('users._form', [
                    'action' => route('users.store'),
                    'method' => 'POST',
                    'user' => null,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
