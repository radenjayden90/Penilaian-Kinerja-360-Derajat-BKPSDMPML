@props(['title', 'subtitle' => null])
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            {{ $title }}
        </h2>
        @if($subtitle)
            <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
        @endif
    </div>
    <div>
        {{ $actions ?? '' }}
    </div>
</div>
