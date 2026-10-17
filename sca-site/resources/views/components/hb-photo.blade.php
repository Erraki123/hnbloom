@props(['src' => null, 'local' => null, 'fallback' => null, 'alt' => '', 'label' => 'Image à remplacer'])
@php
    $resolved = $src ?: (($local && file_exists(public_path($local))) ? asset($local) : $fallback);
@endphp
@if($resolved)
    <img src="{{ $resolved }}" alt="{{ $alt }}" loading="lazy" decoding="async" class="hb-photo"
         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
@endif
<div class="hb-placeholder" @if($resolved) style="display:none" @endif>
    <x-icon name="image" :size="25" :stroke="1.2" />
    <span>{{ $label }}</span>
</div>
