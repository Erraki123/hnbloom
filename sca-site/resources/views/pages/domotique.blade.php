@php
    $d = config('hnbloom.domotique') ?? [];
    $c = config('hnbloom') ?? [];

    // Premier fichier existant dans la liste (public/...)
    $first = function (array $paths) {
        foreach ($paths as $p) {
            if ($p && file_exists(public_path($p))) return asset($p);
        }
        return null;
    };

    $dmVideo = $first([
        $d['bg_video'] ?? 'videos/domotique.mp4',
        $c['solutions_video'] ?? 'videos/solutions.mp4',
        $c['hero_video'] ?? null,
    ]);
    $dmPoster = $first([$d['bg_poster'] ?? null, $c['hero_poster'] ?? null]);
    $dmBg     = $first([$d['bg_image'] ?? null, $d['image'] ?? null, $c['images']['solutions'] ?? null]) ?: $dmPoster;
    $dmCover  = $dmPoster ?: $dmBg;

    // Valeurs par défaut
    $d['lead'] = $d['lead'] ?? 'Représentant exclusif au Maroc de SMART G4 CONTROL, HN BLOOM étudie et installe des systèmes qui améliorent le confort, la sécurité et l’efficacité énergétique des bâtiments.';
    $d['markets'] = $d['markets'] ?? [
        ['slug' => 'smart-home', 'title' => 'Smart Home', 'category' => 'RÉSIDENTIEL', 'text' => '', 'items' => [], 'fallback' => '', 'icon' => 'home'],
        ['slug' => 'smart-hotel', 'title' => 'Smart Hotel', 'category' => 'HÔTELLERIE', 'text' => '', 'items' => [], 'fallback' => '', 'icon' => 'building'],
        ['slug' => 'smart-hospital', 'title' => 'Smart Hospital', 'category' => 'SANTÉ', 'text' => '', 'items' => [], 'fallback' => '', 'icon' => 'plus'],
    ];
@endphp

<x-hb-layout
    title="Domotique & Smart Building"
    description="HN BLOOM vous accompagne dans vos projets Smart Home, Smart Hotel et Smart Hospital avec les solutions SMART G4 CONTROL au Maroc."
>
<div class="hb-dm-page">

    {{-- ================= HERO ================= --}}
    <section class="hb-dm-hero">
        <div class="hb-dm-hero-media" aria-hidden="true"
             @if($dmBg) style="background-image:url('{{ $dmBg }}')" @endif>
            @if($dmVideo)
                <video class="hb-dm-video" autoplay muted loop playsinline preload="auto" disablepictureinpicture
                       @if($dmCover) poster="{{ $dmCover }}" @endif>
                    <source src="{{ $dmVideo }}" type="video/mp4">
                </video>
            @endif
        </div>

        <div class="hb-dm-hero-inner">
            {{-- Texte principal --}}
            <div class="hb-dm-hero-content">
                <p class="hb-eyebrow">
                    <span class="hb-dm-eyebrow-line"></span>
                    DOMOTIQUE · SMART BUILDING
                </p>

                <h1>
                    Le bâtiment intelligent,
                    <span>pensé pour demain.</span>
                </h1>

                <p class="hb-dm-lead">{{ $d['lead'] }}</p>
            </div>

            {{-- Choix rapide des univers --}}
            <div class="hb-dm-quick-univers" id="nos-univers">
                <div class="hb-dm-quick-heading">
                    <div>
                        <p class="hb-dm-quick-eyebrow">NOS SOLUTIONS</p>
                        <h2>Choisissez votre <span>univers.</span></h2>
                    </div>
                    <p class="hb-dm-quick-note">SMART G4 CONTROL <span>·</span> DISTRIBUTION EXCLUSIVE AU MAROC</p>
                </div>

                <div class="hb-dm-grid">
                    @foreach($d['markets'] as $i => $m)
                        @php
                            $local = !empty($m['image']) && file_exists(public_path($m['image'])) ? asset($m['image']) : null;
                            $img   = $local ?: ($m['fallback'] ?? '');
                            $url   = route('domotique.show', $m['slug']);
                            $mv    = !empty($m['video']) && file_exists(public_path($m['video'])) ? asset($m['video']) : null;
                        @endphp
                        <a href="{{ $url }}" class="hb-dm-card hb-dm-rv" style="--d:{{ .3 + $i * 0.12 }}s" aria-label="Découvrir {{ $m['title'] }}">
                            <div class="hb-dm-card-image">
                                @if($mv)
                                    {{-- La vidéo ne se télécharge et ne joue qu'au survol ; l'image (poster) reste affichée sinon --}}
                                    <video src="{{ $mv }}" poster="{{ $img }}" muted loop playsinline preload="none" disablepictureinpicture></video>
                                @else
                                    <img src="{{ $img }}" alt="{{ $m['title'] }} — HN BLOOM" loading="{{ $i === 0 ? 'eager' : 'lazy' }}" decoding="async">
                                @endif
                                <span class="hb-dm-card-shine" aria-hidden="true"></span>
                                <span class="hb-dm-index">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="hb-dm-card-glow" aria-hidden="true"></span>
                                <div class="hb-dm-card-overlay">
                                    <span class="hb-dm-card-kicker">{{ $m['category'] ?? 'SMART BUILDING' }}</span>
                                    <h3>{{ $m['title'] }}</h3>
                                    @if(!empty($m['text']))
                                        <p>{{ $m['text'] }}</p>
                                    @endif
                                    <span class="hb-dm-card-discover">Voir les solutions <span class="hb-dm-card-circle"><x-icon name="arrow-up-right" :size="18" /></span></span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>

