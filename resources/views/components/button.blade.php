@props(['variant' => 'primary'])
@php
    $variants = [
        'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
        'secondary' => 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 focus:ring-indigo-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    ];
    $variantClass = $variants[$variant] ?? $variants['primary'];
@endphp
<button {{ $attributes->merge(['type' => 'button', 'class' => "inline-flex justify-center rounded-md border border-transparent py-2 px-4 text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 $variantClass"]) }}>
    {{ $slot }}
</button>
