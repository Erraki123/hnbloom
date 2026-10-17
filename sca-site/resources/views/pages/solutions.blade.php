@php
    $c = config('hnbloom');

    // Image de fond de la page : public/images/solutions.jpg
    $solPath = $c['images']['solutions'] ?? 'images/solutions.jpg';
    $solBg   = file_exists(public_path($solPath)) ? asset($solPath) : null;

    // Vidéo du hero : public/videos/solutions.mp4 (ou clé 'solutions_video' dans config/hnbloom.php)
    $solVidPath = $c['solutions_video'] ?? 'videos/solutions.mp4';
    $solVideo   = file_exists(public_path($solVidPath)) ? asset($solVidPath) : null;
@endphp

<x-hb-layout
    title="Nos solutions"
    description="Obstacles physiques automatisés, hygiène agro-alimentaire, sécurité électronique, détection incendie et domotique au Maroc."
>

<div class="hb-solutions-page">

    @if($solBg)
        {{-- FOND DE LA PAGE (photo fixe, visible derrière toutes les sections sauf le hero) --}}
        <div class="hb-solutions-bg" style="background-image:url('{{ $solBg }}')" aria-hidden="true"></div>
    @endif

    {{-- HERO : vidéo solutions.mp4 (sinon la photo) --}}
    <section class="hb-solutions-hero">
        @if($solVideo || $solBg)
            <div class="hb-solutions-hero-photo"
                 @if(!$solVideo) style="background-image:url('{{ $solBg }}')" @endif
                 aria-hidden="true">
                @if($solVideo)
                    <video class="hb-solutions-hero-video" autoplay muted loop playsinline preload="auto" disablepictureinpicture
                           @if($solBg) poster="{{ $solBg }}" @endif>
                        <source src="{{ $solVideo }}" type="video/mp4">
                    </video>
                @endif
            </div>
        @endif

        <div class="hb-solutions-hero-inner">
            <div class="hb-solutions-hero-content">
                <p class="hb-eyebrow">NOS SOLUTIONS</p>

                <h1>
                    Une sécurité complète
                    <span>pour vos espaces.</span>
                </h1>

                <p class="hb-solutions-lead">
                    Des solutions fiables et intégrées pour contrôler les accès,
                    protéger les personnes et les biens, maîtriser l’hygiène
                    et rendre les bâtiments plus intelligents.
                </p>

                <a href="#catalogue-solutions" class="hb-solutions-button">
                    Explorer nos solutions
                    <x-icon name="arrow-down" :size="17" />
                </a>
            </div>

            <div class="hb-solutions-hero-side">
                <span class="hb-solutions-side-number">01 — 04</span>
                <span class="hb-solutions-side-line"></span>
                <p>Protection. Contrôle. Intelligence.</p>
                <span class="hb-solutions-side-location">
                    SMART BUILDING · CASABLANCA
                </span>
            </div>
        </div>
    </section>

    {{-- INTRODUCTION --}}
    <section class="hb-solutions-intro" id="catalogue-solutions">
        <div class="hb-solutions-intro-inner">
            <div>
                <p class="hb-eyebrow">NOTRE EXPERTISE</p>
                <h2>
                    La bonne solution,
                    <span>au bon endroit.</span>
                </h2>
            </div>

            <p>
                Chaque environnement possède ses propres exigences.
                Nous proposons des équipements et des solutions adaptés
                aux besoins des entreprises, des bâtiments et des sites
                professionnels, de l’étude à la mise en service.
            </p>
        </div>
    </section>

    {{-- CARTES SOLUTIONS --}}
    <section class="hb-solutions-catalogue">
        <div class="hb-solutions-grid">

            @foreach($c['families'] as $slug => $f)
                <a
                    href="{{ route('solutions.show', $slug) }}"
                    class="hb-solution-card reveal"
                    style="--d:{{ $loop->index * 0.08 }}s"
                >
                    <div class="hb-solution-image">
                        <x-hb-photo
                            :local="$f['image']"
                            :fallback="$f['fallback']"
                            :alt="$f['title']"
                            :label="$f['title']"
                        />

                        <span class="hb-solution-index">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>

                        <span class="hb-solution-image-arrow">
                            <x-icon name="arrow-up-right" :size="20" />
                        </span>
                    </div>

                    <div class="hb-solution-content">
                        <div class="hb-solution-meta">
                            <span class="hb-solution-icon">
                                <x-icon
                                    :name="$f['icon']"
                                    :size="20"
                                    :stroke="1.5"
                                />
                            </span>

                            <span class="hb-eyebrow">
                                {{ $f['eyebrow'] }}
                            </span>
                        </div>

                        <h3>{{ $f['title'] }}</h3>

                        <p>{{ $f['lead'] }}</p>

                        <span class="hb-solution-link">
                            Découvrir la solution
                            <x-icon name="arrow-right" :size="16" />
                        </span>
                    </div>
                </a>
            @endforeach

        </div>
    </section>

    {{-- DOMOTIQUE --}}
    <section class="hb-solutions-smart reveal">
        <div class="hb-solutions-smart-content">
            <p class="hb-eyebrow">DOMOTIQUE & SMART BUILDING</p>

            <h2>
                Le bâtiment intelligent,
                <span>pensé pour demain.</span>
            </h2>

            <p>
                Découvrez les solutions SMART G4 CONTROL pour la gestion
                intelligente des bâtiments résidentiels, hôteliers
                et hospitaliers.
            </p>

            <a href="{{ route('domotique') }}" class="hb-solutions-button">
                Explorer la domotique
                <x-icon name="arrow-right" :size="17" />
            </a>
        </div>

        <div class="hb-solutions-smart-aside">
            <div class="hb-smart-item">
                <span>01</span>
                <p>Smart Home</p>
            </div>

            <div class="hb-smart-item">
                <span>02</span>
                <p>Smart Hotel</p>
            </div>

            <div class="hb-smart-item">
                <span>03</span>
                <p>Smart Hospital</p>
            </div>
        </div>
    </section>