<script>
(function () {
    var reduce = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    var saver  = navigator.connection && navigator.connection.saveData;
    var hasIO  = 'IntersectionObserver' in window;
    var tryPlay = function (v) {
        var p = v.play();
        if (p && p.catch) p.catch(function () {});
    };

    /* 1) Vidéo du hero : lecture directe, pause hors écran */
    var hero = document.querySelector('.hb-dm-video');
    if (hero) {
        hero.muted = true;
        if (saver || reduce) {
            hero.remove();               // l'image de fond reste affichée
        } else {
            tryPlay(hero);
            if (hasIO) {
                new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) tryPlay(hero); else hero.pause();
                    });
                }, { threshold: 0.1 }).observe(hero);
            }
        }
    }

    /* 2) Vidéos des cartes : uniquement au survol / au focus (souris),
          rien n'est téléchargé avant. Sur mobile, l'image reste affichée. */
    var canHover = window.matchMedia('(hover:hover) and (pointer:fine)').matches;
    document.querySelectorAll('.hb-dm-card').forEach(function (card) {
        var v = card.querySelector('video');
        if (!v) return;
        v.muted = true;
        if (saver || reduce || !canHover) return;

        var start = function () { v.preload = 'auto'; tryPlay(v); };
        var stop  = function () { v.pause(); };
        card.addEventListener('mouseenter', start);
        card.addEventListener('focus', start);
        card.addEventListener('mouseleave', stop);
        card.addEventListener('blur', stop);
    });

    /* 3) Apparition des cartes */
    var els = document.querySelectorAll('.hb-dm-rv');
    if (!hasIO || reduce) {
        els.forEach(function (el) { el.classList.add('is-in'); });
    } else {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
            });
        }, { threshold: 0.12 });
        els.forEach(function (el) { io.observe(el); });
    }
})();
</script>
<noscript><style>.hb-dm-rv:not(.is-in){opacity:1 !important}</style></noscript>

