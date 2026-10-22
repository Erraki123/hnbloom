@php
    $vPath  = $market['video'] ?? null;
    $iPath  = $market['image'] ?? null;
    $video  = ($vPath && file_exists(public_path($vPath))) ? asset($vPath) : null;
    $poster = ($iPath && file_exists(public_path($iPath))) ? asset($iPath) : ($market['fallback'] ?? null);
@endphp

<div class="hb-mkbg" aria-hidden="true" @if($poster) style="background-image:url('{{ $poster }}')" @endif>
    @if($video)
        <video class="hb-mkbg-video" autoplay muted loop playsinline preload="auto" disablepictureinpicture
               @if($poster) poster="{{ $poster }}" @endif>
            <source src="{{ $video }}" type="video/mp4">
        </video>
    @endif
</div>

<script>
    // Force la lecture (certains navigateurs bloquent l'autoplay au premier chargement)
    document.querySelectorAll('.hb-mkbg-video').forEach(function (v) {
        v.muted = true;
        var p = v.play && v.play();
        if (p && p.catch) p.catch(function () {});
    });
</script>

<style>
    /* الغلاف طبقة مستقلة: الخلفية (z-index:-1) تبقى فوق خلفية الصفحة وتحت المحتوى */
    .hb-smart-page { position: relative; isolation: isolate; }

    .hb-mkbg { position:fixed; inset:0; z-index:-1; background:#090908 center / cover no-repeat; overflow:hidden; }
    .hb-mkbg video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
    .hb-mkbg::after { content:""; position:absolute; inset:0;
        background:linear-gradient(180deg, rgba(9,9,8,.5) 0%, rgba(9,9,8,.8) 100%); }
</style>
