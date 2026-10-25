@php
    $__ok  = fn ($x) => $x && file_exists(public_path($x));
    $__cat = (isset($cat) && is_array($cat)) ? $cat : [];
    $__fam = (isset($f) && is_array($f)) ? $f : [];
    $__fs  = (string) ($solution ?? ($__fam['slug'] ?? ''));
    $__cs  = (string) ($category ?? ($__cat['slug'] ?? ''));

    // ---- Nom de base de la solution (obstacles / hygiene / securite / incendie / domotique) ----
    $__familyNames = [
        'obstacles-physiques-automatises' => 'obstacles',
        'hygiene-agro-alimentaire'        => 'hygiene',
        'securite-electronique'           => 'securite',
        'detection-incendie'              => 'incendie',
        'domotique'                       => 'domotique',
    ];
    $__familyName = $__familyNames[$__fs] ?? null;
    if (!$__familyName && $__fs !== '') {
        foreach (['obstacle' => 'obstacles', 'hygien' => 'hygiene', 'secur' => 'securite', 'incend' => 'incendie', 'domotique' => 'domotique'] as $k => $v) {
            if (str_contains($__fs, $k)) { $__familyName = $v; break; }
        }
    }

    // ---- VIDÉO : catégorie d'abord, puis solution ----
    $__base  = fn ($p) => $p ? pathinfo($p, PATHINFO_FILENAME) : null;
    $__names = array_values(array_unique(array_filter([
        $__base($__cat['video'] ?? null),
        $__cs,
        ($__fs && $__cs) ? "{$__fs}-{$__cs}" : null,
        $__base($__fam['video'] ?? null),
        $__familyName,
        $__fs,
    ])));

    $__videoPath = $__ok($__cat['video'] ?? null) ? $__cat['video'] : null;
    if (!$__videoPath) {
        foreach ($__names as $__n) {
            foreach (['videos/categories', 'videos/solutions', 'videos'] as $__d) {
                if ($__ok("{$__d}/{$__n}.mp4")) { $__videoPath = "{$__d}/{$__n}.mp4"; break 2; }
            }
        }
    }

    // ---- PHOTO DE SECOURS : même nom que la vidéo, dossier images/ en premier ----
    $__vn = $__videoPath ? pathinfo($__videoPath, PATHINFO_FILENAME) : null;
    $__imagePath = null;
    foreach (array_values(array_unique(array_filter([$__vn, $__familyName]))) as $__n) {
        foreach (['images', 'images/categories', 'images/solutions'] as $__d) {
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $__e) {
                if ($__ok("{$__d}/{$__n}.{$__e}")) { $__imagePath = "{$__d}/{$__n}.{$__e}"; break 3; }
            }
        }
    }
    // dernier recours : image de la solution (jamais l'image produit de la catégorie)
    if (!$__imagePath && $__ok($__fam['image'] ?? null)) { $__imagePath = $__fam['image']; }

    // On ignore toute valeur transmise par les pages : le résultat est identique partout
    $scopeVideo = $__videoPath ? asset($__videoPath) : null;
    $scopeImage = $__imagePath ? asset($__imagePath) : null;
@endphp
<!-- hb-video-src: {{ $__videoPath ?: 'AUCUN' }} | hb-photo-src: {{ $__imagePath ?: 'AUCUNE' }} | solution={{ $__fs }} | category={{ $__cs }} -->

@if($scopeVideo || $scopeImage)
    <div class="hb-scope-bg" aria-hidden="true"
         @if($scopeImage) style="background-image:url('{{ $scopeImage }}')" @endif>
        @if($scopeVideo)
            <video class="hb-scope-video" autoplay muted loop playsinline preload="auto" disablepictureinpicture
                   @if($scopeImage) poster="{{ $scopeImage }}" @endif>
                <source src="{{ $scopeVideo }}" type="video/mp4">
            </video>
        @endif
    </div>
@endif

