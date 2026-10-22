@php
    $c = config('hnbloom');
    $img = fn ($local, $fallback = null) => ($local && file_exists(public_path($local))) ? asset($local) : $fallback;
    $heroVideo = (!empty($c['hero_video']) && file_exists(public_path($c['hero_video']))) ? asset($c['hero_video']) : null;
    $heroPoster = $img($c['hero_poster'] ?? null);
    $introVideo = (!empty($c['intro']['video']) && file_exists(public_path($c['intro']['video']))) ? asset($c['intro']['video']) : null;
    $introPoster = $img($c['intro']['poster'] ?? null);
    $introOnce = (bool) ($c['intro']['once_per_session'] ?? true);

    // Image utilisée comme fond de la page d'accueil (changez ici pour une autre image)
    $homeBg = $img($c['images']['hero'] ?? null) ?: $heroPoster;

    $firstFile = function (array $paths) {
        foreach ($paths as $p) {
            if ($p && file_exists(public_path($p))) return asset($p);
        }
        return null;
    };

    // Cherche la vidéo d'une solution :
    // 1) clé 'video' dans la config  2) même nom que l'image en .mp4
    // 3) nom de la page (slug) dans videos/, images/solutions/, videos/solutions/ ...
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

    // ===== DOMOTIQUE (5ème carte de "Nos solutions") =====
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

