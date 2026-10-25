@php
    $projects = $projects ?? config('portfolio.projects');

    // Photo de fond : public/images/projects.jpg (sinon hero.jpg, sinon about.jpg)
    $bgPath = collect([
        config('hnbloom.images.projects', 'images/projects.jpg'),
        config('hnbloom.images.hero'),
        config('hnbloom.images.about'),
    ])->first(fn ($p) => $p && file_exists(public_path($p)));
    $cover = $bgPath ? asset($bgPath) : null;

    // Vidéo du hero (optionnelle)
    $videoPath = config('hnbloom.projects_video');
    $heroVideo = ($videoPath && file_exists(public_path($videoPath))) ? asset($videoPath) : null;

    // Logos pour les bandes défilantes (uniquement les projets qui ont une image)
    $marquee = collect($projects)->filter(function ($p) {
        $t = $p['thumb'] ?? null;
        return $t && file_exists(public_path($t));
    })->values();

    // Deux rangées : la 2e démarre à un autre endroit de la liste
    $half = intdiv($marquee->count(), 2);
    $rows = [
        $marquee,
        $marquee->slice($half)->concat($marquee->slice(0, $half))->values(),
    ];

    // Logos plus grands = il en faut moins pour remplir la largeur
    $reps = $marquee->count() ? max(1, (int) ceil(9 / $marquee->count())) : 1;
    $marqueeDur = max(30, $marquee->count() * $reps * 4);
@endphp

<x-hb-layout title="Nos projets" description="Les projets réalisés par HN BLOOM : industrie, événementiel, sport, tertiaire et institutions.">

