@props([
    'title',
    'subtitle' => null,
    'chartId',
    'chartConfig',
    'height' => 280,
])

<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex items-start justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
            @if ($subtitle)
                <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>

        <button
            type="button"
            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            data-report-chart-download="{{ $chartId }}"
        >
            Download PNG
        </button>
    </div>

    <div class="p-6">
        <div class="w-full" style="height: {{ $height }}px;">
            <canvas id="{{ $chartId }}" data-report-chart='@json($chartConfig)' class="h-full w-full"></canvas>
        </div>
    </div>
</div>