@php
    $c = config('hnbloom');

    $solPath = $c['images']['solutions'] ?? 'images/solutions.jpg';
    $solBg = file_exists(public_path($solPath))
        ? asset($solPath)
        : asset('images/solutions.jpg');

    $solVidPath = $c['solutions_video'] ?? 'videos/solutions.mp4';
    $solVideo = file_exists(public_path($solVidPath))
        ? asset($solVidPath)
        : null;

    $families = collect($c['families'] ?? [])->take(4);
@endphp

<x-hb-layout
    title="Nos solutions"
    description="Obstacles physiques automatisés, sécurité électronique, détection incendie et hygiène agro-alimentaire au Maroc."
>
<div class="hb-solutions">

    <div class="hb-sol-progress" aria-hidden="true"><span></span></div>

    <section class="hb-sol-hero" id="catalogue-solutions">
        <div class="hb-sol-hero-media" aria-hidden="true"
             style="background-image:url('{{ $solBg }}')">
            @if($solVideo)
                <video class="hb-sol-hero-video" autoplay muted loop playsinline
                       preload="auto" disablepictureinpicture
                       poster="{{ $solBg }}">
                    <source src="{{ $solVideo }}" type="video/mp4">
                </video>
            @endif
        </div>

        <div class="hb-sol-hero-shade" aria-hidden="true"></div>

        <div class="hb-sol-hero-inner">
            <div class="hb-sol-heading">
                <p class="hb-sol-eyebrow hb-sol-rv" style="--d:.05s">
                    NOS SOLUTIONS <span></span>
                </p>

                <h1 class="hb-sol-rv" style="--d:.15s">
                    Une sécurité complète
                    <em>pour vos espaces.</em>
                </h1>

                <p class="hb-sol-lead hb-sol-rv" style="--d:.28s">
                    Des solutions fiables et intégrées pour contrôler les accès,
                    protéger les personnes et les biens, maîtriser l’hygiène
                    et rendre les bâtiments plus intelligents.
                </p>
            </div>

            <aside class="hb-sol-side-note hb-sol-rv" style="--d:.4s">
                <span class="hb-sol-count">01 <i></i> 04</span>
                <span class="hb-sol-side-rule"></span>
                <p>Protection. Contrôle.<br>Intelligence.</p>
                <small>SMART BUILDING · CASABLANCA</small>
            </aside>

            <div class="hb-sol-cards">
                @foreach($families as $slug => $f)
                    @php
                        // images/obstacles-physiques-automatises.jpg
                        // -> videos/obstacles-physiques-automatises.mp4
                        $solutionImageName = $f['image'] ?? basename((string) $slug);

                        $solutionVideoName = pathinfo(
                            basename(parse_url(
                                $solutionImageName,
                                PHP_URL_PATH
                            ) ?: $solutionImageName),
                            PATHINFO_FILENAME
                        ) . '.mp4';

                        $solutionVideoPath = 'videos/' . $solutionVideoName;

                        $solutionVideoUrl = file_exists(
                            public_path($solutionVideoPath)
                        ) ? asset($solutionVideoPath) : null;
                    @endphp

                    <a href="{{ route('solutions.show', $slug) }}"
                       class="hb-sol-card hb-sol-rv"
                       style="--d:{{ .45 + $loop->index * 0.12 }}s">

                        <i class="hb-sol-topline" aria-hidden="true"></i>
                        <i class="hb-sol-halo" aria-hidden="true"></i>

                        <div class="hb-sol-card-image">
                            {{-- Image de secours : visible tant que la vidéo ne joue pas --}}
                            <x-hb-photo
                                :local="$f['image'] ?? null"
                                :fallback="$f['fallback'] ?? null"
                                :alt="$f['title'] ?? 'Solution HN BLOOM'"
                            />

                            @if($solutionVideoUrl)
                                <video class="hb-sol-card-video"
                                       muted loop playsinline preload="none"
                                       disablepictureinpicture
                                       aria-hidden="true" tabindex="-1">
                                    <source src="{{ $solutionVideoUrl }}"
                                            type="video/mp4">
                                </video>
                            @endif

                            <span class="hb-sol-shine" aria-hidden="true"></span>

                            <span class="hb-sol-card-number">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            <span class="hb-sol-card-arrow">
                                <x-icon name="arrow-up-right" :size="19" />
                            </span>
                        </div>

                        <div class="hb-sol-card-caption">
                            <span class="hb-sol-card-line"></span>
                            <h2>{{ $f['title'] }}</h2>
                            <span class="hb-sol-card-open" aria-hidden="true">
                                <x-icon name="arrow-right" :size="18" />
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="hb-sol-bottom-note hb-sol-rv" style="--d:.95s">
                <span>HN BLOOM</span>
                <span>Des solutions adaptées aux environnements professionnels</span>
                <a href="{{ route('contact', ['sujet' => 'quote']) }}">
                    PARLONS DE VOTRE PROJET
                    <x-icon name="arrow-up-right" :size="14" />
                </a>
            </div>
        </div>
    </section>

    <section class="hb-sol-smart">
        <div class="hb-sol-smart-label hb-sol-rv" style="--d:0s">
            <span class="hb-sol-eyebrow">AU-DELÀ DE LA SÉCURITÉ</span>
            <h2>Le bâtiment <em>intelligent.</em></h2>
        </div>

        <p class="hb-sol-rv" style="--d:.12s">
            Smart Home, Smart Hotel et Smart Hospital : découvrez les solutions
            SMART G4 CONTROL pour un bâtiment plus confortable et plus efficace.
        </p>

        <a href="{{ route('domotique') }}" class="hb-sol-smart-link hb-sol-rv" style="--d:.24s">
            Découvrir la domotique
            <x-icon name="arrow-right" :size="17" />
        </a>
    </section>