<div class="hb-pl-page">

    {{-- Barre de progression dorée --}}
    <div class="hb-pl-progress" aria-hidden="true"><span></span></div>

    {{-- Fond fixe (compatible mobile) --}}
    @if($cover)
        <div class="hb-pl-fixed-bg" style="background-image:url('{{ $cover }}')" aria-hidden="true"></div>
    @endif

    {{-- HERO (plein écran) --}}
    <section class="hb-pl-hero">
        @if($cover || $heroVideo)
            <div class="hb-pl-hero-media" aria-hidden="true">
                @if($cover)
                    <div class="hb-pl-hero-bg" style="background-image:url('{{ $cover }}')"></div>
                @endif

                @if($heroVideo)
                    <video class="hb-pl-hero-video" autoplay muted loop playsinline preload="auto" disablepictureinpicture>
                        <source src="{{ $heroVideo }}" type="video/mp4">
                    </video>
                @endif
            </div>
        @endif

        <div class="hb-wrap hb-pl-hero-inner">
            <nav class="hb-pl-crumbs hb-pl-in" style="--i:0" aria-label="Fil d’Ariane">
                <a href="{{ route('home') }}">Accueil</a><span>/</span>
                <b>Projets</b>
            </nav>
            <p class="hb-eyebrow hb-pl-in" style="--i:1"><i class="hb-pl-eyebrow-line"></i>NOS PROJETS</p>
            <h1 class="hb-pl-in" style="--i:2">Réalisations<br><span>et références</span></h1>
            <p class="hb-pl-lead hb-pl-in" style="--i:3">Industries, institutions, événements, sport et tertiaire : des sites très différents, un même niveau d’exigence.</p>
        </div>

        {{-- BANDES DÉFILANTES : rangée 1 en haut, rangée 2 en bas --}}
        @if($marquee->count())
            @foreach($rows as $ri => $row)
                <div class="hb-pl-marquee {{ $ri ? 'hb-pl-marquee-bottom' : 'hb-pl-marquee-top' }} hb-pl-in"
                     style="--i:{{ $ri ? 5 : 0 }}"
                     @if($ri) aria-label="Quelques références" @else aria-hidden="true" @endif>

                    @if($ri)
                        <div class="hb-pl-marquee-label">
                            <i></i><span>ILS NOUS FONT CONFIANCE</span>
                        </div>
                    @endif

                    <div class="hb-pl-marquee-viewport {{ $ri ? 'is-reverse' : '' }}">
                        <div class="hb-pl-marquee-track" style="--dur:{{ $marqueeDur + ($ri ? 6 : 0) }}s">
                            @foreach([0, 1] as $copy)
                                <div class="hb-pl-marquee-set" @if($copy) aria-hidden="true" @endif>
                                    @for($r = 0; $r < $reps; $r++)
                                        @foreach($row as $p)
                                            <a href="{{ route('projects.show', $p['slug']) }}"
                                               class="hb-pl-logo"
                                               title="{{ $p['title'] }}"
                                               @if(!$ri || $copy || $r) tabindex="-1" @endif>
                                                <img src="{{ asset($p['thumb']) }}" alt="{{ $ri && !$copy && !$r ? $p['title'] : '' }}" loading="lazy" decoding="async">
                                            </a>
                                        @endforeach
                                    @endfor
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </section>

    {{-- GRILLE --}}
    <section class="hb-pl-section" id="plSection">
        <div class="hb-wrap">

            <div class="hb-pl-toolbar hb-pl-rv" style="--d:0s">
                <label class="hb-pl-search">
                    <x-icon name="search" :size="16" />
                    <input type="search" id="plSearch" placeholder="Rechercher un projet…" autocomplete="off" aria-label="Rechercher un projet">
                </label>
                <span class="hb-pl-results" id="plResults" aria-live="polite"></span>
            </div>

            <div class="hb-pl-grid" id="plGrid">
                @foreach($projects as $i => $p)
                    @php
                        $thumb = $p['thumb'] ?? null;
                        $hasThumb = $thumb && file_exists(public_path($thumb));
                    @endphp
                    <a href="{{ route('projects.show', $p['slug']) }}"
                       class="hb-pl-card"
                       data-title="{{ \Illuminate\Support\Str::lower($p['title']) }}"
                       style="--d:{{ ($i % 3) * 130 }}ms">
                        <i class="hb-pl-line" aria-hidden="true"></i>
                        <i class="hb-pl-halo" aria-hidden="true"></i>
                        <span class="hb-pl-thumb">
                            @if($hasThumb)
                                <img src="{{ asset($thumb) }}" alt="{{ $p['title'] }}" loading="lazy" decoding="async">
                            @else
                                <span class="hb-pl-noimg">{{ $p['title'] }}</span>
                            @endif
                            <span class="hb-pl-shine" aria-hidden="true"></span>
                        </span>
                        <span class="hb-pl-info">
                            <small>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</small>
                            <strong>{{ $p['title'] }}</strong>
                            <x-icon name="arrow-up-right" :size="16" />
                        </span>
                    </a>
                @endforeach
            </div>

            <p class="hb-pl-empty" id="plEmpty" hidden>Aucun projet ne correspond à votre recherche.</p>
        </div>
    </section>

