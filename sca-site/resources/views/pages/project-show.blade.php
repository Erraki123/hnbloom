@php
    use Illuminate\Support\Js;
    use Illuminate\Support\Str;

    $heading    = $project['heading'] ?? $project['title'];
    $paragraphs = $project['paragraphs'] ?? [];

    // Visuels du projet (uniquement les fichiers qui existent)
    $rawImages = array_values(array_filter(array_merge([$project['image'] ?? null], $project['gallery'] ?? [])));
    $images    = array_map('asset', array_values(array_filter($rawImages, fn ($p) => file_exists(public_path($p)))));

    // Logo / vignette du client
    $thumbPath = $project['thumb'] ?? null;
    $logo      = ($thumbPath && file_exists(public_path($thumbPath))) ? asset($thumbPath) : null;

    // Photo de fond du site (même logique que la page « Nos projets »)
    $bgPath = collect([
        config('hnbloom.images.projects', 'images/projects.jpg'),
        config('hnbloom.images.hero'),
        config('hnbloom.images.about'),
    ])->first(fn ($p) => $p && file_exists(public_path($p)));
    $siteBg = $bgPath ? asset($bgPath) : null;

    // Fond du hero : première vraie photo du projet, sinon la photo du site
    $heroBg = $images[0] ?? $siteBg;

    // Champs optionnels : s'affichent seulement s'ils existent dans la config
    $meta = array_filter([
        'Client'  => $project['name'] ?? null,
        'Secteur' => $project['category'] ?? null,
        'Ville'   => $project['city'] ?? null,
        'Année'   => $project['year'] ?? null,
        'Mission' => $project['role'] ?? null,
    ]);
@endphp

<x-hb-layout :title="$heading" :description="Str::limit($paragraphs[0] ?? 'Projet réalisé par HN BLOOM.', 150)">

