@props(['subtitle' => null])
<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
    @if($subtitle)
        <p class="max-w-xl text-sm text-stone">{{ $subtitle }}</p>
    @else
        <span></span>
    @endif
    <div class="flex gap-3">{{ $slot }}</div>
</div>
