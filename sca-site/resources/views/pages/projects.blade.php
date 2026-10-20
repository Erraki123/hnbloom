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
@endphp

<x-hb-layout title="Nos projets" description="Les projets réalisés par HN BLOOM : industrie, événementiel, sport, tertiaire et institutions.">

<div class="hb-pl-page">

    {{-- Fond fixe (pseudo-élément : compatible mobile) --}}
    @if($cover)
        <div class="hb-pl-fixed-bg" style="background-image:url('{{ $cover }}')" aria-hidden="true"></div>
    @endif

    {{-- HERO --}}
    <section class="hb-pl-hero">
        @if($cover || $heroVideo)
            <div class="hb-pl-hero-media" aria-hidden="true">
                @if($cover)
                    <div class="hb-pl-hero-bg" style="background-image:url('{{ $cover }}')"></div>
                @endif

                @if($heroVideo)
                    <video class="hb-pl-hero-video" autoplay muted loop playsinline preload="metadata" disablepictureinpicture>
                        <source src="{{ $heroVideo }}" type="video/mp4">
                    </video>
                @endif
            </div>
        @endif

        <div class="hb-wrap hb-pl-hero-inner">
            <nav class="hb-pl-crumbs" aria-label="Fil d’Ariane">
                <a href="{{ route('home') }}">Accueil</a><span>/</span>
                <b>Projets</b>
            </nav>
            <p class="hb-eyebrow">NOS PROJETS</p>
            <h1>Réalisations<br>et références</h1>
            <p class="hb-pl-lead">Industries, institutions, événements, sport et tertiaire : des sites très différents, un même niveau d’exigence.</p>
            <p class="hb-pl-count">{{ str_pad(count($projects), 2, '0', STR_PAD_LEFT) }} projets</p>
        </div>
    </section>

    {{-- GRILLE --}}
    <section class="hb-pl-section">
        <div class="hb-wrap">

            <div class="hb-pl-toolbar">
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
                       style="--d:{{ ($i % 3) * 90 }}ms">
                        <span class="hb-pl-thumb">
                            @if($hasThumb)
                                <img src="{{ asset($thumb) }}" alt="{{ $p['title'] }}" loading="lazy" decoding="async">
                            @else
                                <span class="hb-pl-noimg">{{ $p['title'] }}</span>
                            @endif
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
        .hb-pl-page { position:relative; background:#090908; isolation:isolate; }

        /* Fond fixe : fonctionne aussi sur mobile (contrairement à background-attachment:fixed) */
        .hb-pl-fixed-bg { position:fixed; inset:0; z-index:-2; background-size:cover; background-position:center; }
        .hb-pl-page::before { content:""; position:fixed; inset:0; z-index:-1; background:linear-gradient(180deg, rgba(9,9,8,.78) 0%, rgba(9,9,8,.92) 100%); }

        .hb-pl-hero, .hb-pl-section { --g:#d8b45e; --gl:#f0d69a; --muted:#b1ada3; --border:rgba(216,180,94,.25); color:#f6f1e7; }

        /* ====== HERO ====== */
        .hb-pl-hero { position:relative; overflow:hidden; min-height:460px; display:flex; align-items:flex-end; border-bottom:1px solid var(--border); }
        .hb-pl-hero-media { position:absolute; inset:0; }
        .hb-pl-hero-bg { position:absolute; inset:0; background-size:cover; background-position:center; transform:scale(1.04); }
        .hb-pl-hero-video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center; display:block; opacity:0; transition:opacity .9s ease; }
        .hb-pl-hero-video.is-on { opacity:1; }
        .hb-pl-hero::after { content:""; position:absolute; inset:0; background:linear-gradient(180deg,rgba(9,9,8,.55),rgba(9,9,8,.35) 45%,rgba(9,9,8,.85) 100%); }
        .hb-pl-hero-inner { position:relative; z-index:1; width:100%; padding-top:130px; padding-bottom:64px; }
        .hb-pl-crumbs { display:flex; gap:10px; align-items:center; margin-bottom:28px; font-size:11px; letter-spacing:.4px; color:#d0ccc1; }
        .hb-pl-crumbs a { color:#d0ccc1; text-decoration:none; transition:color .2s; }
        .hb-pl-crumbs a:hover { color:var(--gl); }
        .hb-pl-crumbs b { color:var(--gl); font-weight:400; }
        .hb-pl-crumbs span { opacity:.5; }
        .hb-pl-hero .hb-eyebrow { margin:0 0 16px; color:var(--g); font-size:10px; font-weight:600; letter-spacing:2.4px; }
        .hb-pl-hero h1 { max-width:880px; margin:0; font-family:Georgia,"Times New Roman",serif; font-size:clamp(36px,5.4vw,66px); font-weight:400; line-height:1.08; letter-spacing:-1px; color:#f6f1e7; }
        .hb-pl-lead { max-width:560px; margin:22px 0 0; color:#d6d2c8; font-size:14px; line-height:1.9; }
        .hb-pl-count { display:inline-block; margin:26px 0 0; padding-top:14px; border-top:1px solid var(--border); color:var(--gl); font-family:Georgia,serif; font-size:15px; letter-spacing:1px; }

        /* ====== SECTION ====== */
        .hb-pl-section { padding:60px 0 90px; background:rgba(9,9,8,.35); }

        /* Barre de recherche */
        .hb-pl-toolbar { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:32px; }
        .hb-pl-search { display:flex; align-items:center; gap:10px; width:min(100%,360px); padding:0 16px; height:46px; border:1px solid var(--border); background:rgba(16,16,14,.85); color:var(--g); transition:border-color .25s, box-shadow .25s; }
        .hb-pl-search:focus-within { border-color:var(--g); box-shadow:0 0 0 3px rgba(216,180,94,.15); }
        .hb-pl-search input { flex:1; min-width:0; border:0; outline:0; background:transparent; color:#f6f1e7; font:inherit; font-size:14px; }
        .hb-pl-search input::placeholder { color:#8d897f; }
        .hb-pl-results { color:var(--muted); font-size:12px; letter-spacing:.5px; }

        /* Grille */
        .hb-pl-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:24px; }

        .hb-pl-card {
            display:flex; flex-direction:column; overflow:hidden;
            border:1px solid var(--border); background:transparent; -webkit-backdrop-filter:none; backdrop-filter:none;
            color:inherit; text-decoration:none;
            opacity:0; transform:translateY(24px);
            transition:opacity .7s ease var(--d,0ms), transform .7s cubic-bezier(.2,.7,.2,1) var(--d,0ms), border-color .3s, box-shadow .3s;
        }
        .hb-pl-card.is-visible { opacity:1; transform:none; }
        .hb-pl-card.is-hidden { display:none; }
        .hb-pl-card.is-visible:hover { transform:translateY(-6px); border-color:var(--g); box-shadow:0 20px 44px rgba(0,0,0,.55), 0 0 0 1px rgba(216,180,94,.18); transition-delay:0ms; }
        .hb-pl-card:focus-visible { outline:2px solid var(--g); outline-offset:3px; }

        /* ===== Carte transparente : on voit le fond de la page ===== */
        .hb-pl-thumb { position:relative; display:flex; align-items:center; justify-content:center; aspect-ratio:4/3; overflow:hidden; background:transparent; }
        .hb-pl-thumb img { display:block; width:100%; height:100%; padding:24px; object-fit:contain; object-position:center; background:transparent; transition:transform .55s cubic-bezier(.2,.7,.2,1); }
        .hb-pl-card:hover .hb-pl-thumb img { transform:scale(1.07); }
        .hb-pl-noimg { display:flex; align-items:center; justify-content:center; width:100%; height:100%; padding:20px; text-align:center; color:#f1ece0; font-family:Georgia,serif; font-size:15px; background:transparent; }

        .hb-pl-info { display:flex; align-items:center; gap:14px; padding:18px 20px; border-top:1px solid var(--border); }
        .hb-pl-info small { color:var(--g); font-family:Georgia,serif; font-size:14px; }
        .hb-pl-info strong { flex:1; font-family:Georgia,serif; font-size:16px; font-weight:400; line-height:1.35; color:#f1ece0; }
        .hb-pl-info svg { flex:none; color:var(--g); transition:transform .3s; }
        .hb-pl-card:hover .hb-pl-info svg { transform:translate(3px,-3px); }

        .hb-pl-empty { padding:60px 0; text-align:center; color:var(--muted); font-size:15px; }

        /* ====== RESPONSIVE ====== */
        @media (max-width:980px){ .hb-pl-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (max-width:600px){
            .hb-pl-hero { min-height:400px; }
            .hb-pl-hero-inner { padding-top:100px; padding-bottom:42px; }
            .hb-pl-grid { grid-template-columns:1fr; gap:16px; }
            .hb-pl-toolbar { flex-direction:column; align-items:stretch; }
            .hb-pl-search { width:100%; }
        }

        /* ====== ACCESSIBILITÉ ====== */
        @media (prefers-reduced-motion:reduce){
            .hb-pl-card { opacity:1; transform:none; transition:border-color .2s; }
            .hb-pl-thumb img, .hb-pl-info svg { transition:none; }
            .hb-pl-hero-video { display:none; }
        }
    </style>

    <script>
        (function () {
            /* ---------- Vidéo hero : affichage seulement si elle démarre ---------- */
            var v = document.querySelector('.hb-pl-hero-video');
            if (v) {
                v.addEventListener('playing', function () { v.classList.add('is-on'); });
                v.addEventListener('error', function () { v.remove(); });
                var src = v.querySelector('source');
                if (src) src.addEventListener('error', function () { v.remove(); });
                var p = v.play();
                if (p && p.catch) p.catch(function () { v.remove(); });
            }

            /* ---------- Apparition des cartes au scroll ---------- */
            var cards = Array.prototype.slice.call(document.querySelectorAll('.hb-pl-card'));
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) {
                            e.target.classList.add('is-visible');
                            io.unobserve(e.target);
                        }
                    });
                }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
                cards.forEach(function (c) { io.observe(c); });
            } else {
                cards.forEach(function (c) { c.classList.add('is-visible'); });
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
                    c.classList.toggle('is-hidden', !match);
                    if (match) { shown++; c.classList.add('is-visible'); }
                });
                results.textContent = q ? shown + ' résultat' + (shown > 1 ? 's' : '') : '';
                empty.hidden = shown !== 0;
            }

            if (input) input.addEventListener('input', filter);
        })();
    </script>
</x-hb-layout>
