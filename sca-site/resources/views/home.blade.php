@php
    $c = config('hnbloom');
    $img = fn ($local, $fallback = null) => ($local && file_exists(public_path($local))) ? asset($local) : $fallback;
    $heroVideo = (!empty($c['hero_video']) && file_exists(public_path($c['hero_video']))) ? asset($c['hero_video']) : null;
    $heroPoster = $img($c['hero_poster'] ?? null);
    $introVideo = (!empty($c['intro']['video']) && file_exists(public_path($c['intro']['video']))) ? asset($c['intro']['video']) : null;
    $introPoster = $img($c['intro']['poster'] ?? null);
    $introOnce = (bool) ($c['intro']['once_per_session'] ?? true);

    $homeBg = $img($c['images']['hero'] ?? null) ?: $heroPoster;

    $firstFile = function (array $paths) {
        foreach ($paths as $p) {
            if ($p && file_exists(public_path($p))) return asset($p);
        }
        return null;
    };

    $videoFor = function (array $s) use ($firstFile) {
        $candidates = [];
        if (!empty($s['video'])) $candidates[] = $s['video'];

        if (!empty($s['image'])) {
            $info = pathinfo($s['image']);
            $dir = ($info['dirname'] && $info['dirname'] !== '.') ? $info['dirname'].'/' : '';
            $candidates[] = $dir.$info['filename'].'.mp4';
            $candidates[] = 'videos/'.$info['filename'].'.mp4';
        }

        if (!empty($s['href'])) {
            $slug = basename(rtrim(parse_url($s['href'], PHP_URL_PATH) ?: $s['href'], '/'));
            if ($slug) {
                $candidates[] = 'videos/'.$slug.'.mp4';
                $candidates[] = 'videos/solutions/'.$slug.'.mp4';
                $candidates[] = 'images/solutions/'.$slug.'.mp4';
                $candidates[] = 'images/'.$slug.'.mp4';
                $candidates[] = 'solutions/'.$slug.'.mp4';
            }
        }
        return $firstFile($candidates);
    };

    // ===== DOMOTIQUE (5ème carte) =====
    $d = config('hnbloom.domotique');
    $dmIndexUrl = \Illuminate\Support\Facades\Route::has('domotique.index') ? route('domotique.index') : url('domotique');
    $dmIcon = $d['icon'] ?? ($d['markets'][0]['icon'] ?? 'home');
    $dmImagePath = $d['image'] ?? ($c['images']['solutions'] ?? null);
    $dmVideo = $videoFor(['image' => $dmImagePath, 'href' => 'domotique', 'video' => $d['bg_video'] ?? null])
        ?: $firstFile([$c['solutions_video'] ?? null]);
    $dmPoster = $img($dmImagePath, $d['fallback'] ?? ($d['markets'][0]['fallback'] ?? null))
        ?: $firstFile([$d['bg_poster'] ?? null, $c['hero_poster'] ?? null]);
    $dmText = \Illuminate\Support\Str::limit($d['card_text'] ?? 'Smart Home, Smart Hotel et Smart Hospital avec SMART G4 CONTROL.', 62);
@endphp
<x-hb-layout :title="null" :description="$c['tagline']">

@if($introVideo && $introOnce)
    @push('head')
    <script>try { if (sessionStorage.getItem('hn-intro-seen')) document.documentElement.classList.add('hb-skip-intro'); } catch (e) {}</script>
    @endpush
@endif