@if($homeBg)
    {{-- FOND DE LA PAGE D'ACCUEIL --}}
    <div class="hb-home-bg" style="background-image:url('{{ $homeBg }}')" aria-hidden="true"></div>
@endif

{{-- HERO --}}
<section id="accueil" class="hb-hero">
    @if($heroVideo)
        <video class="hb-hero-video" autoplay muted loop playsinline preload="auto" disablepictureinpicture
               @if($heroPoster) poster="{{ $heroPoster }}" @endif>
            <source src="{{ $heroVideo }}" type="video/mp4">
        </video>
    @endif
    <div class="hb-hero-shade"></div>
    <div class="hb-hero-content">
        <p class="hb-eyebrow hb-rise" style="--d:0s">SMART BUILDING · SÉCURITÉ · AUTOMATION</p>
        <h1 class="hb-rise" style="--d:.12s">{{ $c['name'] }}</h1>
        <p class="hb-hero-subtitle hb-rise" style="--d:.24s">{{ $c['tagline'] }}</p>
        <div class="hb-hero-actions hb-rise" style="--d:.36s">
            <a class="hb-button hb-button-gold" href="{{ route('solutions.index') }}">Nos solutions <x-icon name="arrow-right" :size="16" /></a>
            <a class="hb-button hb-button-outline" href="{{ route('contact', ['sujet' => 'quote']) }}">Demander un devis</a>
        </div>
        <p class="hb-location hb-rise" style="--d:.48s"><x-icon name="map-pin" :size="14" /> Casablanca · Maroc</p>
    </div>
    <div class="hb-hero-index"><span>01</span><i></i><span>04</span></div>
</section>

{{-- BANDEAU (4 solutions + Domotique) --}}
<div class="hb-service-strip" aria-label="Nos domaines">
    @foreach(array_slice($c['solutions'], 0, 4) as $s)
        <a class="hb-strip-item" href="{{ url($s['href']) }}"><x-icon :name="$s['icon']" :size="23" /><span>{{ $s['title'] }}</span></a>
    @endforeach
    <a class="hb-strip-item" href="{{ $dmIndexUrl }}"><x-icon :name="$dmIcon" :size="23" /><span>Domotique</span></a>
</div>

{{-- SOLUTIONS --}}
<section id="solutions" class="hb-section hb-solutions-section">
    <div class="hb-section-intro">
        <p class="hb-eyebrow">NOS SOLUTIONS</p>
        <h2>Une sécurité complète<br>pour vos espaces</h2>
        <p>Des solutions innovantes et fiables pour la protection, le confort et la gestion intelligente de vos bâtiments.</p>
        <a class="hb-text-link" href="{{ route('solutions.index') }}">Découvrir nos solutions <x-icon name="arrow-right" :size="15" /></a>
    </div>

    <div class="hb-solution-grid">
        @foreach(array_slice($c['solutions'], 0, 4) as $i => $s)
            @php
                $sVideo = $videoFor($s);
                $sImage = $img($s['image'] ?? null, $s['fallback'] ?? null);
            @endphp
            <a href="{{ url($s['href']) }}" class="hb-solution-card reveal" style="--d:{{ $i * 0.07 }}s">
                <div class="hb-solution-image">
                    @if($sVideo)
                        <video class="hb-vid" autoplay muted loop playsinline preload="auto" disablepictureinpicture
                               @if($sImage) poster="{{ $sImage }}" @endif>
                            <source src="{{ $sVideo }}" type="video/mp4">
                        </video>
                    @else
                        <x-hb-photo :src="$sImage" :alt="$s['title']" :label="$s['title']" />
                    @endif
                    <div class="hb-image-shade"></div>
                </div>
                <div class="hb-solution-info">
                    <x-icon :name="$s['icon']" :size="20" :stroke="1.5" />
                    <h3>{{ $s['title'] }}</h3>
                    <p>{{ $s['text'] }}</p>
                </div>
            </a>
        @endforeach

        {{-- 5ème carte : DOMOTIQUE --}}
        <a href="{{ $dmIndexUrl }}" id="domotique" class="hb-solution-card reveal" style="--d:.28s">
            <div class="hb-solution-image">
                @if($dmVideo)
                    <video class="hb-vid" autoplay muted loop playsinline preload="auto" disablepictureinpicture
                           @if($dmPoster) poster="{{ $dmPoster }}" @endif>
                        <source src="{{ $dmVideo }}" type="video/mp4">
                    </video>
                @else
                    <x-hb-photo :src="$dmPoster" alt="Domotique" label="Domotique" />
                @endif
                <div class="hb-image-shade"></div>
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
        <p class="hb-eyebrow">NOTRE MÉTIER</p>
        <h2>De l’étude à la<br>mise en service</h2>
    </div>
    <div class="hb-process-steps">
        @foreach($c['steps'] as $i => $s)
            <div class="hb-step reveal" style="--d:{{ $i * 0.1 }}s">
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
    <div class="hb-about-image">
        <x-hb-photo :src="$img($c['images']['about'], $c['about']['fallback'])" alt="Architecture moderne et domotique" label="Architecture {{ $c['name'] }}" />
    </div>

    <div class="hb-about-copy">
        <p class="hb-eyebrow">À PROPOS</p>
        <h2>{{ $c['name'] }}</h2>
        @foreach($c['about']['lines'] as $line)<p>{{ $line }}</p>@endforeach
        <a class="hb-button hb-button-outline" href="{{ route('about') }}">En savoir plus <x-icon name="arrow-right" :size="15" /></a>
    </div>

    <div id="projets" class="hb-projects">
        <p class="hb-eyebrow">NOS PROJETS</p>
        <h2>Réalisations</h2>
        <div class="hb-project-grid">
            @foreach($c['projects'] as $i => $p)
                <div class="hb-project">
                    <x-hb-photo :src="$img($p['image'], $p['fallback'])" :alt="'Projet '.$c['name'].' '.($i + 1)" :label="'Projet '.($i + 1)" />
                    <span>{{ $p['title'] }}</span>
                    <small>{{ $p['city'] }}</small>
                </div>
            @endforeach
        </div>
        <a class="hb-text-link" href="{{ route('projects') }}">Voir tous les projets <x-icon name="arrow-right" :size="15" /></a>
    </div>

    <div id="contact" class="hb-contact">
        <p class="hb-eyebrow">CONTACT</p>
        <h2>Parlons de votre projet</h2>
        <div class="hb-contact-list">
            @foreach($c['contact']['phones'] as $p)
                <a href="tel:{{ preg_replace('/\s+/', '', $p) }}"><x-icon name="phone" :size="15" />{{ $p }}</a>
            @endforeach
            <a href="mailto:{{ $c['contact']['email'] }}"><x-icon name="mail" :size="15" />{{ $c['contact']['email'] }}</a>
            <p><x-icon name="map-pin" :size="15" />{{ $c['contact']['address'] }}</p>
        </div>
        <a class="hb-button hb-button-gold" href="{{ route('contact') }}">Nous contacter <x-icon name="arrow-right" :size="15" /></a>
    </div>
</section>

</div>{{-- /hb-home-page --}}

<script>
    // Force la lecture des vidéos des cartes (certains navigateurs bloquent l'autoplay)
    document.querySelectorAll('.hb-vid').forEach(function (v) {
        v.muted = true;
        var p = v.play && v.play();
        if (p && p.catch) p.catch(function () {});
    });
</script>

<style>
    /* ====== FOND DE LA PAGE D'ACCUEIL ====== */
    .hb-home-page {
        position: relative;
        isolation: isolate;
        clip-path: inset(0);
        background: #090908;
    }

    .hb-home-bg {
        position: fixed;
        inset: 0;
        z-index: -1;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
    }

    .hb-home-bg::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(
            180deg,
            rgba(9, 9, 8, .62) 0%,
            rgba(9, 9, 8, .82) 100%
        );
    }

    .hb-home-page .hb-service-strip,
    .hb-home-page .hb-solutions-section,
    .hb-home-page .hb-process,
    .hb-home-page .hb-about {
        background: rgba(9, 9, 8, .45) !important;
    }

    /* ====== BANDEAU : 5 éléments ====== */
    @media (min-width: 901px) {
        .hb-home-page .hb-service-strip {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
    }

    /* ====== NOS SOLUTIONS : moins d'espace + 5 cartes sur une ligne ====== */
    .hb-home-page .hb-solutions-section {
        display: block !important;
        min-height: 0 !important;
        height: auto !important;
        padding: clamp(40px, 5vw, 64px) 5% clamp(44px, 5vw, 64px) !important;
    }
    .hb-home-page .hb-solutions-section .hb-section-intro {
        max-width: 640px;
        margin: 0 0 28px !important;
        padding: 0 !important;
    }
    .hb-home-page .hb-solutions-section .hb-section-intro h2 {
        margin: 10px 0 12px;
        font-size: clamp(28px, 3vw, 38px);
        line-height: 1.12;
    }
    .hb-home-page .hb-solutions-section .hb-section-intro p { margin-top: 0; margin-bottom: 12px; }
    .hb-home-page .hb-solutions-section .hb-eyebrow { margin-bottom: 0; }

    .hb-home-page .hb-solution-grid {
        display: grid !important;
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        gap: 14px;
        width: 100%;
        margin: 0 !important;
        align-items: stretch;
    }
    .hb-home-page .hb-solution-card {
        height: auto !important;
        min-height: 0 !important;
    }
    .hb-home-page .hb-solution-info { padding: 18px 16px 20px; }
    .hb-home-page .hb-solution-info h3 { font-size: clamp(16px, 1.5vw, 19px); }
    .hb-home-page .hb-solution-info p { font-size: 11px; line-height: 1.7; }

    @media (max-width: 1100px) and (min-width: 701px) {
        .hb-home-page .hb-solution-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
    }
    @media (max-width: 700px) {
        .hb-home-page .hb-solution-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 12px; }
    }

    /* ====== IMAGE / VIDÉO DANS LES CARTES (même taille) ====== */
    .hb-home-page .hb-solution-image {
        position: relative;
        overflow: hidden;
        height: clamp(150px, 14vw, 200px);
    }
    .hb-home-page .hb-solution-image img,
    .hb-home-page .hb-solution-image .hb-vid {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .hb-home-page .hb-solution-image .hb-image-shade { pointer-events: none; }
</style>

</x-hb-layout>