</div>

@include('partials.hb-cta')

<style>
.hb-solutions {
    --sol-gold: #d6ad55;
    --sol-gold-light: #f2d58f;
    --sol-black: #080908;
    --sol-white: #f8f5ee;
    --sol-muted: #c4c0b5;
    position: relative;
    isolation: isolate;
    background: var(--sol-black);
    color: var(--sol-white);
    font-family: Arial, Helvetica, sans-serif;
    overflow: clip;
}

.hb-solutions * { box-sizing: border-box; }

.hb-sol-progress {
    position: fixed;
    z-index: 9999;
    inset: 0 0 auto;
    height: 2px;
    pointer-events: none;
}

.hb-sol-progress span {
    display: block;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, var(--sol-gold), var(--sol-gold-light));
    transform: scaleX(0);
    transform-origin: left;
    will-change: transform;
}

/* ====== HERO ====== */
.hb-sol-hero {
    position: relative;
    min-height: min(900px, 100svh);
    min-height: max(720px, min(900px, 100svh));
    padding: clamp(54px, 7vh, 86px) clamp(24px, 5.3vw, 86px) 24px;
    display: flex;
    align-items: stretch;
    isolation: isolate;
}

.hb-sol-hero-media,
.hb-sol-hero-shade {
    position: absolute;
    inset: 0;
    z-index: -2;
}

.hb-sol-hero-media {
    background-position: center;
    background-size: cover;
    background-color: #080908;
    animation: hbSolBgIn 1.8s cubic-bezier(.2,.7,.2,1) both;
}