<div class="hb-pd-page">

    <div class="hb-pd-progress" aria-hidden="true"><span></span></div>

    @if($siteBg)
        <div class="hb-pd-fixed-bg" style="background-image:url('{{ $siteBg }}')" aria-hidden="true"></div>
    @endif

    {{-- HERO --}}
    <section class="hb-pd-hero">
        @if($heroBg)
            <div class="hb-pd-hero-bg" style="background-image:url('{{ $heroBg }}')" aria-hidden="true"></div>
        @endif

        <div class="hb-wrap hb-pd-hero-inner">
            <nav class="hb-pd-crumbs hb-pd-in" style="--i:0" aria-label="Fil d’Ariane">
                <a href="{{ route('home') }}">Accueil</a><span>/</span>
                <a href="{{ route('projects') }}">Projets</a><span>/</span>
                <b>{{ $project['title'] }}</b>
            </nav>
            <p class="hb-eyebrow hb-pd-in" style="--i:1"><i></i>{{ strtoupper($project['category'] ?? 'NOS PROJETS') }}</p>
            <h1 class="hb-pd-in" style="--i:2">{{ $heading }}</h1>
        </div>

        @if(count($meta))
            <div class="hb-pd-meta hb-pd-in" style="--i:4">
                <div class="hb-wrap">
                    <dl>
                        @foreach($meta as $label => $value)
                            <div><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>
                        @endforeach
                    </dl>
                </div>
            </div>
        @endif
    </section>

    {{-- CONTENU --}}
    <section class="hb-pd-section" x-data="hbLightbox({{ Js::from($images) }})"
             @keydown.escape.window="close()"
             @keydown.arrow-right.window="open && next()"
             @keydown.arrow-left.window="open && prev()">
        <div class="hb-wrap">

            <div class="hb-pd-layout">
                <div class="hb-pd-text hb-pd-rv">
                    <p class="hb-pd-kicker"><i></i>À PROPOS DU PROJET</p>
                    @forelse($paragraphs as $k => $para)
                        <p class="{{ $k === 0 ? 'is-lead' : '' }}">{{ $para }}</p>
                    @empty
                        <p class="is-lead">Un projet réalisé par HN BLOOM, avec le même niveau d’exigence que tous nos sites de référence.</p>
                    @endforelse
                </div>

                <aside class="hb-pd-side hb-pd-rv" style="--d:.12s">
                    @if($logo)
                        <div class="hb-pd-logo"><img src="{{ $logo }}" alt="{{ $project['title'] }}" decoding="async"></div>
                    @endif
                    <div class="hb-pd-side-body">
                        <p class="hb-pd-kicker"><i></i>VOTRE PROJET</p>
                        @if(!empty($project['website']))
                            <a href="{{ $project['website'] }}" target="_blank" rel="noopener noreferrer" class="hb-pd-btn">
                                Visiter le site <x-icon name="arrow-up-right" :size="15" />
                            </a>
                        @endif
                        <a href="{{ route('contact', ['sujet' => 'quote']) }}" class="hb-pd-btn {{ !empty($project['website']) ? 'is-outline' : '' }}">
                            Un projet similaire ? <x-icon name="arrow-right" :size="15" />
                        </a>
                    </div>
                </aside>
            </div>

            {{-- GALERIE --}}
            @if(count($images))
                <div class="hb-pd-gallery-head hb-pd-rv">
                    <p class="hb-pd-kicker"><i></i>GALERIE</p>
                    <span>{{ str_pad(count($images), 2, '0', STR_PAD_LEFT) }} visuel{{ count($images) > 1 ? 's' : '' }}</span>
                </div>
                <div class="hb-pd-gallery {{ count($images) === 1 ? 'is-single' : '' }}">
                    @foreach($images as $k => $img)
                        <button type="button" class="hb-pd-shot hb-pd-rv {{ $k === 0 && count($images) > 1 ? 'is-first' : '' }}"
                                style="--d:{{ ($k % 2) * 120 }}ms"
                                @click="show({{ $k }})" aria-label="Agrandir l’image {{ $k + 1 }}">
                            <img src="{{ $img }}" alt="{{ $heading }} — image {{ $k + 1 }}" loading="lazy" decoding="async">
                            <i class="hb-pd-shot-veil" aria-hidden="true"></i>
                            <span class="hb-pd-shot-ico"><x-icon name="maximize-2" :size="18" /></span>
                            <small>{{ str_pad($k + 1, 2, '0', STR_PAD_LEFT) }}</small>
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- PRÉCÉDENT / SUIVANT --}}
            <nav class="hb-pd-nav hb-pd-rv" aria-label="Navigation entre projets">
                @if($prev)
                    <a href="{{ route('projects.show', $prev['slug']) }}" class="is-prev">
                        @if(!empty($prev['thumb']) && file_exists(public_path($prev['thumb'])))
                            <span class="hb-pd-nav-img"><img src="{{ asset($prev['thumb']) }}" alt="" loading="lazy"></span>
                        @endif
                        <span class="hb-pd-nav-txt"><small>← Projet précédent</small><strong>{{ $prev['title'] }}</strong></span>
                    </a>
                @else <span></span> @endif
                @if($next)
                    <a href="{{ route('projects.show', $next['slug']) }}" class="is-next">
                        <span class="hb-pd-nav-txt"><small>Projet suivant →</small><strong>{{ $next['title'] }}</strong></span>
                        @if(!empty($next['thumb']) && file_exists(public_path($next['thumb'])))
                            <span class="hb-pd-nav-img"><img src="{{ asset($next['thumb']) }}" alt="" loading="lazy"></span>
                        @endif
                    </a>
                @endif
            </nav>

            <div class="hb-pd-back hb-pd-rv">
                <a href="{{ route('projects') }}"><x-icon name="arrow-left" :size="15" /> Tous les projets</a>
            </div>
        </div>

        {{-- LIGHTBOX --}}
        <div class="hb-lb" x-show="open" x-cloak x-transition.opacity.duration.250ms @click.self="close()"
             @touchstart.passive="tx = $event.changedTouches[0].clientX"
             @touchend.passive="swipe($event.changedTouches[0].clientX)">
            <button type="button" class="hb-lb-close" @click="close()" aria-label="Fermer"><x-icon name="x" :size="22" /></button>
            <button type="button" class="hb-lb-nav is-l" x-show="imgs.length > 1" @click="prev()" aria-label="Précédent"><x-icon name="arrow-left" :size="20" /></button>
            <img :src="imgs[i]" alt="">
            <button type="button" class="hb-lb-nav is-r" x-show="imgs.length > 1" @click="next()" aria-label="Suivant"><x-icon name="arrow-right" :size="20" /></button>

            <div class="hb-lb-bar" x-show="imgs.length > 1">
                <p class="hb-lb-count" x-text="(i + 1) + ' / ' + imgs.length"></p>
                <div class="hb-lb-thumbs">
                    <template x-for="(src, n) in imgs" :key="n">
                        <button type="button" :class="{ 'is-on': n === i }" @click="i = n" :aria-label="'Image ' + (n + 1)"><img :src="src" alt=""></button>
                    </template>
                </div>
            </div>
        </div>
    </section>