@if($introVideo)
<div class="hb-intro" x-data="hbIntro({{ $introOnce ? 'true' : 'false' }})" x-show="intro"
     x-transition.opacity.duration.700ms @click="canEnter = true">
    <video x-ref="introVideo" class="hb-intro-video" src="{{ $introVideo }}"
           @if($introPoster) poster="{{ $introPoster }}" @endif
           autoplay muted playsinline preload="auto"
           x-init="$el.play?.().catch(() => canEnter = true)"
           x-on:ended="canEnter = true" x-on:error="canEnter = true"></video>
    <div class="hb-intro-shade"></div>
    <div class="hb-intro-ui">
        <button type="button" x-show="canEnter" x-transition.opacity.duration.500ms
                class="hb-button hb-button-gold hb-intro-enter" @click.stop="enter()">
            Entrer <x-icon name="arrow-right" :size="16" />
        </button>
        <p x-show="!canEnter" class="hb-intro-hint">Cliquez pour continuer</p>
    </div>
    <button type="button" class="hb-intro-sound" @click.stop="toggleSound()"
            :aria-label="muted ? 'Activer le son' : 'Couper le son'" x-text="muted ? 'Son : off' : 'Son : on'"></button>
</div>
<script>
function hbIntro(once) {
    return {
        intro: true, canEnter: false, muted: true,
        init() {
            try { if (once && sessionStorage.getItem('hn-intro-seen')) this.intro = false; } catch (e) {}
            document.documentElement.classList.toggle('hb-intro-lock', this.intro);
        },
        enter() {
            this.intro = false;
            document.documentElement.classList.remove('hb-intro-lock');
            try { sessionStorage.setItem('hn-intro-seen', '1'); } catch (e) {}
            this.$refs.introVideo?.pause();
        },
        toggleSound() {
            const v = this.$refs.introVideo;
            if (!v) return;
            v.muted = !v.muted;
            this.muted = v.muted;
        },
    };
}
</script>
@endif

<div class="hb-home-page">

<div class="hb-progress" aria-hidden="true"><span></span></div>

@if($homeBg)
    <div class="hb-home-bg" style="background-image:url('{{ $homeBg }}')" aria-hidden="true"></div>
@endif