</div>{{-- /hb-pl-page --}}

    @include('partials.hb-cta', ['heading' => 'Engagez-vous dans l’Excellence avec HN BLOOM'])

    <style>
        /* ====== PAGE ====== */
        .hb-pl-page { position:relative; background:#090908; isolation:isolate; font-family:Arial,Helvetica,sans-serif; }

        .hb-pl-fixed-bg { position:fixed; inset:0; z-index:-2; background-size:cover; background-position:center; }
        .hb-pl-page::before { content:""; position:fixed; inset:0; z-index:-1; background:linear-gradient(180deg, rgba(9,9,8,.78) 0%, rgba(9,9,8,.92) 100%); }

        .hb-pl-hero, .hb-pl-section { --g:#d8b45e; --gl:#f0d69a; --muted:#b1ada3; --border:rgba(216,180,94,.25); color:#f6f1e7; }

        /* ====== HERO : PLEIN ÉCRAN ====== */
        .hb-pl-hero { position:relative; overflow:hidden; min-height:100vh; min-height:100svh; display:flex; flex-direction:column; border-bottom:1px solid var(--border); }
        .hb-pl-hero-media { position:absolute; inset:0; }
        .hb-pl-hero-bg { position:absolute; inset:0; background-size:cover; background-position:center; }
        .hb-pl-hero-video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center; display:block; opacity:0; transition:opacity .9s ease; }
        .hb-pl-hero-video.is-on { opacity:1; }
        .hb-pl-hero::after { content:""; position:absolute; inset:0; pointer-events:none; background:
            radial-gradient(ellipse at 50% 45%, rgba(5,6,5,0) 0%, rgba(5,6,5,.35) 60%, rgba(5,6,5,.85) 100%),
            linear-gradient(90deg, rgba(5,6,5,.75) 0%, rgba(5,6,5,.35) 50%, rgba(5,6,5,.2) 100%),
            linear-gradient(0deg, rgba(5,6,5,.9) 0%, rgba(5,6,5,0) 45%),
            linear-gradient(180deg, rgba(5,6,5,.55) 0%, rgba(5,6,5,0) 25%); }
        .hb-pl-hero-inner { position:relative; z-index:1; width:100%; flex:1; display:flex; flex-direction:column; justify-content:center; padding-top:34px; padding-bottom:34px; }
        .hb-pl-hero-inner > * { align-self:flex-start; }
        .hb-pl-crumbs { display:flex; gap:10px; align-items:center; margin-bottom:28px; font-size:11px; letter-spacing:.4px; color:#d0ccc1; }
        .hb-pl-crumbs a { color:#d0ccc1; text-decoration:none; transition:color .2s; }
        .hb-pl-crumbs a:hover { color:var(--gl); }
        .hb-pl-crumbs b { color:var(--gl); font-weight:400; }
        .hb-pl-crumbs span { opacity:.5; }
        .hb-pl-hero .hb-eyebrow { display:flex; align-items:center; gap:14px; margin:0 0 18px; color:var(--gl); font:700 10.5px/1.5 Arial,Helvetica,sans-serif; letter-spacing:4px; text-transform:uppercase; }
        .hb-pl-eyebrow-line { display:inline-block; flex:0 0 40px; height:1px; background:var(--g); transform-origin:left; animation:hbPlRule 1s .6s ease-out both; }
        .hb-pl-hero h1 { max-width:880px; margin:0; font-family:Georgia,"Times New Roman",serif; font-size:clamp(32px,3.7vw,52px); font-weight:400; line-height:1.15; letter-spacing:-.6px; color:#fffdf7; text-shadow:0 2px 24px rgba(0,0,0,.4); }
        .hb-pl-hero h1 span { display:block; color:var(--gl); font-style:italic; }
        .hb-pl-lead { max-width:520px; margin:20px 0 0; color:#e8e4db; font:400 13.5px/1.9 Arial,Helvetica,sans-serif; text-shadow:0 1px 12px rgba(0,0,0,.5); }

        /* ====== BANDES DÉFILANTES DES LOGOS (haut + bas) ====== */
        .hb-pl-marquee { --lh:clamp(70px, 13.5vh, 104px); position:relative; z-index:2; flex:none; }
        .hb-pl-marquee-top { order:-1; }
        .hb-pl-marquee-bottom { padding:0 0 20px; }
        .hb-pl-marquee-label { display:flex; align-items:center; gap:14px; margin:0 0 12px; padding:0 max(5%, calc((100vw - 1200px) / 2)); color:var(--gl); font:700 9.5px/1.5 Arial,Helvetica,sans-serif; letter-spacing:3.5px; }
        .hb-pl-marquee-label i { display:block; flex:0 0 38px; height:1px; background:var(--g); }

        .hb-pl-marquee-viewport {
            overflow:hidden;
            border-top:1px solid rgba(216,180,94,.22);
            border-bottom:1px solid rgba(216,180,94,.22);
            background:rgba(9,9,8,.55);
            -webkit-mask-image:linear-gradient(90deg, transparent 0%, #000 7%, #000 93%, transparent 100%);
                    mask-image:linear-gradient(90deg, transparent 0%, #000 7%, #000 93%, transparent 100%);
        }
        .hb-pl-marquee-track { display:flex; width:max-content; animation:hbPlMarquee var(--dur,40s) linear infinite; will-change:transform; }
        .hb-pl-marquee-viewport.is-reverse .hb-pl-marquee-track { animation-direction:reverse; }
        .hb-pl-marquee:hover .hb-pl-marquee-track { animation-play-state:paused; }
        .hb-pl-marquee-set { display:flex; flex:none; }

        /* Logos : grands, fond sombre, taille adaptée à la hauteur de l'écran */
        .hb-pl-logo { display:flex; align-items:center; justify-content:center; flex:none; width:calc(var(--lh) * 2.3); height:var(--lh); margin:8px 10px; padding:10px 20px; border:1px solid rgba(216,180,94,.28); background:rgba(16,16,14,.6); text-decoration:none; transition:border-color .3s, background .3s, transform .35s; }
        .hb-pl-logo img { display:block; width:100%; height:100%; object-fit:contain; }
        .hb-pl-logo:hover, .hb-pl-logo:focus-visible { border-color:var(--gl); background:rgba(216,180,94,.12); transform:translateY(-3px); }

        /* ====== SECTION ====== */
        .hb-pl-section { position:relative; padding:60px 0 90px; background:rgba(9,9,8,.35); }
        .hb-pl-section::before { content:""; position:absolute; top:0; left:6%; right:6%; height:1px; background:linear-gradient(90deg,transparent,var(--g),transparent); animation:hbPlRule 1.6s .2s ease-out both; }

        .hb-pl-toolbar { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:32px; }
        .hb-pl-search { display:flex; align-items:center; gap:10px; width:min(100%,360px); padding:0 16px; height:46px; border:1px solid var(--border); background:rgba(16,16,14,.85); color:var(--g); transition:border-color .25s, box-shadow .25s; }
        .hb-pl-search:focus-within { border-color:var(--g); box-shadow:0 0 0 3px rgba(216,180,94,.15), 0 0 24px -6px rgba(216,180,94,.4); }
        .hb-pl-search input { flex:1; min-width:0; border:0; outline:0; background:transparent; color:#f6f1e7; font:inherit; font-size:14px; }
        .hb-pl-search input::placeholder { color:#8d897f; }
        .hb-pl-results { color:var(--muted); font-size:12px; letter-spacing:.5px; }

        /* Grille */
        .hb-pl-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:24px; }

        /* Cartes */
        .hb-pl-card {
            position:relative; display:flex; flex-direction:column; overflow:hidden;
            border:1px solid var(--border);
            background:linear-gradient(160deg, rgba(22,22,19,.72), rgba(12,12,10,.72));
            color:inherit; text-decoration:none;
            transform:perspective(1100px) rotateX(var(--rx,0deg)) rotateY(var(--ry,0deg));
            transition:transform .35s ease-out, border-color .4s, box-shadow .5s;
        }
        .hb-pl-card:not(.is-visible) { opacity:0; }
        .hb-pl-card.is-visible { animation:hbPlRise 1s var(--d,0ms) cubic-bezier(.2,.7,.2,1) backwards; }
        .hb-pl-card.is-pop { animation:hbPlPop .55s cubic-bezier(.2,.7,.2,1) backwards; }
        .hb-pl-card.is-hidden { display:none; }
        .hb-pl-card:hover { transform:perspective(1100px) translateY(-8px) rotateX(var(--rx,0deg)) rotateY(var(--ry,0deg)); border-color:var(--g); box-shadow:0 30px 60px -20px rgba(0,0,0,.7), 0 0 40px -10px rgba(216,180,94,.35); }
        .hb-pl-card:focus-visible { outline:2px solid var(--g); outline-offset:3px; }

        .hb-pl-halo { position:absolute; inset:0; z-index:2; pointer-events:none; opacity:0; transition:opacity .4s; background:radial-gradient(380px circle at var(--mx,50%) var(--my,50%), rgba(240,214,154,.14), transparent 60%); }
        .hb-pl-card:hover .hb-pl-halo { opacity:1; }
        .hb-pl-line { position:absolute; z-index:3; top:0; left:0; right:0; height:2px; pointer-events:none; background:linear-gradient(90deg,transparent,var(--gl),transparent); transform:scaleX(0); transition:transform .6s cubic-bezier(.2,.7,.2,1); }
        .hb-pl-card:hover .hb-pl-line { transform:scaleX(1); }

        .hb-pl-thumb { position:relative; display:flex; align-items:center; justify-content:center; aspect-ratio:4/3; overflow:hidden; background:radial-gradient(ellipse at 50% 50%, rgba(216,180,94,.08), transparent 70%); }
        .hb-pl-thumb img { display:block; width:100%; height:100%; padding:24px; object-fit:contain; object-position:center; transition:transform .7s cubic-bezier(.2,.7,.2,1); }
        .hb-pl-card.is-visible .hb-pl-thumb img { animation:hbPlImg 1.6s calc(var(--d,0ms) + .15s) cubic-bezier(.2,.7,.2,1) backwards; }
        .hb-pl-card:hover .hb-pl-thumb img { transform:scale(1.08); }
        .hb-pl-shine { position:absolute; z-index:1; top:0; bottom:0; left:0; width:45%; pointer-events:none; background:linear-gradient(100deg,transparent,rgba(255,244,214,.2),transparent); transform:translateX(-170%) skewX(-18deg); }
        .hb-pl-card:hover .hb-pl-shine { transform:translateX(330%) skewX(-18deg); transition:transform 1s ease; }
        .hb-pl-noimg { display:flex; align-items:center; justify-content:center; width:100%; height:100%; padding:20px; text-align:center; color:#f1ece0; font-family:Georgia,serif; font-size:15px; }

        .hb-pl-info { display:flex; align-items:center; gap:14px; padding:18px 20px; border-top:1px solid var(--border); transition:background .4s; }
        .hb-pl-card:hover .hb-pl-info { background:rgba(216,180,94,.07); }
        .hb-pl-info small { color:var(--g); font-family:Georgia,serif; font-size:14px; transition:transform .4s, color .3s; }
        .hb-pl-card:hover .hb-pl-info small { transform:translateX(4px); color:var(--gl); }
        .hb-pl-info strong { flex:1; font-family:Georgia,serif; font-size:16px; font-weight:400; line-height:1.35; color:#f1ece0; transition:color .3s; }
        .hb-pl-card:hover .hb-pl-info strong { color:#fff1cf; }
        .hb-pl-info svg { flex:none; color:var(--g); transition:transform .35s; }
        .hb-pl-card:hover .hb-pl-info svg { transform:translate(4px,-4px); }

        .hb-pl-empty { padding:60px 0; text-align:center; color:var(--muted); font-size:15px; }

        /* ====== ANIMATIONS (opacity / transform) ====== */
        html { scroll-behavior:smooth; }
        @keyframes hbPlRule    { from { transform:scaleX(0); } to { transform:scaleX(1); } }
        @keyframes hbPlUp      { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:none; } }
        @keyframes hbPlRise    { from { opacity:0; transform:translateY(50px) scale(.97); } to { opacity:1; transform:none; } }
        @keyframes hbPlPop     { from { opacity:0; transform:scale(.94); } to { opacity:1; transform:none; } }
        @keyframes hbPlImg     { from { transform:scale(1.2); } to { transform:scale(1); } }
        @keyframes hbPlMarquee { from { transform:translate3d(0,0,0); } to { transform:translate3d(-50%,0,0); } }

        .hb-pl-in { animation:hbPlUp 1s calc(.15s + var(--i,0) * .16s) cubic-bezier(.2,.7,.2,1) backwards; }

        .hb-pl-rv:not(.is-in) { opacity:0; }
        .hb-pl-rv.is-in { animation:hbPlUp .9s var(--d,0s) cubic-bezier(.2,.7,.2,1) backwards; }

        .hb-pl-progress { position:fixed; z-index:9999; top:0; left:0; right:0; height:2px; pointer-events:none; }
        .hb-pl-progress span { display:block; width:100%; height:100%; background:linear-gradient(90deg,#d8b45e,#f0d69a); transform:scaleX(0); transform-origin:left; }

        /* ====== RESPONSIVE ====== */
        @media (max-width:980px){ .hb-pl-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (max-width:600px){
            .hb-pl-hero-inner { padding-top:28px; padding-bottom:28px; }
            .hb-pl-grid { grid-template-columns:1fr; gap:16px; }
            .hb-pl-card { transform:none !important; }
            .hb-pl-toolbar { flex-direction:column; align-items:stretch; }
            .hb-pl-search { width:100%; }
            .hb-pl-marquee { --lh:56px; }
            .hb-pl-marquee-bottom { padding-bottom:14px; }
            .hb-pl-marquee-label { font-size:8.5px; letter-spacing:2.5px; margin-bottom:10px; }
            .hb-pl-logo { width:180px; height:90px; margin:8px 6px; padding:10px 14px; }
        }

        /* ====== ACCESSIBILITÉ : les bandes restent défilables à la main ====== */
        @media (prefers-reduced-motion:reduce){
            .hb-pl-card:not(.is-visible), .hb-pl-rv:not(.is-in) { opacity:1; }
            .hb-pl-card, .hb-pl-card *, .hb-pl-in, .hb-pl-rv, .hb-pl-eyebrow-line, .hb-pl-section::before { animation:none !important; }
            .hb-pl-card { transform:none !important; }
            .hb-pl-marquee-track { animation:none; }
            .hb-pl-marquee-viewport { overflow-x:auto; }
            html { scroll-behavior:auto; }
        }
    </style>
    <noscript><style>.hb-pl-card:not(.is-visible), .hb-pl-rv:not(.is-in){opacity:1 !important}</style></noscript>

    <script>
        (function () {
            var reduce = window.matchMedia('(prefers-reduced-motion:reduce)').matches;

            /* ---------- Vidéo hero : lecture directe, pause hors écran ---------- */
            var v = document.querySelector('.hb-pl-hero-video');
            if (v) {
                v.muted = true;
                v.addEventListener('playing', function () { v.classList.add('is-on'); });
                v.addEventListener('error', function () { v.remove(); });
                var src = v.querySelector('source');
                if (src) src.addEventListener('error', function () { v.remove(); });
                var tryPlay = function () { var p = v.play(); if (p && p.catch) p.catch(function () {}); };
                tryPlay();
                if ('IntersectionObserver' in window) {
                    new IntersectionObserver(function (entries) {
                        entries.forEach(function (e) { if (e.isIntersecting) tryPlay(); else v.pause(); });
                    }, { threshold: 0.05 }).observe(v);
                }
                ['pointerdown', 'keydown', 'touchstart', 'scroll'].forEach(function (evt) {
                    window.addEventListener(evt, function () { if (v.paused) tryPlay(); }, { once: true, passive: true });
                });
            }

            /* ---------- Bandes défilantes : pause quand elles ne sont plus visibles ---------- */
            var tracks = document.querySelectorAll('.hb-pl-marquee-track');
            if (tracks.length && 'IntersectionObserver' in window && !reduce) {
                var mio = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        e.target.style.animationPlayState = e.isIntersecting ? 'running' : 'paused';
                    });
                }, { threshold: 0 });
                tracks.forEach(function (t) { mio.observe(t); });
            }

            /* ---------- Apparition des cartes et de la barre d'outils ---------- */
            var cards = Array.prototype.slice.call(document.querySelectorAll('.hb-pl-card'));
            var rv = Array.prototype.slice.call(document.querySelectorAll('.hb-pl-rv'));
            if ('IntersectionObserver' in window && !reduce) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) {
                            e.target.classList.add(e.target.classList.contains('hb-pl-card') ? 'is-visible' : 'is-in');
                            io.unobserve(e.target);
                        }
                    });
                }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
                cards.forEach(function (c) { io.observe(c); });
                rv.forEach(function (r) { io.observe(r); });
            } else {
                cards.forEach(function (c) { c.classList.add('is-visible'); });
                rv.forEach(function (r) { r.classList.add('is-in'); });
            }

            /* ---------- Barre de progression ---------- */
            var bar = document.querySelector('.hb-pl-progress span');
            if (bar && !reduce) {
                var ticking = false;
                var upd = function () {
                    var h = document.documentElement.scrollHeight - window.innerHeight;
                    bar.style.transform = 'scaleX(' + (h > 0 ? Math.min(window.scrollY / h, 1) : 0) + ')';
                    ticking = false;
                };
                window.addEventListener('scroll', function () {
                    if (!ticking) { ticking = true; requestAnimationFrame(upd); }
                }, { passive: true });
                upd();
            }

            /* ---------- Inclinaison 3D + halo doré (souris uniquement) ---------- */
            if (!reduce && window.matchMedia('(hover:hover) and (pointer:fine)').matches) {
                cards.forEach(function (card) {
                    var raf = 0, ev = null;
                    var apply = function () {
                        raf = 0;
                        var r = card.getBoundingClientRect();
                        var x = (ev.clientX - r.left) / r.width;
                        var y = (ev.clientY - r.top) / r.height;
                        card.style.setProperty('--mx', (x * 100) + '%');
                        card.style.setProperty('--my', (y * 100) + '%');
                        card.style.setProperty('--ry', ((x - .5) * 6) + 'deg');
                        card.style.setProperty('--rx', ((.5 - y) * 6) + 'deg');
                    };
                    card.addEventListener('pointermove', function (e) { ev = e; if (!raf) raf = requestAnimationFrame(apply); }, { passive: true });
                    card.addEventListener('pointerleave', function () {
                        card.style.setProperty('--rx', '0deg');
                        card.style.setProperty('--ry', '0deg');
                    });
                });
            }

            /* ---------- Image cassée : message propre ---------- */
            document.querySelectorAll('.hb-pl-thumb img').forEach(function (img) {
                img.addEventListener('error', function () {
                    var s = document.createElement('span');
                    s.className = 'hb-pl-noimg';
                    s.textContent = img.alt || 'Image bientôt disponible';
                    img.replaceWith(s);
                });
            });

            /* ---------- Recherche instantanée ---------- */
            var input = document.getElementById('plSearch');
            var results = document.getElementById('plResults');
            var empty = document.getElementById('plEmpty');

            function norm(s) {
                return (s || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            }

            function filter() {
                var q = norm(input.value.trim());
                var shown = 0;
                cards.forEach(function (c) {
                    var match = !q || norm(c.dataset.title).indexOf(q) !== -1;
                    var wasHidden = c.classList.contains('is-hidden');
                    c.classList.toggle('is-hidden', !match);
                    if (match) {
                        shown++;
                        c.classList.add('is-visible');
                        if (wasHidden && !reduce) {
                            c.classList.remove('is-pop');
                            void c.offsetWidth;
                            c.classList.add('is-pop');
                        }
                    }
                });
                results.textContent = q ? shown + ' résultat' + (shown > 1 ? 's' : '') : '';
                empty.hidden = shown !== 0;
            }

            if (input) input.addEventListener('input', filter);
        })();
    </script>
</x-hb-layout>