</div>{{-- /hb-pd-page --}}

    @include('partials.hb-cta', ['heading' => 'Engagez-vous dans l’Excellence avec HN BLOOM'])

    <script>
        function hbLightbox(imgs) {
            return {
                imgs, i: 0, open: false, tx: 0,
                show(n) { this.i = n; this.open = true; document.documentElement.style.overflow = 'hidden'; },
                close() { this.open = false; document.documentElement.style.overflow = ''; },
                next() { this.i = (this.i + 1) % this.imgs.length; },
                prev() { this.i = (this.i - 1 + this.imgs.length) % this.imgs.length; },
                swipe(x) { var d = x - this.tx; if (Math.abs(d) > 50 && this.imgs.length > 1) { d < 0 ? this.next() : this.prev(); } },
            };
        }

        (function () {
            var reduce = window.matchMedia('(prefers-reduced-motion:reduce)').matches;

            /* Apparition au scroll */
            var rv = Array.prototype.slice.call(document.querySelectorAll('.hb-pd-rv'));
            if ('IntersectionObserver' in window && !reduce) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
                    });
                }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
                rv.forEach(function (r) { io.observe(r); });
            } else {
                rv.forEach(function (r) { r.classList.add('is-in'); });
            }

            /* Barre de progression */
            var bar = document.querySelector('.hb-pd-progress span');
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
        })();
    </script>

    <style>
        [x-cloak] { display:none !important; }

        /* ====== PAGE ====== */
        .hb-pd-page { position:relative; background:#090908; isolation:isolate; font-family:Arial,Helvetica,sans-serif; }
        .hb-pd-fixed-bg { position:fixed; inset:0; z-index:-2; background-size:cover; background-position:center; }
        .hb-pd-page::before { content:""; position:fixed; inset:0; z-index:-1; background:linear-gradient(180deg, rgba(9,9,8,.8) 0%, rgba(9,9,8,.94) 100%); }
        .hb-pd-hero, .hb-pd-section { --g:#d8b45e; --gl:#f0d69a; --muted:#aaa69c; --border:rgba(216,180,94,.25); color:#f6f1e7; }

        /* ====== HERO ====== */
        .hb-pd-hero { position:relative; overflow:hidden; display:flex; flex-direction:column; min-height:72vh; min-height:72svh; border-bottom:1px solid var(--border); }
        .hb-pd-hero-bg { position:absolute; inset:0; background-size:cover; background-position:center; }
        .hb-pd-hero::after { content:""; position:absolute; inset:0; pointer-events:none; background:
            radial-gradient(ellipse at 50% 45%, rgba(5,6,5,0) 0%, rgba(5,6,5,.35) 60%, rgba(5,6,5,.85) 100%),
            linear-gradient(90deg, rgba(5,6,5,.8) 0%, rgba(5,6,5,.4) 55%, rgba(5,6,5,.25) 100%),
            linear-gradient(0deg, rgba(5,6,5,.95) 0%, rgba(5,6,5,0) 50%),
            linear-gradient(180deg, rgba(5,6,5,.55) 0%, rgba(5,6,5,0) 25%); }
        .hb-pd-hero-inner { position:relative; z-index:1; width:100%; flex:1; display:flex; flex-direction:column; justify-content:flex-end; padding-top:130px; padding-bottom:48px; }
        .hb-pd-hero-inner > * { align-self:flex-start; }
        .hb-pd-crumbs { display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-bottom:28px; font-size:11px; letter-spacing:.4px; color:#d0ccc1; }
        .hb-pd-crumbs a { color:#d0ccc1; text-decoration:none; transition:color .2s; }
        .hb-pd-crumbs a:hover { color:var(--gl); }
        .hb-pd-crumbs b { color:var(--gl); font-weight:400; }
        .hb-pd-crumbs span { opacity:.5; }
        .hb-pd-hero .hb-eyebrow { display:flex; align-items:center; gap:14px; margin:0 0 18px; color:var(--gl); font:700 10.5px/1.5 Arial,Helvetica,sans-serif; letter-spacing:4px; text-transform:uppercase; }
        .hb-pd-hero .hb-eyebrow i { display:inline-block; flex:0 0 40px; height:1px; background:var(--g); transform-origin:left; animation:hbPdRule 1s .6s ease-out both; }
        .hb-pd-hero h1 { max-width:900px; margin:0; font-family:Georgia,"Times New Roman",serif; font-size:clamp(32px,4.4vw,60px); font-weight:400; line-height:1.1; letter-spacing:-.8px; color:#fffdf7; text-shadow:0 2px 24px rgba(0,0,0,.45); }

        /* Bandeau d'informations */
        .hb-pd-meta { position:relative; z-index:2; border-top:1px solid rgba(216,180,94,.22); background:rgba(9,9,8,.6); -webkit-backdrop-filter:blur(8px); backdrop-filter:blur(8px); }
        .hb-pd-meta dl { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); margin:0; }
        .hb-pd-meta dl div { padding:20px 24px; border-left:1px solid rgba(216,180,94,.2); }
        .hb-pd-meta dl div:first-child { padding-left:0; border-left:0; }
        .hb-pd-meta dt { color:var(--muted); font-size:9px; letter-spacing:2px; text-transform:uppercase; }
        .hb-pd-meta dd { margin:7px 0 0; font-family:Georgia,serif; font-size:16px; color:#f1ece0; }

        /* ====== CONTENU ====== */
        .hb-pd-section { position:relative; padding:80px 0 90px; background:rgba(9,9,8,.35); }
        .hb-pd-section::before { content:""; position:absolute; top:0; left:6%; right:6%; height:1px; background:linear-gradient(90deg,transparent,var(--g),transparent); }
        .hb-pd-kicker { display:flex; align-items:center; gap:14px; margin:0 0 24px; color:var(--gl); font:700 10px/1.5 Arial,Helvetica,sans-serif; letter-spacing:3.5px; }
        .hb-pd-kicker i { display:block; flex:0 0 34px; height:1px; background:var(--g); }

        .hb-pd-layout { display:grid; grid-template-columns:minmax(0,1.7fr) minmax(290px,1fr); gap:64px; align-items:start; margin-bottom:80px; }
        .hb-pd-text p:not(.hb-pd-kicker) { margin:0 0 20px; color:#c4c0b6; font-size:14.5px; line-height:2; }
        .hb-pd-text p.is-lead { color:#f1ece0; font-family:Georgia,serif; font-size:23px; line-height:1.6; }

        .hb-pd-side { position:sticky; top:100px; overflow:hidden; border:1px solid var(--border); background:linear-gradient(160deg, rgba(22,22,19,.8), rgba(12,12,10,.8)); }
        .hb-pd-side::before { content:""; position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg,transparent,var(--gl),transparent); }
        .hb-pd-logo { display:flex; align-items:center; justify-content:center; aspect-ratio:16/10; padding:28px 34px; border-bottom:1px solid var(--border); background:radial-gradient(ellipse at 50% 50%, rgba(216,180,94,.1), transparent 70%); }
        .hb-pd-logo img { display:block; max-width:100%; max-height:100%; object-fit:contain; }
        .hb-pd-side-body { padding:26px 26px 28px; }
        .hb-pd-side-body .hb-pd-kicker { margin-bottom:18px; }
        .hb-pd-btn { display:flex; align-items:center; justify-content:space-between; gap:14px; min-height:48px; margin-top:12px; padding:0 18px; border:1px solid var(--g); background:var(--g); color:#15130d; font-size:11.5px; font-weight:700; letter-spacing:.4px; text-decoration:none; transition:background .25s, color .25s, transform .25s; }
        .hb-pd-btn:hover { background:transparent; color:var(--gl); transform:translateY(-2px); }
        .hb-pd-btn.is-outline { background:transparent; color:var(--gl); font-weight:600; }
        .hb-pd-btn.is-outline:hover { background:var(--g); color:#15130d; }

        /* ====== GALERIE ====== */
        .hb-pd-gallery-head { display:flex; align-items:baseline; justify-content:space-between; padding-bottom:16px; margin-bottom:24px; border-bottom:1px solid var(--border); }
        .hb-pd-gallery-head .hb-pd-kicker { margin:0; }
        .hb-pd-gallery-head span { color:var(--muted); font-size:11px; letter-spacing:1px; }
        .hb-pd-gallery { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
        .hb-pd-gallery.is-single { grid-template-columns:1fr; }
        .hb-pd-shot { position:relative; display:block; padding:0; border:1px solid var(--border); background:#14130f; overflow:hidden; cursor:zoom-in; aspect-ratio:16/10; transition:border-color .4s, box-shadow .5s; }
        .hb-pd-shot.is-first { grid-column:1 / -1; aspect-ratio:21/9; }
        .hb-pd-gallery.is-single .hb-pd-shot { aspect-ratio:21/10; }
        .hb-pd-shot:hover { border-color:var(--g); box-shadow:0 26px 50px -22px rgba(0,0,0,.75), 0 0 36px -12px rgba(216,180,94,.35); }
        .hb-pd-shot:focus-visible { outline:2px solid var(--g); outline-offset:3px; }
        .hb-pd-shot img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .9s cubic-bezier(.2,.7,.2,1); }
        .hb-pd-shot:hover img { transform:scale(1.06); }
        .hb-pd-shot-veil { position:absolute; inset:0; pointer-events:none; opacity:0; transition:opacity .4s; background:linear-gradient(0deg, rgba(5,6,5,.65), transparent 55%); }
        .hb-pd-shot:hover .hb-pd-shot-veil { opacity:1; }
        .hb-pd-shot-ico { position:absolute; right:16px; bottom:16px; display:grid; width:40px; height:40px; place-items:center; border:1px solid rgba(255,255,255,.6); border-radius:50%; color:#fff; background:rgba(0,0,0,.35); opacity:0; transform:translateY(8px); transition:opacity .3s, transform .3s, background .3s, color .3s; }
        .hb-pd-shot:hover .hb-pd-shot-ico { opacity:1; transform:none; background:var(--g); border-color:var(--g); color:#111; }
        .hb-pd-shot small { position:absolute; left:16px; bottom:14px; color:var(--gl); font-family:Georgia,serif; font-size:14px; opacity:0; transition:opacity .3s; }
        .hb-pd-shot:hover small { opacity:1; }

        /* ====== NAVIGATION ====== */
        .hb-pd-nav { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:80px; }
        .hb-pd-nav a { display:flex; align-items:center; gap:20px; padding:14px; border:1px solid var(--border); background:linear-gradient(160deg, rgba(22,22,19,.72), rgba(12,12,10,.72)); color:inherit; text-decoration:none; transition:border-color .3s, transform .35s, box-shadow .4s; }
        .hb-pd-nav a:hover { border-color:var(--g); transform:translateY(-4px); box-shadow:0 22px 40px -20px rgba(0,0,0,.7); }
        .hb-pd-nav a.is-next { justify-content:flex-end; text-align:right; }
        .hb-pd-nav-img { display:flex; align-items:center; justify-content:center; flex:0 0 110px; height:70px; padding:8px 12px; border:1px solid rgba(216,180,94,.18); background:rgba(9,9,8,.6); }
        .hb-pd-nav-img img { display:block; max-width:100%; max-height:100%; object-fit:contain; }
        .hb-pd-nav small { display:block; margin-bottom:7px; color:var(--g); font-size:10px; letter-spacing:1.2px; }
        .hb-pd-nav strong { font-family:Georgia,serif; font-size:16px; font-weight:400; line-height:1.35; color:#f1ece0; }

        .hb-pd-back { margin-top:40px; text-align:center; }
        .hb-pd-back a { display:inline-flex; align-items:center; gap:10px; color:var(--muted); font-size:11.5px; letter-spacing:1.5px; text-transform:uppercase; text-decoration:none; transition:color .25s, gap .25s; }
        .hb-pd-back a:hover { color:var(--gl); gap:14px; }

        /* ====== LIGHTBOX ====== */
        .hb-lb { position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; padding:56px 80px 130px; background:rgba(5,5,4,.95); }
        .hb-lb > img { max-width:100%; max-height:100%; object-fit:contain; }
        .hb-lb-close, .hb-lb-nav { position:absolute; display:grid; width:46px; height:46px; place-items:center; border:1px solid rgba(255,255,255,.4); border-radius:50%; background:transparent; color:#fff; cursor:pointer; transition:background .25s, border-color .25s, color .25s; }
        .hb-lb-close:hover, .hb-lb-nav:hover { background:#d8b45e; border-color:#d8b45e; color:#111; }
        .hb-lb-close { top:20px; right:20px; }
        .hb-lb-nav { top:50%; margin-top:-23px; }
        .hb-lb-nav.is-l { left:18px; } .hb-lb-nav.is-r { right:18px; }
        .hb-lb-bar { position:absolute; left:0; right:0; bottom:16px; display:flex; flex-direction:column; align-items:center; gap:12px; }
        .hb-lb-count { margin:0; color:#bdb8ad; font-size:11px; letter-spacing:1.5px; }
        .hb-lb-thumbs { display:flex; gap:8px; max-width:90%; overflow-x:auto; padding:2px; }
        .hb-lb-thumbs button { flex:none; width:64px; height:44px; padding:0; border:1px solid transparent; background:#111; opacity:.5; cursor:pointer; transition:opacity .25s, border-color .25s; }
        .hb-lb-thumbs button.is-on, .hb-lb-thumbs button:hover { opacity:1; border-color:#d8b45e; }
        .hb-lb-thumbs img { display:block; width:100%; height:100%; object-fit:cover; }

        /* ====== ANIMATIONS ====== */
        @keyframes hbPdRule { from { transform:scaleX(0); } to { transform:scaleX(1); } }
        @keyframes hbPdUp   { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:none; } }
        .hb-pd-in { animation:hbPdUp 1s calc(.15s + var(--i,0) * .16s) cubic-bezier(.2,.7,.2,1) backwards; }
        .hb-pd-rv:not(.is-in) { opacity:0; }
        .hb-pd-rv.is-in { animation:hbPdUp .9s var(--d,0s) cubic-bezier(.2,.7,.2,1) backwards; }

        .hb-pd-progress { position:fixed; z-index:9999; top:0; left:0; right:0; height:2px; pointer-events:none; }
        .hb-pd-progress span { display:block; width:100%; height:100%; background:linear-gradient(90deg,#d8b45e,#f0d69a); transform:scaleX(0); transform-origin:left; }

        /* ====== RESPONSIVE ====== */
        @media (max-width:900px){
            .hb-pd-layout { grid-template-columns:1fr; gap:40px; margin-bottom:60px; }
            .hb-pd-side { position:static; }
            .hb-pd-meta dl div { padding:16px 16px; }
        }
        @media (max-width:620px){
            .hb-pd-hero { min-height:64svh; }
            .hb-pd-hero-inner { padding-top:100px; padding-bottom:34px; }
            .hb-pd-meta dl { grid-template-columns:1fr 1fr; }
            .hb-pd-meta dl div { padding:14px 12px; border-left:0; border-top:1px solid rgba(216,180,94,.15); }
            .hb-pd-meta dl div:nth-child(-n+2) { border-top:0; }
            .hb-pd-section { padding:56px 0 70px; }
            .hb-pd-gallery { grid-template-columns:1fr; gap:14px; }
            .hb-pd-shot.is-first { aspect-ratio:16/10; }
            .hb-pd-nav { grid-template-columns:1fr; margin-top:56px; }
            .hb-pd-nav-img { flex-basis:88px; height:58px; }
            .hb-pd-text p.is-lead { font-size:19px; }
            .hb-lb { padding:60px 10px 120px; }
            .hb-lb-nav { display:none; }
            .hb-pd-shot-ico, .hb-pd-shot small { opacity:1; transform:none; }
        }

        @media (prefers-reduced-motion:reduce){
            .hb-pd-rv:not(.is-in) { opacity:1; }
            .hb-pd-in, .hb-pd-rv, .hb-pd-hero .hb-eyebrow i { animation:none !important; }
            .hb-pd-shot img, .hb-pd-btn, .hb-pd-nav a { transition:none; }
        }
    </style>
    <noscript><style>.hb-pd-rv:not(.is-in){opacity:1 !important}</style></noscript>
</x-hb-layout>