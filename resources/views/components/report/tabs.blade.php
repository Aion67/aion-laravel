@props(['active' => 'overview'])

@php
    $tabs = [
        'overview' => ['label' => 'Overview', 'href' => route('reports.index')],
        'patients' => ['label' => 'Patients', 'href' => route('reports.patients')],
        'sales' => ['label' => 'Sales', 'href' => route('reports.sales')],
        'stock' => ['label' => 'Stock', 'href' => route('reports.stock')],
    ];
@endphp

<div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 p-2">
    <div class="flex flex-wrap gap-2">
        @foreach ($tabs as $key => $tab)
            <a href="{{ $tab['href'] }}" class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold transition {{ $active === $key ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>
</div>