@props([
    'href',
    'label',
])

<a
    href="{{ $href }}"
    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
>
    {{ $label }}
</a>