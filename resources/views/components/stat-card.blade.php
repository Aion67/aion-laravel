@props([
    'label',
    'value',
])

<div class="bg-white shadow-sm sm:rounded-lg p-5 border border-gray-100">
    <p class="text-sm text-gray-500">{{ $label }}</p>
    <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $value }}</p>
</div>