</div>{{-- /hb-solutions-page --}}

    {{-- CTA EXISTANT --}}
    @include('partials.hb-cta')

    {{-- STYLES --}}
    <style>
        /* ====== FOND DE LA PAGE (solutions.jpg) ====== */
        .hb-solutions-page {
            position: relative;
            isolation: isolate;
            background: #090908;
        }

        .hb-solutions-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
        }

        /* Voile sombre de la page : augmentez pour assombrir, baissez pour voir plus l'image */
        .hb-solutions-bg::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                rgba(9, 9, 8, .78) 0%,
                rgba(9, 9, 8, .9) 100%
            );
        }

        .hb-solutions-hero,
        .hb-solutions-intro,
        .hb-solutions-catalogue,
        .hb-solutions-smart {
            --sol-gold: #d6ad55;
            --sol-gold-light: #f1d58e;
            --sol-bg: #090908;
            --sol-panel: #10100e;
            --sol-border: rgba(214, 173, 85, .25);
            --sol-muted: #b1b0a8;
            color: #f5f1e8;
        }

        /* ====== HERO : vidéo (ou photo) ====== */
        .hb-solutions-hero {
            position: relative;
            overflow: hidden;
            padding: clamp(75px, 10vw, 135px) 6%;
            background: #10100e;
            border-bottom: 1px solid var(--sol-border);
        }

        .hb-solutions-hero-photo {
            position: absolute;
            inset: 0;
            z-index: 0;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
        }

        .hb-solutions-hero-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        /* Voile du hero (au-dessus de la vidéo) : garde le texte lisible */
        .hb-solutions-hero-photo::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(
                    90deg,
                    rgba(9, 9, 8, .88) 0%,
                    rgba(9, 9, 8, .6) 55%,
                    rgba(9, 9, 8, .35) 100%
                ),
                linear-gradient(
                    180deg,
                    rgba(9, 9, 8, 0) 60%,
                    rgba(9, 9, 8, .85) 100%
                );
        }

        .hb-solutions-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 45px;
            width: 100%;
            max-width: 1250px;
            margin: 0 auto;
        }

        .hb-solutions-hero-content {
            max-width: 820px;
        }

        .hb-solutions-hero .hb-eyebrow,
        .hb-solutions-intro .hb-eyebrow,
        .hb-solutions-smart .hb-eyebrow {
            margin: 0 0 24px;
            color: var(--sol-gold);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 3px;
        }

        .hb-solutions-hero h1,
        .hb-solutions-intro h2,
        .hb-solutions-smart h2 {
            margin: 0;
            color: #f8f4eb;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(42px, 6vw, 76px);
            font-weight: 400;
            line-height: 1.04;
            letter-spacing: -1.7px;
        }

        .hb-solutions-hero h1 span,
        .hb-solutions-intro h2 span,
        .hb-solutions-smart h2 span {
            display: block;
            color: var(--sol-gold-light);
        }

        .hb-solutions-lead {
            max-width: 590px;
            margin: 26px 0 0;
            color: #cfcdc5;
            font-size: 14px;
            line-height: 1.95;
        }

        .hb-solutions-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 22px;
            min-height: 48px;
            margin-top: 32px;
            padding: 0 20px;
            border: 1px solid var(--sol-gold);
            background: var(--sol-gold);
            color: #17130a;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: background .25s ease, color .25s ease,
                        transform .25s ease;
        }

        .hb-solutions-button:hover {
            transform: translateY(-2px);
            background: transparent;
            color: var(--sol-gold-light);
        }

        .hb-solutions-side-number {
            color: var(--sol-gold-light);
            font-family: Georgia, "Times New Roman", serif;
            font-size: 28px;
        }

        .hb-solutions-hero-side {
            display: flex;
            flex: 0 0 200px;
            flex-direction: column;
            align-items: flex-start;
            padding-bottom: 7px;
        }

        .hb-solutions-side-line {
            width: 100%;
            height: 1px;
            margin: 17px 0;
            background: var(--sol-border);
        }

        .hb-solutions-hero-side p {
            margin: 0;
            color: #e9e2d2;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 17px;
            line-height: 1.5;
        }

        .hb-solutions-side-location {
            margin-top: 20px;
            color: #a09e95;
            font-size: 9px;
            letter-spacing: 1.4px;
        }

        .hb-solutions-intro {
            padding: 75px 6% 45px;
            background: rgba(9, 9, 8, .45);
        }

        .hb-solutions-intro-inner {
            display: grid;
            grid-template-columns: 1fr .8fr;
            align-items: end;
            gap: 55px;
            max-width: 1250px;
            margin: 0 auto;
        }

        .hb-solutions-intro h2,
        .hb-solutions-smart h2 {
            font-size: clamp(34px, 4vw, 49px);
            line-height: 1.12;
            letter-spacing: -.7px;
        }

        .hb-solutions-intro-inner > p {
            margin: 0 0 4px;
            color: var(--sol-muted);
            font-size: 13px;
            line-height: 1.95;
        }

        .hb-solutions-catalogue {
            padding: 25px 6% 90px;
            background: rgba(9, 9, 8, .45);
        }

        .hb-solutions-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 25px;
            width: 100%;
            max-width: 1250px;
            margin: 0 auto;
        }

        .hb-solution-card {
            display: flex;
            min-width: 0;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--sol-border);
            background: var(--sol-panel);
            color: inherit;
            text-decoration: none;
            transition: border-color .3s ease, transform .3s ease,
                        background .3s ease;
        }

        .hb-solution-card:hover {
            transform: translateY(-5px);
            border-color: rgba(241, 213, 142, .8);
            background: #13130f;
        }

        .hb-solution-image {
            position: relative;
            height: clamp(200px, 24vw, 320px);
            overflow: hidden;
            background: #1a1915;
        }

        .hb-solution-image > * {
            width: 100%;
            height: 100%;
        }

        .hb-solution-image img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .7s ease, filter .4s ease;
        }

        .hb-solution-card:hover .hb-solution-image img {
            transform: scale(1.045);
            filter: brightness(1.05);
        }

        .hb-solution-index {
            position: absolute;
            top: 17px;
            left: 17px;
            display: grid;
            width: 37px;
            height: 37px;
            place-items: center;
            border: 1px solid rgba(241, 213, 142, .65);
            background: rgba(9, 9, 8, .82);
            color: var(--sol-gold-light);
            font-size: 11px;
            backdrop-filter: blur(8px);
        }

        .hb-solution-image-arrow {
            position: absolute;
            right: 17px;
            bottom: 17px;
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            border: 1px solid rgba(241, 213, 142, .75);
            background: rgba(9, 9, 8, .75);
            color: var(--sol-gold-light);
            transition: background .25s ease, color .25s ease;
        }

        .hb-solution-card:hover .hb-solution-image-arrow {
            background: var(--sol-gold);
            color: #11100c;
        }

        .hb-solution-content {
            display: flex;
            flex: 1;
            flex-direction: column;
            align-items: flex-start;
            padding: 27px 28px 25px;
        }

        .hb-solution-meta {
            display: flex;
            align-items: center;
            gap: 13px;
            margin-bottom: 22px;
        }

        .hb-solution-icon {
            display: grid;
            flex: 0 0 40px;
            width: 40px;
            height: 40px;
            place-items: center;
            border: 1px solid var(--sol-border);
            border-radius: 50%;
            color: var(--sol-gold-light);
        }

        .hb-solution-meta .hb-eyebrow {
            margin: 0;
            color: var(--sol-gold);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.8px;
        }

        .hb-solution-content h3 {
            margin: 0;
            color: #f5e4bd;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(23px, 2.5vw, 31px);
            font-weight: 400;
            line-height: 1.25;
        }

        .hb-solution-content > p {
            max-width: 510px;
            margin: 13px 0 24px;
            color: var(--sol-muted);
            font-size: 12px;
            line-height: 1.9;
        }

        .hb-solution-link {
            display: inline-flex;
            align-items: center;
            gap: 13px;
            margin-top: auto;
            color: var(--sol-gold-light);
            font-size: 11px;
            transition: gap .25s ease;
        }

        .hb-solution-card:hover .hb-solution-link {
            gap: 19px;
        }

        .hb-solutions-smart {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            align-items: center;
            gap: 60px;
            max-width: 1250px;
            margin: 0 auto 90px;
            padding: clamp(35px, 5vw, 65px);
            border: 1px solid var(--sol-border);
            background:
                radial-gradient(
                    ellipse at 90% 10%,
                    rgba(214, 173, 85, .09),
                    transparent 45%
                ),
                rgba(17, 17, 14, .92);
        }

        .hb-solutions-smart h2 {
            max-width: 600px;
        }

        .hb-solutions-smart-content > p:not(.hb-eyebrow) {
            max-width: 560px;
            margin: 20px 0 0;
            color: var(--sol-muted);
            font-size: 13px;
            line-height: 1.95;
        }

        .hb-solutions-smart-content .hb-solutions-button {
            margin-top: 27px;
        }

        .hb-solutions-smart-aside {
            border-top: 1px solid var(--sol-border);
        }

        .hb-smart-item {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 20px 0;
            border-bottom: 1px solid var(--sol-border);
        }

        .hb-smart-item span {
            color: var(--sol-gold);
            font-family: Georgia, "Times New Roman", serif;
            font-size: 17px;
        }

        .hb-smart-item p {
            margin: 0;
            color: #eee5d2;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 20px;
        }

        @media (min-width: 1500px) {
            .hb-solutions-hero,
            .hb-solutions-intro,
            .hb-solutions-catalogue {
                padding-right: max(6%, calc((100vw - 1450px) / 2));
                padding-left: max(6%, calc((100vw - 1450px) / 2));
            }
        }

        @media (max-width: 900px) {
            .hb-solutions-hero-inner {
                align-items: flex-start;
                flex-direction: column;
            }

            .hb-solutions-hero-side {
                flex-basis: auto;
                width: 100%;
                max-width: 330px;
                margin-top: 15px;
            }

            .hb-solutions-intro-inner {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .hb-solutions-smart {
                margin-right: 5%;
                margin-left: 5%;
                grid-template-columns: 1fr;
                gap: 35px;
            }
        }

        @media (max-width: 600px) {
            .hb-solutions-hero {
                padding: 65px 6%;
            }

            .hb-solutions-hero h1 {
                font-size: clamp(38px, 10vw, 52px);
                letter-spacing: -1px;
            }

            .hb-solutions-hero-side {
                margin-top: 10px;
            }

            .hb-solutions-intro {
                padding: 60px 6% 30px;
            }

            .hb-solutions-catalogue {
                padding: 15px 6% 60px;
            }

            .hb-solutions-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .hb-solution-image {
                height: 235px;
            }

            .hb-solution-content {
                padding: 23px 21px;
            }

            .hb-solution-content h3 {
                font-size: 25px;
            }

            .hb-solutions-smart {
                margin: 0 6% 60px;
                padding: 30px 23px;
            }

            .hb-solutions-smart h2 {
                font-size: 35px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .hb-solution-card,
            .hb-solution-image img,
            .hb-solution-image-arrow,
            .hb-solution-link,
            .hb-solutions-button {
                transition: none;
            }

            .hb-solutions-hero-video {
                display: none;
            }
        }
    </style>

</x-hb-layout>