.hb-sol-hero-video {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hb-sol-hero-shade {
    z-index: -1;
    background:
        linear-gradient(90deg, rgba(5,6,5,.92) 0%, rgba(5,6,5,.66) 42%, rgba(5,6,5,.25) 100%),
        linear-gradient(0deg, rgba(5,6,5,.96) 0%, rgba(5,6,5,.4) 44%, rgba(5,6,5,.18) 100%),
        linear-gradient(180deg, rgba(5,6,5,.36), transparent 25%);
}

.hb-sol-hero-inner {
    position: relative;
    display: grid;
    grid-template-columns: minmax(0, 1fr) 170px;
    grid-template-rows: auto 1fr auto;
    column-gap: clamp(24px, 4vw, 65px);
    row-gap: 28px;
    width: 100%;
    max-width: 1500px;
    margin: auto;
}

.hb-sol-heading {
    max-width: 790px;
    padding-top: 10px;
}

.hb-sol-eyebrow {
    display: flex;
    align-items: center;
    gap: 17px;
    margin: 0 0 22px;
    color: var(--sol-gold-light);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 4px;
    line-height: 1.5;
    text-transform: uppercase;
}

.hb-sol-eyebrow span {
    display: inline-block;
    width: 95px;
    height: 1px;
    background: linear-gradient(90deg, var(--sol-gold), transparent);
    transform-origin: left;
    animation: hbSolRule 1.2s .6s ease-out backwards;
}

.hb-sol-heading h1 {
    margin: 0;
    max-width: 800px;
    color: #fffdf8;
    font: 400 clamp(38px, 4.25vw, 66px)/1.04 Georgia, "Times New Roman", serif;
    letter-spacing: -1.7px;
    text-shadow: 0 3px 25px rgba(0,0,0,.25);
}

.hb-sol-heading h1 em {
    display: block;
    margin-top: 8px;
    color: var(--sol-gold-light);
    font-weight: 400;
}

.hb-sol-lead {
    max-width: 555px;
    margin: 22px 0 0;
    color: #eee9df;
    font-size: 13px;
    line-height: 1.85;
    text-shadow: 0 2px 12px rgba(0,0,0,.55);
}

/* NOTE LATERALE */
.hb-sol-side-note {
    align-self: center;
    margin-top: 18px;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    text-shadow: 0 2px 10px rgba(0,0,0,.6);
}

.hb-sol-count {
    color: var(--sol-gold-light);
    font: 400 27px/1 Georgia, "Times New Roman", serif;
    white-space: nowrap;
}

.hb-sol-count i {
    display: inline-block;
    width: 25px;
    height: 1px;
    margin: 0 5px 7px;
    background: var(--sol-gold);
}

.hb-sol-side-rule {
    height: 1px;
    margin: 18px 0;
    background: rgba(242,213,143,.45);
}

.hb-sol-side-note p {
    margin: 0;
    color: #f2eadb;
    font: 400 16px/1.55 Georgia, "Times New Roman", serif;
}

.hb-sol-side-note small {
    margin-top: 19px;
    color: #ddd5c5;
    font-size: 8px;
    letter-spacing: 1.35px;
    white-space: nowrap;
}

/* ====== 4 CARTES ====== */
.hb-sol-cards {
    grid-column: 1 / -1;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    align-self: end;
    margin-top: 12px;
}

.hb-sol-card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow: hidden;
    border: 1px solid rgba(214,173,85,.85);
    background: rgba(7,8,7,.74);
    color: inherit;
    text-decoration: none;
    box-shadow: 0 15px 45px rgba(0,0,0,.2);
    /* inclinaison 3D pilotée par le script (comme la page d'accueil) */
    transform: perspective(1100px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg));
    transition: transform .35s ease-out, border-color .4s ease, box-shadow .5s ease;
}

.hb-sol-card:hover {
    transform: perspective(1100px) translateY(-8px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg));
    border-color: var(--sol-gold-light);
    box-shadow: 0 30px 60px -20px rgba(0,0,0,.7), 0 0 40px -10px rgba(214,173,85,.35);
}