{{-- HERO : vidéo en fond, les 2 barres + la liste sont à l'intérieur --}}
<section id="accueil" class="hb-hero">
    @if($heroVideo)
        <video class="hb-hero-video" autoplay muted loop playsinline preload="auto" disablepictureinpicture
               @if($heroPoster) poster="{{ $heroPoster }}" @endif>
            <source src="{{ $heroVideo }}" type="video/mp4">
        </video>
    @elseif($heroPoster)
        <div class="hb-hero-video" style="background:url('{{ $heroPoster }}') center/cover no-repeat"></div>
    @endif
    <div class="hb-hero-shade"></div>

    {{-- Barre du haut --}}
    @include('partials.hb-brandbar', ['position' => 'top'])

    {{-- Texte --}}
    <div class="hb-hero-content">
        <p class="hb-eyebrow hb-rise" style="--d:0s">SMART BUILDING · SÉCURITÉ · AUTOMATION</p>
        <h1 class="hb-rise" style="--d:.12s">
            <span class="hb-white">Des solutions de sécurité intelligentes pour un</span>
            <span class="hb-gold">avenir plus sûr.</span>
        </h1>
        <p class="hb-hero-subtitle hb-rise" style="--d:.24s">
            {{ $c['name'] }} est le distributeur officiel de ZKTeco et G4 au Maroc, pour des solutions de contrôle d'accès, de sécurité électronique et d'automatisation des bâtiments.
        </p>
        <div class="hb-hero-actions hb-rise" style="--d:.36s">
            <a class="hb-button hb-button-gold" href="{{ route('solutions.index') }}">Découvrir nos solutions <x-icon name="arrow-right" :size="16" /></a>
            <a class="hb-button hb-button-outline" href="{{ route('contact', ['sujet' => 'partner']) }}">Devenir partenaire <x-icon name="arrow-right" :size="16" /></a>
        </div>
        <p class="hb-location hb-rise" style="--d:.48s"><x-icon name="map-pin" :size="14" /> Casablanca · Maroc</p>
    </div>

    {{-- Liste des 4 atouts (milieu, à droite) --}}

    {{-- Barre du bas --}}
    @include('partials.hb-brandbar', ['position' => 'bottom'])
</section>

{{-- BANDEAU --}}
<div class="hb-service-strip" aria-label="Nos domaines">
    @foreach(array_slice($c['solutions'], 0, 4) as $s)
        <a class="hb-strip-item" href="{{ url($s['href']) }}"><x-icon :name="$s['icon']" :size="23" /><span>{{ $s['title'] }}</span></a>
    @endforeach
    <a class="hb-strip-item" href="{{ $dmIndexUrl }}"><x-icon :name="$dmIcon" :size="23" /><span>Domotique</span></a>
</div>

{{-- SOLUTIONS --}}
<section id="solutions" class="hb-section hb-solutions-section">
    <div class="hb-section-intro">
        <p class="hb-eyebrow hb-rv" style="--d:0s">NOS SOLUTIONS</p>
        <h2 class="hb-rv" style="--d:.1s">Une sécurité complète<br>pour vos espaces</h2>
        <p class="hb-rv" style="--d:.2s">Des solutions innovantes et fiables pour la protection, le confort et la gestion intelligente de vos bâtiments.</p>
        <a class="hb-text-link hb-rv" style="--d:.3s" href="{{ route('solutions.index') }}">Découvrir nos solutions <x-icon name="arrow-right" :size="15" /></a>
    </div>

    <div class="hb-solution-grid">
        @foreach(array_slice($c['solutions'], 0, 4) as $i => $s)
            @php
                $sVideo = $videoFor($s);
                $sImage = $img($s['image'] ?? null, $s['fallback'] ?? null);
            @endphp
            <a href="{{ url($s['href']) }}" class="hb-solution-card hb-rv" style="--d:{{ $i * 0.12 }}s">
                <i class="hb-card-line" aria-hidden="true"></i>
                <i class="hb-card-halo" aria-hidden="true"></i>
                <div class="hb-solution-image">
                    @if($sVideo)
                        <video class="hb-vid" muted loop playsinline preload="none" disablepictureinpicture
                               @if($sImage) poster="{{ $sImage }}" @endif>
                            <source src="{{ $sVideo }}" type="video/mp4">
                        </video>
                    @else
                        <x-hb-photo :src="$sImage" :alt="$s['title']" :label="$s['title']" />
                    @endif
                    <div class="hb-image-shade"></div>
                    <span class="hb-shine" aria-hidden="true"></span>
                </div>
                <div class="hb-solution-info">
                    <x-icon :name="$s['icon']" :size="20" :stroke="1.5" />
                    <h3>{{ $s['title'] }}</h3>
                    <p>{{ $s['text'] }}</p>
                </div>
            </a>
        @endforeach

        <a href="{{ $dmIndexUrl }}" id="domotique" class="hb-solution-card hb-rv" style="--d:.48s">
            <i class="hb-card-line" aria-hidden="true"></i>
            <i class="hb-card-halo" aria-hidden="true"></i>
            <div class="hb-solution-image">
                @if($dmVideo)
                    <video class="hb-vid" muted loop playsinline preload="none" disablepictureinpicture
                           @if($dmPoster) poster="{{ $dmPoster }}" @endif>
                        <source src="{{ $dmVideo }}" type="video/mp4">
                    </video>
                @else
                    <x-hb-photo :src="$dmPoster" alt="Domotique" label="Domotique" />
                @endif
                <div class="hb-image-shade"></div>
                <span class="hb-shine" aria-hidden="true"></span>
            </div>
            <div class="hb-solution-info">
                <x-icon :name="$dmIcon" :size="20" :stroke="1.5" />
                <h3>Domotique</h3>
                <p>{{ $dmText }}</p>
            </div>
        </a>
    </div>
</section>

{{-- MÉTIER --}}
<section class="hb-process">
    <div class="hb-process-heading">
        <p class="hb-eyebrow hb-rv" style="--d:0s">NOTRE MÉTIER</p>
        <h2 class="hb-rv" style="--d:.1s">De l’étude à la<br>mise en service</h2>
    </div>
    <div class="hb-process-steps">
        @foreach($c['steps'] as $i => $s)
            <div class="hb-step hb-rv" style="--d:{{ $i * 0.15 }}s">
                <div class="hb-step-number">{{ $s['n'] }}</div>
                <div class="hb-step-line"><span></span></div>
                <h3>{{ $s['title'] }}</h3>
                <p>{{ $s['text'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- À PROPOS / PROJETS / CONTACT --}}
<section id="apropos" class="hb-about">
    <div class="hb-about-image hb-rv hb-rv-clip">
        <x-hb-photo :src="$img($c['images']['about'], $c['about']['fallback'])" alt="Architecture moderne et domotique" label="Architecture {{ $c['name'] }}" />
    </div>

    <div class="hb-about-copy">
        <p class="hb-eyebrow hb-rv" style="--d:0s">À PROPOS</p>
        <h2 class="hb-rv" style="--d:.1s">{{ $c['name'] }}</h2>
        @foreach($c['about']['lines'] as $li => $line)<p class="hb-rv" style="--d:{{ .2 + $li * 0.1 }}s">{{ $line }}</p>@endforeach
        <a class="hb-button hb-button-outline hb-rv" style="--d:.45s" href="{{ route('about') }}">En savoir plus <x-icon name="arrow-right" :size="15" /></a>
    </div>

    <div id="projets" class="hb-projects">
        <p class="hb-eyebrow hb-rv" style="--d:0s">NOS PROJETS</p>
        <h2 class="hb-rv" style="--d:.1s">Réalisations</h2>
        <div class="hb-project-grid">
            @foreach($c['projects'] as $i => $p)
                <div class="hb-project hb-rv" style="--d:{{ $i * 0.12 }}s">
                    <x-hb-photo :src="$img($p['image'], $p['fallback'])" :alt="'Projet '.$c['name'].' '.($i + 1)" :label="'Projet '.($i + 1)" />
                    <span>{{ $p['title'] }}</span>
                    <small>{{ $p['city'] }}</small>
                </div>
            @endforeach
        </div>
        <a class="hb-text-link hb-rv" style="--d:.2s" href="{{ route('projects') }}">Voir tous les projets <x-icon name="arrow-right" :size="15" /></a>
    </div>

    <div id="contact" class="hb-contact">
        <p class="hb-eyebrow hb-rv" style="--d:0s">CONTACT</p>
        <h2 class="hb-rv" style="--d:.1s">Parlons de votre projet</h2>
        <div class="hb-contact-list hb-rv" style="--d:.2s">
            @foreach($c['contact']['phones'] as $p)
                <a href="tel:{{ preg_replace('/\s+/', '', $p) }}"><x-icon name="phone" :size="15" />{{ $p }}</a>
            @endforeach
            <a href="mailto:{{ $c['contact']['email'] }}"><x-icon name="mail" :size="15" />{{ $c['contact']['email'] }}</a>
            <p><x-icon name="map-pin" :size="15" />{{ $c['contact']['address'] }}</p>
        </div>
        <a class="hb-button hb-button-gold hb-rv" style="--d:.32s" href="{{ route('contact') }}">Nous contacter <x-icon name="arrow-right" :size="15" /></a>
    </div>
</section>

</div>{{-- /hb-home-page --}}

<script>
    (function () {
        var reduce = window.matchMedia('(prefers-reduced-motion:reduce)').matches;

        // 1) Apparition au scroll
        var els = document.querySelectorAll('.hb-rv');
        if (!('IntersectionObserver' in window) || reduce) {
            els.forEach(function (el) { el.classList.add('is-in'); });
        } else {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
            els.forEach(function (el) { io.observe(el); });
        }

        // 2) Vidéos visibles uniquement
        var vids = document.querySelectorAll('video.hb-hero-video, .hb-vid');
        var saver = navigator.connection && navigator.connection.saveData;
        vids.forEach(function (v) { v.muted = true; });
        if (saver) {
            vids.forEach(function (v) { v.removeAttribute('autoplay'); v.pause(); });
        } else if ('IntersectionObserver' in window) {
            var vio = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    var v = e.target;
                    if (e.isIntersecting) { var p = v.play(); if (p && p.catch) p.catch(function () {}); }
                    else { v.pause(); }
                });
            }, { threshold: 0.15 });
            vids.forEach(function (v) { vio.observe(v); });
        } else {
            vids.forEach(function (v) { var p = v.play && v.play(); if (p && p.catch) p.catch(function () {}); });
        }

        // 3) Barre de progression
        var bar = document.querySelector('.hb-progress span');
        if (bar && !reduce) {
            var ticking = false;
            function upd() {
                var h = document.documentElement.scrollHeight - window.innerHeight;
                bar.style.transform = 'scaleX(' + (h > 0 ? Math.min(window.scrollY / h, 1) : 0) + ')';
                ticking = false;
            }
            window.addEventListener('scroll', function () {
                if (!ticking) { ticking = true; requestAnimationFrame(upd); }
            }, { passive: true });
            upd();
        }

        // 4) Inclinaison 3D + halo sur les cartes
        if (!reduce && window.matchMedia('(hover:hover) and (pointer:fine)').matches) {
            document.querySelectorAll('.hb-solution-card').forEach(function (card) {
                var raf = 0, ev = null;
                function apply() {
                    raf = 0;
                    var r = card.getBoundingClientRect();
                    var x = (ev.clientX - r.left) / r.width;
                    var y = (ev.clientY - r.top) / r.height;
                    card.style.setProperty('--mx', (x * 100) + '%');
                    card.style.setProperty('--my', (y * 100) + '%');
                    card.style.setProperty('--ry', ((x - .5) * 7) + 'deg');
                    card.style.setProperty('--rx', ((.5 - y) * 7) + 'deg');
                }
                card.addEventListener('pointermove', function (e) { ev = e; if (!raf) raf = requestAnimationFrame(apply); }, { passive: true });
                card.addEventListener('pointerleave', function () {
                    card.style.setProperty('--rx', '0deg');
                    card.style.setProperty('--ry', '0deg');
                });
            });
        }
    })();
