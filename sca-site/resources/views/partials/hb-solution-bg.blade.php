@php
    $vidPath = $f['video'] ?? null;
    $imgPath = $f['image'] ?? null;

    $video  = ($vidPath && file_exists(public_path($vidPath))) ? asset($vidPath) : null;
    $hasImg = $imgPath && file_exists(public_path($imgPath));
    $poster = $hasImg ? asset($imgPath) : ($f['fallback'] ?? null);
@endphp

<div class="hb-sol-bg" aria-hidden="true" @if($poster) style="background-image:url('{{ $poster }}')" @endif>
    @if($video)
        <video autoplay muted loop playsinline preload="auto" disablepictureinpicture
               @if($poster) poster="{{ $poster }}" @endif>
            <source src="{{ $video }}" type="video/mp4">
        </video>
    @endif
</div>

<style>
    .hb-sol-bg { position: fixed; inset: 0; z-index: -1; background: #090908 center / cover no-repeat; overflow: hidden; }
    .hb-sol-bg video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .hb-sol-bg::after { content: ""; position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(9,9,8,.5) 0%, rgba(9,9,8,.8) 100%); }
    @media (prefers-reduced-motion: reduce) { .hb-sol-bg video { display: none; } }
</style>