<style>
    .hb-dm-page { position:relative; isolation:isolate; background:#090908; }

    /* ================= HERO ================= */
    .hb-dm-hero {
        position:relative; isolation:isolate;
        min-height:100vh; min-height:100svh;
        display:flex; align-items:center; overflow:hidden;
        padding:clamp(82px,9vh,112px) clamp(25px,5vw,80px) 72px;
        background:#090908; color:#f8f4eb;
    }
    .hb-dm-hero, .hb-dm-hero * { box-sizing:border-box; }

    /* Plus de zoom animé ni de will-change : la vidéo reste sur sa propre couche, sans repaint */
    .hb-dm-hero-media {
        position:absolute; z-index:-2; inset:0;
        background-color:#151511; background-position:center; background-size:cover; background-repeat:no-repeat;
    }
    .hb-dm-hero-media::after {
        content:""; position:absolute; inset:0; pointer-events:none;
        background:
            radial-gradient(ellipse at 50% 45%, rgba(5,6,5,0) 0%, rgba(5,6,5,.35) 60%, rgba(5,6,5,.85) 100%),
            linear-gradient(90deg, rgba(5,6,5,.75) 0%, rgba(5,6,5,.3) 55%, rgba(5,6,5,.15) 100%),
            linear-gradient(0deg, rgba(5,6,5,.85) 0%, rgba(5,6,5,0) 45%);
    }
    .hb-dm-video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }

    .hb-dm-hero-inner { display:block; width:100%; max-width:1450px; margin:0 auto; }
    .hb-dm-hero-content { max-width:790px; }

    .hb-dm-hero .hb-eyebrow {
        display:flex; align-items:center; gap:14px; margin:0 0 18px;
        color:#f2d38a; font:700 10.5px/1.5 Arial,sans-serif; letter-spacing:4px; text-transform:uppercase;
    }
    .hb-dm-eyebrow-line { display:inline-block; flex:0 0 38px; height:1px; background:#dfb95f; }

    .hb-dm-hero h1 {
        margin:0; color:#fffdf7; font-family:Georgia,"Times New Roman",serif;
        font-size:clamp(30px,3.15vw,47px); font-weight:400; line-height:1.15; letter-spacing:-.6px;
        text-shadow:0 2px 20px rgba(0,0,0,.45);
    }
    .hb-dm-hero h1 span { display:block; margin-top:4px; color:#f2cf7b; font-style:italic; }

    .hb-dm-lead {
        max-width:700px; margin:13px 0 0; color:#e8e4db;
        font:400 12.5px/1.75 Arial,sans-serif; letter-spacing:.1px;
        text-shadow:0 1px 12px rgba(0,0,0,.55);
    }

    /* ================= CHOIX RAPIDE ================= */
    .hb-dm-quick-univers { width:100%; margin-top:clamp(25px,3.4vh,42px); }
    .hb-dm-quick-heading { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; margin-bottom:14px; }
    .hb-dm-quick-eyebrow { margin:0 0 5px; color:#e7c16e; font:700 8px/1.5 Arial,sans-serif; letter-spacing:2.5px; }
    .hb-dm-quick-heading h2 { margin:0; color:#fffaf0; font:400 clamp(23px,2.4vw,32px)/1.12 Georgia,'Times New Roman',serif; }
    .hb-dm-quick-heading h2 span { color:#e7c16e; font-style:italic; }
    .hb-dm-quick-note { margin:0 0 3px; color:#ded3b9; font:700 8px/1.7 Arial,sans-serif; letter-spacing:1.4px; text-align:right; }
    .hb-dm-quick-note span { color:#e7c16e; padding:0 3px; }

    .hb-dm-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:13px; }

    .hb-dm-card {
        position:relative; display:flex; flex-direction:column; min-width:0;
        height:clamp(220px,27vh,270px); overflow:hidden; isolation:isolate;
        border:1px solid rgba(226,190,105,.68); background:#090907;
        color:inherit; text-decoration:none;
        box-shadow:0 16px 42px rgba(0,0,0,.36);
        transition:transform .4s ease, border-color .4s ease, box-shadow .4s ease;
    }
    .hb-dm-card:hover, .hb-dm-card:focus-visible {
        transform:translateY(-5px); border-color:rgba(244,215,151,.98);
        box-shadow:0 26px 55px -18px rgba(0,0,0,.7), 0 0 30px -10px rgba(214,173,85,.35);
    }

    .hb-dm-card-image { position:absolute; inset:0; background:#161510; }
    /* Pas de filter sur l'image/vidéo : l'assombrissement passe par le dégradé ci-dessous */
    .hb-dm-card-image img, .hb-dm-card-image video {
        position:absolute; inset:0; display:block; width:100%; height:100%; object-fit:cover;
        transition:transform .9s cubic-bezier(.2,.7,.2,1);
    }
    .hb-dm-card:hover .hb-dm-card-image img,
    .hb-dm-card:hover .hb-dm-card-image video { transform:scale(1.06); }
    .hb-dm-card-image::before {
        content:""; position:absolute; z-index:1; inset:0; pointer-events:none;
        background:linear-gradient(180deg,rgba(5,5,4,.22) 0%,rgba(5,5,4,.1) 22%,rgba(5,5,4,.3) 42%,rgba(5,5,4,.78) 66%,rgba(5,5,4,.97) 100%);
    }

    /* Reflet : animé avec transform (léger), plus avec left */
    .hb-dm-card-shine {
        position:absolute; z-index:2; top:0; bottom:0; left:0; width:45%; pointer-events:none;
        background:linear-gradient(100deg,transparent,rgba(255,244,214,.26),transparent);
        transform:translateX(-170%) skewX(-18deg);
    }
    .hb-dm-card:hover .hb-dm-card-shine { transform:translateX(330%) skewX(-18deg); transition:transform 1s ease; }

    .hb-dm-card-glow {
        position:absolute; z-index:2; inset:0; pointer-events:none; opacity:0;
        background:radial-gradient(ellipse at 50% 80%,rgba(226,184,91,.22),transparent 55%);
        transition:opacity .5s;
    }
    .hb-dm-card:hover .hb-dm-card-glow { opacity:1; }

    /* Ligne dorée en haut au survol */
    .hb-dm-card::before {
        content:""; position:absolute; z-index:4; top:0; left:0; right:0; height:2px; pointer-events:none;
        background:linear-gradient(90deg,transparent,#f1d58e,transparent);
        transform:scaleX(0); transition:transform .6s cubic-bezier(.2,.7,.2,1);
    }
    .hb-dm-card:hover::before { transform:scaleX(1); }

    /* Badge : sans backdrop-filter */
    .hb-dm-index {
        position:absolute; z-index:3; top:11px; left:11px; display:grid; width:31px; height:31px; place-items:center;
        border:1px solid rgba(245,218,155,.9); background:rgba(8,8,7,.8); color:#f5d991;
        font-size:10px; box-shadow:0 4px 14px rgba(0,0,0,.35);
        transition:transform .4s ease, background .3s, color .3s;
    }
    .hb-dm-card:hover .hb-dm-index { transform:translateY(-3px); background:#d6ad55; color:#11100c; }

    .hb-dm-card-overlay {
        position:absolute; z-index:3; right:0; bottom:0; left:0; padding:15px 17px 13px; color:#fffaf0;
        background:linear-gradient(180deg,rgba(9,9,7,0) 0%,rgba(9,9,7,.48) 12%,rgba(9,9,7,.88) 45%,rgba(9,9,7,.96) 100%);
        border-top:1px solid rgba(231,193,110,.22); text-shadow:0 2px 12px rgba(0,0,0,.65);
    }
    .hb-dm-card-kicker { display:block; margin:0 0 4px; color:#f1d38b; font:700 8px/1.4 Arial,sans-serif; letter-spacing:1.65px; text-transform:uppercase; }
    .hb-dm-card-overlay h3 { margin:0; color:#fffaf0; font:400 clamp(21px,1.8vw,27px)/1.08 Georgia,"Times New Roman",serif; letter-spacing:-.35px; transition:color .3s; }
    .hb-dm-card:hover .hb-dm-card-overlay h3 { color:#fff1cf; }
    .hb-dm-card-overlay p { max-width:100%; margin:6px 0 0; color:#f0ece2; font:500 10px/1.45 Arial,sans-serif; }
    .hb-dm-card-discover {
        display:flex; align-items:center; justify-content:space-between; gap:12px;
        margin-top:9px; padding-top:8px; border-top:1px solid rgba(231,193,110,.38);
        color:#f4d58e; font:700 8px/1.5 Arial,sans-serif; letter-spacing:1.25px; text-transform:uppercase; text-shadow:none;
    }
    .hb-dm-card-circle {
        display:grid; flex:0 0 27px; width:27px; height:27px; place-items:center;
        border:1px solid rgba(242,211,138,.8); border-radius:50%;
        color:#f5d991; background:rgba(8,8,7,.68);
        transition:background .35s, color .35s, transform .35s;
    }
    .hb-dm-card:hover .hb-dm-card-circle { background:#e2bb62; color:#11100b; transform:rotate(45deg); }

    /* ================= ANIMATIONS (opacity / transform uniquement) ================= */
    @keyframes hbFadeUp { from { opacity:0; transform:translateY(26px); } to { opacity:1; transform:none; } }
    @keyframes hbLine   { from { transform:scaleX(0); } to { transform:scaleX(1); } }
    @keyframes hbRise   { from { opacity:0; transform:translateY(40px) scale(.97); } to { opacity:1; transform:none; } }

    .hb-dm-hero .hb-eyebrow { animation:hbFadeUp .9s .15s cubic-bezier(.2,.7,.2,1) both; }
    .hb-dm-eyebrow-line { transform-origin:left; animation:hbLine 1s .5s ease-out both; }
    .hb-dm-hero h1 { animation:hbFadeUp 1s .3s cubic-bezier(.2,.7,.2,1) both; }
    .hb-dm-hero h1 span { animation:hbFadeUp 1s .5s cubic-bezier(.2,.7,.2,1) both; }
    .hb-dm-lead { animation:hbFadeUp 1s .7s cubic-bezier(.2,.7,.2,1) both; }
    .hb-dm-quick-heading { animation:hbFadeUp 1s .85s cubic-bezier(.2,.7,.2,1) both; }

    /* Cartes : visibles dès le départ dans le hero, donc apparition échelonnée */
    .hb-dm-rv:not(.is-in) { opacity:0; }
    .hb-dm-rv.is-in { animation:hbRise 1s var(--d,0s) cubic-bezier(.2,.7,.2,1) backwards; }

    /* ================= RESPONSIVE ================= */
    @media (max-width:1100px) {
        .hb-dm-hero { padding-inline:4%; }
    }
    @media (max-width:900px) {
        .hb-dm-hero { padding:90px 28px 48px; }
        .hb-dm-grid { gap:9px; }
        .hb-dm-card { height:230px; }
        .hb-dm-card-overlay { padding:13px 12px 11px; }
        .hb-dm-card-overlay h3 { font-size:21px; }
        .hb-dm-card-overlay p { font-size:9px; }
        .hb-dm-card-discover { margin-top:7px; padding-top:7px; }
        .hb-dm-quick-note { max-width:210px; }
    }
    @media (max-width:760px) {
        .hb-dm-hero .hb-eyebrow { gap:10px; margin-bottom:16px; font-size:9.5px; letter-spacing:2.5px; }
        .hb-dm-eyebrow-line { flex-basis:26px; }
    }
    @media (max-width:620px) {
        .hb-dm-hero { padding:92px 18px 36px; }
        .hb-dm-hero h1 { font-size:clamp(31px,9vw,42px); }
        .hb-dm-lead { font-size:12px; line-height:1.7; }
        .hb-dm-quick-univers { margin-top:28px; }
        .hb-dm-quick-heading { align-items:flex-start; flex-direction:column; gap:7px; margin-bottom:12px; }
        .hb-dm-quick-heading h2 { font-size:27px; }
        .hb-dm-quick-note { max-width:none; text-align:left; font-size:7px; }
        .hb-dm-grid { grid-template-columns:1fr; gap:10px; }
        .hb-dm-card { height:190px; }
        .hb-dm-card-image img, .hb-dm-card-image video { object-position:center 48%; }
        .hb-dm-card-overlay { padding:15px 17px 12px; }
        .hb-dm-card-overlay h3 { font-size:25px; }
        .hb-dm-card-overlay p { font-size:10px; }
        .hb-dm-card-discover { margin-top:8px; }
        .hb-dm-card-circle { width:27px; height:27px; flex-basis:27px; }
        .hb-dm-index { top:10px; left:10px; width:29px; height:29px; }
    }

    @media (prefers-reduced-motion:reduce) {
        .hb-dm-rv:not(.is-in) { opacity:1; }
        .hb-dm-hero *, .hb-dm-rv { animation:none !important; }
        .hb-dm-card, .hb-dm-card-image img, .hb-dm-card-image video, .hb-dm-card-shine { transition:none; }
        .hb-dm-video { display:none; }
    }
</style>
</x-hb-layout>