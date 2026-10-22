@props(['image' => null, 'video' => null, 'fallback' => null])
@php
    $cfg = config('hnbloom');

    $bg = collect([$image, $cfg['images']['solutions'] ?? null, $cfg['images']['hero'] ?? null])
        ->first(fn ($p) => $p && file_exists(public_path($p)));

    $poster = $bg ? asset($bg) : $fallback;
    $vid    = ($video && file_exists(public_path($video))) ? asset($video) : null;
@endphp

<div class="hb-catalog-bg" aria-hidden="true" @if($poster) style="background-image:url('{{ $poster }}')" @endif>
    @if($vid)
        <video class="hb-catalog-bg-video" autoplay muted loop playsinline preload="auto" disablepictureinpicture
               @if($poster) poster="{{ $poster }}" @endif>
            <source src="{{ $vid }}" type="video/mp4">
        </video>
    @endif
</div>

<style>
    .hb-catalog-bg { overflow: hidden; }
    .hb-catalog-bg-video {
        position: absolute; inset: 0;
        width: 100%; height: 100%;
        object-fit: cover;
    }
    @media (prefers-reduced-motion: reduce) { .hb-catalog-bg-video { display: none; } }
</style>
