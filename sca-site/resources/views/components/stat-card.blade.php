@props(['label', 'value', 'hint' => null, 'href' => null])

<{{ $href ? 'a href='.$href : 'div' }} class="elevate relative block overflow-hidden p-6">
    <span class="absolute inset-y-0 left-0 w-1 bg-gold"></span>
    <p class="text-[11px] uppercase tracking-[0.2em] text-stone">{{ $label }}</p>
    <p class="mt-3 font-serif text-4xl">{{ $value }}</p>
    @if($hint)<p class="mt-2 text-xs text-stone">{{ $hint }}</p>@endif
</{{ $href ? 'a' : 'div' }}>