</script>
<noscript><style>.hb-rv:not(.is-in){opacity:1 !important}</style></noscript>

<style>
    /* ====== FOND DE LA PAGE ====== */
    .hb-home-page { position: relative; isolation: isolate; clip-path: inset(0); background: #090908; }
    .hb-home-bg {
        position: fixed; inset: 0; z-index: -1;
        background-position: center; background-size: cover; background-repeat: no-repeat;
    }
    .hb-home-bg::after {
        content: ""; position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(9,9,8,.62) 0%, rgba(9,9,8,.82) 100%);
    }
    .hb-home-page .hb-service-strip,
    .hb-home-page .hb-solutions-section,
    .hb-home-page .hb-process,
    .hb-home-page .hb-about { background: rgba(9,9,8,.45) !important; }

    /* ====== TYPOGRAPHIE ====== */
    .hb-home-page { font-family: Arial, Helvetica, sans-serif; }
    .hb-home-page .hb-eyebrow {
        font: 700 10.5px/1.5 Arial, Helvetica, sans-serif !important;
        letter-spacing: 4px !important; text-transform: uppercase;
    }
    .hb-home-page .hb-section-intro h2,
    .hb-home-page .hb-process-heading h2,
    .hb-home-page .hb-about-copy h2,
    .hb-home-page .hb-projects h2,
    .hb-home-page .hb-contact h2 {
        font-family: Georgia, "Times New Roman", serif !important; font-weight: 400 !important;
        font-size: clamp(34px, 4vw, 49px) !important; line-height: 1.12 !important; letter-spacing: -.7px !important;
    }
    .hb-home-page .hb-solution-info h3,
    .hb-home-page .hb-step h3 { font-family: Georgia, "Times New Roman", serif !important; font-weight: 400 !important; }
    .hb-home-page .hb-section-intro p,
    .hb-home-page .hb-about-copy p,
    .hb-home-page .hb-step p,
    .hb-home-page .hb-contact-list a,
    .hb-home-page .hb-contact-list p { font-family: Arial, Helvetica, sans-serif !important; }
    .hb-home-page .hb-section-intro p,
    .hb-home-page .hb-about-copy p { font-size: 13px !important; line-height: 1.95 !important; }
    .hb-home-page .hb-button,
    .hb-home-page .hb-text-link {
        font-family: Arial, Helvetica, sans-serif !important; font-size: 11px !important; font-weight: 700 !important;
        letter-spacing: 2px !important; text-transform: uppercase;
    }

    /* ====== HERO : plein écran, vidéo en fond ====== */
    .hb-home-page .hb-hero {
        position: relative;
        height: 100vh !important;
        height: 100svh !important;
        min-height: 640px !important;
        padding: 68px 0 !important;           /* réserve la place des 2 barres */
        display: flex !important;
        flex-direction: row;
        align-items: center;                   /* texte + liste au milieu */
        justify-content: flex-start;
        overflow: hidden;
    }
    .hb-home-page .hb-hero-video,
    .hb-home-page .hb-hero-shade {
        position: absolute !important; inset: 0; width: 100%; height: 100%;
    }
    .hb-home-page .hb-hero-video { object-fit: cover; z-index: 0; will-change: transform; }
    .hb-home-page .hb-hero-shade {
        z-index: 1; pointer-events: none;
        background:
            linear-gradient(90deg, rgba(8,7,6,.82) 0%, rgba(8,7,6,.45) 50%, rgba(8,7,6,.15) 100%),
            linear-gradient(180deg, rgba(8,7,6,.35) 0%, transparent 25%, transparent 75%, rgba(8,7,6,.45) 100%);
    }
    .hb-home-page .hb-hero-content {
        position: relative; z-index: 3;
        flex: 0 1 640px; max-width: 55%;
        margin: 0; padding: 20px 0 20px 6%;
    }
    .hb-home-page .hb-hero h1 {
        font-family: Georgia, "Times New Roman", serif !important;
        font-weight: 400 !important;
        font-size: clamp(30px, 3.6vw, 54px) !important;
        line-height: 1.15 !important; letter-spacing: -.6px !important;
        color: #fff !important; margin: 14px 0 22px; max-width: 100%;
    }
    .hb-home-page .hb-hero h1 .hb-white { display: block; color: #fff !important; }
    .hb-home-page .hb-hero h1 .hb-gold  { display: block; color: #e9c46f !important; }
    .hb-home-page .hb-hero-subtitle {
        font: 400 13.5px/1.9 Arial, Helvetica, sans-serif !important;
        max-width: 520px; color: #f1ece1;
    }
    .hb-home-page .hb-hero-actions { display: flex; flex-wrap: wrap; gap: 16px; margin: 26px 0 22px; }

    @media (max-width: 1100px) {
        .hb-home-page .hb-hero {
            height: auto !important; min-height: 100svh !important;
            flex-direction: column; justify-content: center; align-items: stretch;
            padding: 68px 0 !important;
        }
        .hb-home-page .hb-hero-content { flex: none; max-width: 100%; width: 100%; padding: 50px 6% 20px; }
    }

    /* ====== BANDEAU : 5 éléments ====== */
    @media (min-width: 901px) {
        .hb-home-page .hb-service-strip { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); }
    }

    /* ====== NOS SOLUTIONS : 5 cartes sur une ligne ====== */
    .hb-home-page .hb-solutions-section {
        display: block !important; min-height: 0 !important; height: auto !important;
        padding: clamp(40px, 5vw, 64px) 5% clamp(44px, 5vw, 64px) !important;
    }
    .hb-home-page .hb-solutions-section .hb-section-intro { max-width: 640px; margin: 0 0 28px !important; padding: 0 !important; }
    .hb-home-page .hb-solutions-section .hb-section-intro h2 { margin: 10px 0 12px; }
    .hb-home-page .hb-solutions-section .hb-section-intro p { margin-top: 0; margin-bottom: 12px; }
    .hb-home-page .hb-solutions-section .hb-eyebrow { margin-bottom: 0; }

    .hb-home-page .hb-solution-grid {
        display: grid !important; grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        gap: 14px; width: 100%; margin: 0 !important; align-items: stretch;
    }
    .hb-home-page .hb-solution-card { height: auto !important; min-height: 0 !important; }
    .hb-home-page .hb-solution-info { padding: 18px 16px 20px; }
    .hb-home-page .hb-solution-info h3 { font-size: clamp(16px, 1.5vw, 19px); }
    .hb-home-page .hb-solution-info p { font-size: 12px; line-height: 1.8; }

    @media (max-width: 1100px) and (min-width: 701px) {
        .hb-home-page .hb-solution-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
    }
    @media (max-width: 700px) {
        .hb-home-page .hb-solution-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 12px; }
    }

    .hb-home-page .hb-solution-image { position: relative; overflow: hidden; height: clamp(150px, 14vw, 200px); }
    .hb-home-page .hb-solution-image img,
    .hb-home-page .hb-solution-image .hb-vid {
        display: block; width: 100%; height: 100%; object-fit: cover;
        transition: transform .8s cubic-bezier(.2,.7,.2,1), filter .4s;
    }
    .hb-home-page .hb-solution-image .hb-image-shade { pointer-events: none; }

    /* ====== ANIMATIONS ====== */
    html { scroll-behavior: smooth; }

    @keyframes hbRise    { from { opacity:0; transform:translateY(46px) scale(.97); } to { opacity:1; transform:none; } }
    @keyframes hbClip    { from { clip-path:inset(0 0 100% 0); } to { clip-path:inset(0 0 0 0); } }
    @keyframes hbClipL   { from { clip-path:inset(0 100% 0 0); } to { clip-path:inset(0 0 0 0); } }
    @keyframes hbImgIn   { from { transform:scale(1.25); } to { transform:scale(1); } }
    @keyframes hbRule    { from { transform:scaleX(0); } to { transform:scaleX(1); } }

    .hb-rv:not(.is-in) { opacity: 0; }
    .hb-rv.is-in { animation: hbRise 1.1s var(--d, 0s) cubic-bezier(.2,.7,.2,1) backwards; }
    .hb-rv.hb-rv-clip.is-in { animation: hbClipL 1.5s var(--d, 0s) cubic-bezier(.7,0,.2,1) backwards; }

    .hb-progress { position: fixed; z-index: 9999; top: 0; left: 0; right: 0; height: 2px; pointer-events: none; }
    .hb-progress span { display: block; width: 100%; height: 100%; background: linear-gradient(90deg, #d6ad55, #f1d58e); transform: scaleX(0); transform-origin: left; }

    .hb-home-page .hb-button { position: relative; overflow: hidden; }
    .hb-home-page .hb-button::after { content: ""; position: absolute; top: 0; bottom: 0; left: -70%; width: 40%; pointer-events: none; background: linear-gradient(100deg, transparent, rgba(255,244,214,.35), transparent); transform: skewX(-18deg); }
    .hb-home-page .hb-button:hover::after { left: 130%; transition: left .9s ease; }

    .hb-home-page .hb-strip-item { transition: color .3s, transform .4s ease; }
    .hb-home-page .hb-strip-item svg { transition: transform .5s ease; }
    .hb-home-page .hb-strip-item:hover { transform: translateY(-3px); }
    .hb-home-page .hb-strip-item:hover svg { transform: scale(1.15) rotate(-6deg); }

    .hb-home-page .hb-solution-card {
        position: relative;
        transform: perspective(1100px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg));
        transition: transform .35s ease-out, box-shadow .5s, border-color .4s;
    }
    .hb-home-page .hb-solution-card:hover {
        transform: perspective(1100px) translateY(-8px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg));
        box-shadow: 0 30px 60px -20px rgba(0,0,0,.7), 0 0 40px -10px rgba(214,173,85,.35);
    }
    .hb-card-halo { position: absolute; inset: 0; z-index: 2; pointer-events: none; opacity: 0; transition: opacity .4s; background: radial-gradient(380px circle at var(--mx, 50%) var(--my, 50%), rgba(241,213,142,.15), transparent 60%); }
    .hb-solution-card:hover .hb-card-halo { opacity: 1; }
    .hb-card-line { position: absolute; z-index: 3; top: 0; left: 0; right: 0; height: 2px; pointer-events: none; background: linear-gradient(90deg, transparent, #f1d58e, transparent); transform: scaleX(0); transition: transform .6s cubic-bezier(.2,.7,.2,1); }
    .hb-solution-card:hover .hb-card-line { transform: scaleX(1); }

    .hb-home-page .hb-solution-card.is-in .hb-solution-image { animation: hbClip 1.3s calc(var(--d, 0s) + .15s) cubic-bezier(.7,0,.2,1) backwards; }
    .hb-home-page .hb-solution-card.is-in .hb-solution-image img,
    .hb-home-page .hb-solution-card.is-in .hb-solution-image .hb-vid { animation: hbImgIn 1.8s calc(var(--d, 0s) + .15s) cubic-bezier(.2,.7,.2,1) backwards; }
    .hb-home-page .hb-solution-card:hover .hb-solution-image img,
    .hb-home-page .hb-solution-card:hover .hb-solution-image .hb-vid { transform: scale(1.06); filter: brightness(1.06); }
    .hb-shine { position: absolute; z-index: 2; top: 0; bottom: 0; left: -70%; width: 45%; pointer-events: none; background: linear-gradient(100deg, transparent, rgba(255,244,214,.28), transparent); transform: skewX(-18deg); }
    .hb-solution-card:hover .hb-shine { left: 130%; transition: left 1s ease; }
    .hb-home-page .hb-solution-info svg { transition: transform .5s ease, color .3s; }
    .hb-home-page .hb-solution-card:hover .hb-solution-info svg { transform: scale(1.2) rotate(-6deg); }
    .hb-home-page .hb-solution-info h3 { transition: letter-spacing .5s, color .3s; }
    .hb-home-page .hb-solution-card:hover .hb-solution-info h3 { letter-spacing: .4px; }

    .hb-home-page .hb-step.is-in .hb-step-line span { transform-origin: left; animation: hbRule 1.3s calc(var(--d, 0s) + .4s) ease-out backwards; }
    .hb-home-page .hb-step-number { transition: transform .5s ease, color .3s; }
    .hb-home-page .hb-step:hover .hb-step-number { transform: translateY(-4px); }

    .hb-home-page .hb-about-image { overflow: hidden; }
    .hb-home-page .hb-about-image img { transition: transform 1.4s cubic-bezier(.2,.7,.2,1); }
    .hb-home-page .hb-about-image:hover img { transform: scale(1.04); }
    .hb-home-page .hb-project { overflow: hidden; }
    .hb-home-page .hb-project img { transition: transform .9s cubic-bezier(.2,.7,.2,1), filter .4s; }
    .hb-home-page .hb-project:hover img { transform: scale(1.06); filter: brightness(1.06); }
    .hb-home-page .hb-contact-list a { transition: color .3s, transform .3s; }
    .hb-home-page .hb-contact-list a:hover { transform: translateX(5px); }

    @media (max-width: 760px) {
        .hb-home-page .hb-hero-video { will-change: auto; }
        .hb-home-page .hb-solution-card { transform: none !important; }
    }
    @media (prefers-reduced-motion: reduce) {
        .hb-rv:not(.is-in) { opacity: 1; }
        .hb-rv { animation: none !important; }
        .hb-home-page .hb-solution-card { transform: none !important; }
        html { scroll-behavior: auto; }
    }
</style>

</x-hb-layout>
