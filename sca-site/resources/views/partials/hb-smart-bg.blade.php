@php
    $d = config('hnbloom.domotique');
    $file = fn ($p) => ($p && file_exists(public_path($p))) ? asset($p) : null;
    $bgVideo  = $file($d['bg_video'] ?? null);
    $bgPoster = $file($d['bg_poster'] ?? null);
    $bgImage  = $file($d['bg_image'] ?? null) ?: $bgPoster;
@endphp

<div class="hb-smart-bg" aria-hidden="true">
    @if($bgImage)<div class="hb-smart-bg-img" style="background-image:url('{{ $bgImage }}')"></div>@endif
    @if($bgVideo)
        <video autoplay muted loop playsinline preload="auto" disablepictureinpicture
               @if($bgPoster) poster="{{ $bgPoster }}" @endif>
            <source src="{{ $bgVideo }}" type="video/mp4">
        </video>
    @endif
</div>

<style>
    .hb-smart-page { position: relative; isolation: isolate; clip-path: inset(0); background: #090908; color: #f6f1e7; }
    .hb-smart-bg { position: fixed; inset: 0; z-index: -1; }
    .hb-smart-bg-img, .hb-smart-bg video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .hb-smart-bg-img { background-size: cover; background-position: center; }
    .hb-smart-bg::after { content: ""; position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(9,9,8,.62), rgba(9,9,8,.85)); }
</style>