<style>
    .hb-shop-page {
        position:relative; isolation:isolate; background:#080807; color:#f5f1e8;
        font-family:Arial,Helvetica,sans-serif;
        --g:#d6ad55; --gl:#f2d58f; --border:rgba(214,173,85,.30); --muted:#b9b4a8;
        --card:rgba(12,11,9,.82);
        --tile:radial-gradient(ellipse at 50% 58%, rgba(214,173,85,.12) 0%, transparent 68%);
    }

    /* Fond : la photo reste toujours dessous, la vidéo apparaît par-dessus quand elle joue */
    .hb-scope-bg { position:fixed; inset:0; z-index:-1; background-color:#080807; background-size:cover; background-position:center; background-repeat:no-repeat; }
    .hb-scope-bg::after { content:""; position:absolute; inset:0; pointer-events:none;
        background:
            radial-gradient(ellipse at 50% 30%, rgba(8,8,7,0) 0%, rgba(8,8,7,.5) 100%),
            linear-gradient(180deg, rgba(8,8,7,.55) 0%, rgba(8,8,7,.78) 100%); }
    .hb-scope-video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:0; transition:opacity .8s ease; }
    .hb-scope-video.is-on { opacity:1; }

    .hb-shop-sec { position:relative; padding:70px 0 80px; }

    /* ===== Carte produit ===== */
    .hb-shop-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:18px; }
    .hb-shop-card { position:relative; display:flex; flex-direction:column; min-width:0; overflow:hidden; color:inherit; text-decoration:none;
        border:1px solid var(--border); background:transparent;
        transition:transform .3s ease, border-color .3s, box-shadow .3s; }
    .hb-shop-card[hidden] { display:none; }
    .hb-shop-card::before { content:""; position:absolute; z-index:3; top:0; left:0; right:0; height:2px; pointer-events:none;
        background:linear-gradient(90deg,transparent,var(--gl),transparent); transform:scaleX(0); transition:transform .5s cubic-bezier(.2,.7,.2,1); }
    a.hb-shop-card:hover, a.hb-shop-card:focus-visible { transform:translateY(-6px); border-color:var(--g);
        box-shadow:0 26px 50px -18px rgba(0,0,0,.8), 0 0 34px -12px rgba(214,173,85,.45); }
    a.hb-shop-card:hover::before { transform:scaleX(1); }

    .hb-shop-pic { position:relative; display:flex; align-items:center; justify-content:center; aspect-ratio:1/1; padding:10px; background:var(--tile); }
    .hb-shop-pic img { display:block; width:100%; height:100%; object-fit:contain;
        filter:drop-shadow(0 14px 18px rgba(0,0,0,.55));
        transition:transform .5s cubic-bezier(.2,.7,.2,1); }
    a.hb-shop-card:hover .hb-shop-pic img { transform:scale(1.08); }
    .hb-shop-noimg { padding:14px; text-align:center; color:#cfc6b0; font-family:Georgia,serif; font-size:14px; }
    .hb-shop-badge { position:absolute; top:10px; left:10px; padding:4px 9px; background:rgba(8,8,7,.8); border:1px solid var(--border);
        color:var(--gl); font-size:9px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; }

    .hb-shop-body { flex:1; display:flex; flex-direction:column; padding:14px 16px 16px; border-top:1px solid var(--border);
        background:linear-gradient(180deg, rgba(8,8,7,.45), rgba(8,8,7,.82)); }
    .hb-shop-body h3 { margin:0; font-family:Georgia,serif; font-size:15px; font-weight:400; line-height:1.4; color:#f6efdd;
        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:2.8em; }
    .hb-shop-body p { margin:8px 0 0; color:var(--muted); font-size:11px; line-height:1.6; min-height:3.2em;
        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .hb-shop-btn { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:auto; padding:10px 12px;
        border:1px solid var(--g); color:var(--gl); font-size:11px; font-weight:700; letter-spacing:.7px; text-transform:uppercase; transition:background .25s, color .25s; }
    .hb-shop-body > :last-child { margin-top:14px; }
    a.hb-shop-card:hover .hb-shop-btn { background:var(--g); color:#15130d; }
    .hb-shop-btn svg { transition:transform .25s; }
    a.hb-shop-card:hover .hb-shop-btn svg { transform:translateX(4px); }

    /* ===== Cartes de catégories (page solution) ===== */
    .hb-shop-cats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:18px; }
    .hb-shop-cat { position:relative; display:block; aspect-ratio:4/3; overflow:hidden; color:#fffaf0; text-decoration:none;
        border:1px solid var(--border); background:#14130f; transition:transform .3s, border-color .3s, box-shadow .3s; }
    .hb-shop-cat:hover { transform:translateY(-6px); border-color:var(--g); box-shadow:0 26px 50px -18px rgba(0,0,0,.8), 0 0 34px -12px rgba(214,173,85,.45); }
    .hb-shop-cat img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transition:transform .7s cubic-bezier(.2,.7,.2,1); }
    .hb-shop-cat:hover img { transform:scale(1.06); }
    .hb-shop-cat::after { content:""; position:absolute; inset:0; background:linear-gradient(180deg, rgba(5,5,4,.05) 30%, rgba(5,5,4,.92) 100%); }
    .hb-shop-cat-info { position:absolute; z-index:1; left:0; right:0; bottom:0; padding:18px 20px; }
    .hb-shop-cat-info h3 { margin:0; font-family:Georgia,serif; font-size:21px; font-weight:400; line-height:1.25; }
    .hb-shop-cat-info p { margin:7px 0 0; color:#e0dbcf; font-size:11px; line-height:1.6;
        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .hb-shop-cat-go { display:inline-flex; align-items:center; gap:8px; margin-top:12px; color:var(--gl); font-size:10px; font-weight:700; letter-spacing:1.4px; text-transform:uppercase; }

    @media (max-width:1100px){ .hb-shop-grid { grid-template-columns:repeat(3,minmax(0,1fr)); } }
    @media (max-width:860px){ .hb-shop-cats { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:760px){
        .hb-shop-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
        .hb-shop-pic { padding:6px; }
        .hb-shop-body { padding:11px 11px 12px; }
        .hb-shop-body h3 { font-size:13px; }
        .hb-shop-body p { display:none; }
        .hb-shop-btn { padding:8px 10px; font-size:10px; }
        .hb-shop-sec { padding:50px 0 60px; }
    }
    @media (max-width:520px){ .hb-shop-cats { grid-template-columns:1fr; } }
    @media (prefers-reduced-motion:reduce){ .hb-shop-card, .hb-shop-pic img, .hb-shop-cat, .hb-shop-cat img, .hb-scope-video { transition:none; } }
</style>

<script>
(function () {
    var v = document.querySelector('.hb-scope-video');
    if (!v) return;
    v.muted = true;

    // La vidéo n'apparaît que lorsqu'elle joue vraiment ; sinon la photo (même nom) reste affichée
    v.addEventListener('playing', function () { v.classList.add('is-on'); });
    v.addEventListener('error', function () { v.remove(); });
    var s = v.querySelector('source');
    if (s) s.addEventListener('error', function () { v.remove(); });

    var play = function () { var p = v.play(); if (p && p.catch) p.catch(function () {}); };
    play();
    document.addEventListener('visibilitychange', function () { if (document.hidden) v.pause(); else play(); });
    ['pointerdown', 'keydown', 'touchstart', 'scroll'].forEach(function (e) {
        window.addEventListener(e, function () { if (v.paused) play(); }, { once: true, passive: true });
    });
})();
</script>