@props(['label' => null, 'id' => null, 'error' => null])
@php
    $id = $id ?? $attributes->get('name');
@endphp
<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif
    <div class="mt-1">
        <select id="{{ $id }}" {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm' . ($error ? ' border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : '')]) }}>
            {{ $slot }}
        </select>
    </div>
    @if($error)
        <p class="mt-2 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
