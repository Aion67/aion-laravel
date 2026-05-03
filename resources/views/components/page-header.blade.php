@props([
    'title',
    'subtitle' => null,
])

<div>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>
    @if ($subtitle)
        <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
    @endif
</div>
