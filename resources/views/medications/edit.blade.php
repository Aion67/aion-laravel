<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Edit Medication" subtitle="Update medication details" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @include('medications._form', [
                    'action' => route('medications.update', $medication),
                    'method' => 'PUT',
                    'medication' => $medication,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