/* ligne dorée en haut + halo qui suit la souris */
.hb-sol-topline {
    position: absolute;
    z-index: 5;
    top: 0; left: 0; right: 0;
    height: 2px;
    pointer-events: none;
    background: linear-gradient(90deg, transparent, #f1d58e, transparent);
    transform: scaleX(0);
    transition: transform .6s cubic-bezier(.2,.7,.2,1);
}
.hb-sol-card:hover .hb-sol-topline { transform: scaleX(1); }

.hb-sol-halo {
    position: absolute;
    inset: 0;
    z-index: 4;
    pointer-events: none;
    opacity: 0;
    transition: opacity .4s ease;
    background: radial-gradient(380px circle at var(--mx, 50%) var(--my, 50%), rgba(241,213,142,.15), transparent 60%);
}
.hb-sol-card:hover .hb-sol-halo { opacity: 1; }

.hb-sol-card-image {
    position: relative;
    height: clamp(125px, 15vw, 205px);
    overflow: hidden;
    background: #24231e;
}

.hb-sol-card-image > * {
    width: 100%;
    height: 100%;
}

.hb-sol-card-image img,
.hb-sol-card-image .hb-sol-card-video {
    position: absolute;
    inset: 0;
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .8s cubic-bezier(.2,.7,.2,1);
}

.hb-sol-card-image img { z-index: 0; }

/* la vidéo apparaît en fondu quand elle joue vraiment */
.hb-sol-card-image .hb-sol-card-video {
    z-index: 1;
    opacity: 0;
    pointer-events: none;
    transition: transform .8s cubic-bezier(.2,.7,.2,1), opacity .6s ease;
}
.hb-sol-card.is-playing .hb-sol-card-video { opacity: 1; }

.hb-sol-card:hover .hb-sol-card-image img,
.hb-sol-card:hover .hb-sol-card-video { transform: scale(1.06); }

.hb-sol-card-image::after {
    content: "";
    position: absolute;
    inset: 0;
    z-index: 2;
    background: linear-gradient(0deg, rgba(0,0,0,.48), transparent 60%);
    pointer-events: none;
}

/* reflet qui traverse l'image au survol */
.hb-sol-shine {
    position: absolute !important;
    z-index: 3;
    top: 0; bottom: 0;
    left: -70%;
    width: 45% !important;
    height: auto !important;
    pointer-events: none;
    background: linear-gradient(100deg, transparent, rgba(255,244,214,.28), transparent);
    transform: skewX(-18deg);
}
.hb-sol-card:hover .hb-sol-shine { left: 130%; transition: left 1s ease; }

.hb-sol-card-number {
    position: absolute;
    z-index: 3;
    top: 12px;
    left: 13px;
    color: var(--sol-gold-light);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
}

.hb-sol-card-arrow {
    position: absolute;
    z-index: 3;
    right: 12px;
    bottom: 12px;
    display: grid;
    place-items: center;
    width: 32px;
    height: 32px;
    border: 1px solid rgba(242,213,143,.8);
    background: rgba(8,9,8,.72);
    color: var(--sol-gold-light);
    transition: background .3s ease, color .3s ease;
}

.hb-sol-card:hover .hb-sol-card-arrow {
    background: var(--sol-gold);
    color: #111;
}

.hb-sol-card-caption {
    position: relative;
    display: flex;
    align-items: center;
    gap: 11px;
    min-height: 65px;
    padding: 13px 14px;
}

.hb-sol-card-line {
    flex: 0 0 19px;
    height: 1px;
    background: var(--sol-gold);
    transition: flex-basis .5s cubic-bezier(.2,.7,.2,1);
}
.hb-sol-card:hover .hb-sol-card-line { flex-basis: 30px; }

.hb-sol-card-caption h2 {
    flex: 1;
    margin: 0;
    color: #f8f0df;
    font: 400 clamp(14px, 1.15vw, 18px)/1.25 Georgia, "Times New Roman", serif;
}

.hb-sol-card-open {
    color: var(--sol-gold-light);
    transition: transform .3s ease;
}
.hb-sol-card:hover .hb-sol-card-open { transform: translateX(4px); }

/* BAS DE PAGE */
.hb-sol-bottom-note {
    grid-column: 1 / -1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding-top: 3px;
    color: rgba(239,232,216,.72);
    font-size: 9px;
    letter-spacing: 1.1px;
}

.hb-sol-bottom-note > span:first-child {
    color: var(--sol-gold-light);
    letter-spacing: 2px;
}

.hb-sol-bottom-note a {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--sol-gold-light);
    text-decoration: none;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 1.1px;
    transition: color .25s ease;
}
.hb-sol-bottom-note a:hover { color: #fff; }

/* BANDEAU DOMOTIQUE */
.hb-sol-smart {
    display: grid;
    grid-template-columns: 1fr 1.2fr auto;
    align-items: center;
    gap: 35px;
    padding: 38px clamp(24px, 5.3vw, 86px);
    border-top: 1px solid rgba(214,173,85,.3);
    background:
        radial-gradient(ellipse at 5% 0%, rgba(214,173,85,.1), transparent 45%),
        #0c0d0b;
}

.hb-sol-smart .hb-sol-eyebrow {
    margin-bottom: 9px;
    font-size: 9px;
    letter-spacing: 2.2px;
}

.hb-sol-smart h2 {
    margin: 0;
    color: var(--sol-white);
    font: 400 clamp(25px, 2.5vw, 36px)/1.15 Georgia, "Times New Roman", serif;
}

.hb-sol-smart h2 em {
    color: var(--sol-gold-light);
    font-weight: 400;
}

.hb-sol-smart > p {
    max-width: 520px;
    margin: 0;
    color: var(--sol-muted);
    font-size: 12px;
    line-height: 1.8;
}

.hb-sol-smart-link {
    display: inline-flex;
    align-items: center;
    gap: 13px;
    padding: 14px 17px;
    border: 1px solid rgba(214,173,85,.65);
    color: var(--sol-gold-light);
    text-decoration: none;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .8px;
    white-space: nowrap;
    transition: background .25s ease, color .25s ease;
}
.hb-sol-smart-link:hover {
    background: var(--sol-gold);
    color: #111;
}

/* ====== ANIMATIONS (même logique que la page d'accueil) ====== */
@keyframes hbSolBgIn { from { transform: scale(1.12); } to { transform: scale(1); } }
@keyframes hbSolRise { from { opacity: 0; transform: translateY(46px) scale(.97); } to { opacity: 1; transform: none; } }
@keyframes hbSolRule { from { transform: scaleX(0); } to { transform: scaleX(1); } }
@keyframes hbSolClip { from { clip-path: inset(0 0 100% 0); } to { clip-path: inset(0 0 0 0); } }
@keyframes hbSolImgIn { from { transform: scale(1.25); } to { transform: scale(1); } }

.hb-sol-rv:not(.is-in) { opacity: 0; }
.hb-sol-rv.is-in { animation: hbSolRise 1.1s var(--d, 0s) cubic-bezier(.2,.7,.2,1) backwards; }

/* l'image de chaque carte se révèle en rideau, puis zoome doucement */
.hb-sol-card.is-in .hb-sol-card-image {
    animation: hbSolClip 1.3s calc(var(--d, 0s) + .15s) cubic-bezier(.7,0,.2,1) backwards;
}
.hb-sol-card.is-in .hb-sol-card-image img {
    animation: hbSolImgIn 1.8s calc(var(--d, 0s) + .15s) cubic-bezier(.2,.7,.2,1) backwards;
}

/* ====== RESPONSIVE ====== */
@media (min-width: 1500px) {
    .hb-sol-hero {
        padding-right: max(5.3vw, calc((100vw - 1500px)/2));
        padding-left: max(5.3vw, calc((100vw - 1500px)/2));
    }
}

@media (max-width: 1100px) {
    .hb-sol-hero {
        min-height: auto;
        padding-top: 65px;
    }

    .hb-sol-hero-inner { grid-template-columns: minmax(0, 1fr) 145px; }
    .hb-sol-cards { gap: 12px; }

    .hb-sol-card-caption {
        gap: 8px;
        padding: 12px 10px;
    }

    .hb-sol-card-line { flex-basis: 12px; }

    .hb-sol-smart { grid-template-columns: 1fr 1fr; }

    .hb-sol-smart-link {
        justify-self: start;
        grid-column: 2;
    }
}

@media (max-width: 760px) {
    .hb-sol-card { transform: none !important; }

    .hb-sol-hero { padding: 65px 22px 22px; }

    .hb-sol-hero-inner {
        grid-template-columns: 1fr;
        row-gap: 23px;
    }

    .hb-sol-heading { padding-top: 0; }

    .hb-sol-heading h1 {
        font-size: clamp(37px, 9vw, 54px);
        letter-spacing: -1px;
    }

    .hb-sol-lead {
        max-width: 560px;
        font-size: 12px;
    }

    .hb-sol-side-note { display: none; }

    .hb-sol-cards {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 0;
    }

    .hb-sol-card-image { height: clamp(125px, 29vw, 210px); }
    .hb-sol-card-caption { min-height: 62px; }
    .hb-sol-card-caption h2 { font-size: 15px; }

    .hb-sol-bottom-note {
        flex-wrap: wrap;
        line-height: 1.6;
    }

    .hb-sol-bottom-note > span:nth-child(2) { display: none; }

    .hb-sol-smart {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 30px 22px;
    }

    .hb-sol-smart-link { grid-column: auto; }
}

@media (max-width: 420px) {
    .hb-sol-hero { padding: 54px 15px 18px; }

    .hb-sol-eyebrow {
        font-size: 9px;
        letter-spacing: 3px;
    }

    .hb-sol-heading h1 { font-size: 35px; }
    .hb-sol-cards { gap: 9px; }
    .hb-sol-card-image { height: 125px; }

    .hb-sol-card-caption {
        align-items: flex-start;
        min-height: 76px;
        padding: 10px 8px;
        gap: 6px;
    }

    .hb-sol-card-line {
        flex-basis: 8px;
        margin-top: 8px;
    }

    .hb-sol-card-caption h2 { font-size: 13px; }
    .hb-sol-card-open { display: none; }
    .hb-sol-bottom-note a { font-size: 8px; }
}

@media (prefers-reduced-motion: reduce) {
    .hb-sol-rv:not(.is-in) { opacity: 1; }
    .hb-sol-rv,
    .hb-sol-card.is-in .hb-sol-card-image,
    .hb-sol-card.is-in .hb-sol-card-image img,
    .hb-sol-hero-media,
    .hb-sol-eyebrow span { animation: none !important; }
    .hb-sol-card { transform: none !important; }
    .hb-sol-hero-video,
    .hb-sol-card-video { display: none !important; }
}
</style>
<noscript><style>.hb-sol-rv:not(.is-in){opacity:1 !important}</style></noscript>

<script>
(function () {
    var root = document.querySelector('.hb-solutions');
    if (!root) return;

    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var saver = navigator.connection && navigator.connection.saveData;
    var hasIO = 'IntersectionObserver' in window;
    var tryPlay = function (v) {
        var p = v.play();
        if (p && p.catch) p.catch(function () {});
    };

    /* 1) Barre de progression */
    var bar = root.querySelector('.hb-sol-progress span');
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

    /* 2) Apparition au scroll */
    var rv = root.querySelectorAll('.hb-sol-rv');
    if (!hasIO || reduce) {
        rv.forEach(function (el) { el.classList.add('is-in'); });
    } else {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
        rv.forEach(function (el) { io.observe(el); });
    }

    /* 3) Vidéo du hero : lecture directe, pause hors écran */
    var heroVideo = root.querySelector('.hb-sol-hero-video');
    if (heroVideo) {
        heroVideo.muted = true;
        if (saver || reduce) {
            heroVideo.remove();
        } else if (hasIO) {
            new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) tryPlay(heroVideo); else heroVideo.pause();
                });
            }, { threshold: 0.1 }).observe(heroVideo);
        }
    }

    /* 4) Vidéos des cartes : lecture directe quand la carte est visible.
          - preload="none" : rien n'est téléchargé avant d'être visible
          - démarrage échelonné (une carte après l'autre) pour ne pas bloquer le navigateur
          - pause automatique hors écran */
    var cards = Array.prototype.slice.call(root.querySelectorAll('.hb-sol-card'));
    var started = 0;

    cards.forEach(function (card) {
        var v = card.querySelector('.hb-sol-card-video');
        if (!v) return;

        v.muted = true;

        if (saver || reduce) { v.remove(); return; }

        // la vidéo n'apparaît qu'une fois qu'elle joue vraiment
        v.addEventListener('playing', function () { card.classList.add('is-playing'); });
        v.addEventListener('error', function () { card.classList.remove('is-playing'); });
    });

    if (hasIO && !saver && !reduce) {
        var vio = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                var card = e.target;
                var v = card.querySelector('.hb-sol-card-video');
                if (!v) return;

                if (e.isIntersecting) {
                    var delay = card._started ? 0 : (started++ * 220);
                    card._started = true;
                    clearTimeout(card._t);
                    card._t = setTimeout(function () {
                        v.preload = 'auto';
                        tryPlay(v);
                    }, delay);
                } else {
                    clearTimeout(card._t);
                    v.pause();
                }
            });
        }, { threshold: 0.25 });

        // on attend la fin du chargement de la page pour ne pas la ralentir
        var startVideos = function () {
            cards.forEach(function (c) { vio.observe(c); });
        };
        if (document.readyState === 'complete') startVideos();
        else window.addEventListener('load', startVideos, { once: true });
    }

    /* 5) Inclinaison 3D + halo doré (souris uniquement) */
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
                card.style.setProperty('--ry', ((x - .5) * 7) + 'deg');
                card.style.setProperty('--rx', ((.5 - y) * 7) + 'deg');
            };
            card.addEventListener('pointermove', function (e) {
                ev = e;
                if (!raf) raf = requestAnimationFrame(apply);
            }, { passive: true });
            card.addEventListener('pointerleave', function () {
                card.style.setProperty('--rx', '0deg');
                card.style.setProperty('--ry', '0deg');
            });
        });
    }
})();
</script>
</x-hb-layout>
