@php
    $c = config('hnbloom');
    $a = $c['about_page'];
    $aboutPoster = $c['images']['about'] ?? 'images/about.jpg';
    $aboutVideo  = $c['about_video'] ?? null;
    $hasVideo    = $aboutVideo && file_exists(public_path($aboutVideo));

    // Image utilisée comme fond de la page (changez ici pour une autre image)
    $aboutBg = $aboutPoster;
@endphp

<x-hb-layout
    title="Qui sommes-nous"
    description="HN BLOOM, partenaire technique au Maroc en sécurité électronique, contrôle d'accès, domotique et solutions intelligentes pour les bâtiments."
>

<div class="hb-about-page">

    {{-- FOND DE LA PAGE --}}
    <div class="hb-about-bg" style="background-image:url('{{ asset($aboutBg) }}')" aria-hidden="true"></div>

    {{-- HERO --}}
    <section class="hb-about-hero">
        <div class="hb-about-hero-content">
            <p class="hb-eyebrow">QUI SOMMES-NOUS</p>

            <h1>
                Un partenaire de confiance
                <span>pour des bâtiments plus intelligents</span>
            </h1>

            <p class="hb-about-intro">
                {{ $a['lead'] }}
            </p>

            <div class="hb-about-actions">
                <a href="{{ url('/solutions') }}" class="hb-about-btn">
                    Découvrir nos solutions
                    <span aria-hidden="true">↗</span>
                </a>

                <a href="{{ url('/contact') }}" class="hb-about-link">
                    Parlons de votre projet <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        <div class="hb-about-hero-visual">
            @if($hasVideo)
                <video
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="metadata"
                    poster="{{ asset($aboutPoster) }}"
                    aria-label="Architecture moderne et solutions intelligentes HN BLOOM"
                >
                    <source src="{{ asset($aboutVideo) }}" type="video/mp4">
                </video>
            @else
                <img
                    src="{{ asset($aboutPoster) }}"
                    alt="Architecture moderne et solutions intelligentes HN BLOOM"
                    fetchpriority="high"
                >
            @endif

            <div class="hb-about-image-overlay"></div>

            <div class="hb-about-image-caption">
                <span class="hb-about-caption-line"></span>
                <p>SMART BUILDING · CASABLANCA</p>
                <span>Des solutions pensées pour vos espaces.</span>
            </div>
        </div>
    </section>

    {{-- NOS MÉTIERS --}}
    <section class="hb-about-section" id="nos-metiers">
        <div class="hb-about-wrap">

            <div class="hb-about-section-heading">
                <div>
                    <p class="hb-eyebrow">NOTRE EXPERTISE</p>
                    <h2>Quatre rôles,<br>une seule équipe.</h2>
                </div>

                <p class="hb-about-section-description">
                    De la sélection des équipements à leur intégration,
                    nous accompagnons vos projets avec une approche
                    technique cohérente et adaptée à vos besoins.
                </p>
            </div>

            <div class="hb-about-grid hb-about-grid-4">
                @foreach($a['roles'] as $i => $r)
                    <article
                        class="hb-about-card reveal"
                        style="--d:{{ $i * 0.08 }}s"
                    >
                        <div class="hb-about-card-top">
                            <span class="hb-about-card-icon">
                                <x-icon
                                    :name="$r['icon']"
                                    :size="24"
                                    :stroke="1.5"
                                />
                            </span>

                            <span class="hb-about-card-number">
                                {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>

                        <h3>{{ $r['title'] }}</h3>
                        <p>{{ $r['text'] }}</p>

                        <span class="hb-about-card-bottom" aria-hidden="true">
                            ↗
                        </span>
                    </article>
                @endforeach
            </div>

        </div>
    </section>

    {{-- NOS VALEURS --}}
    <section class="hb-about-section hb-about-section-alt">
        <div class="hb-about-wrap">

            <div class="hb-about-section-heading">
                <div>
                    <p class="hb-eyebrow">NOS ENGAGEMENTS</p>
                    <h2>Ce qui guide<br>notre travail.</h2>
                </div>

                <p class="hb-about-section-description">
                    Chaque projet repose sur des principes essentiels :
                    la qualité des équipements, l'innovation, la durabilité
                    et un accompagnement technique de proximité.
                </p>
            </div>

            <div class="hb-about-grid hb-about-grid-4">
                @foreach($a['values'] as $i => $v)
                    <article
                        class="hb-about-value reveal"
                        style="--d:{{ $i * 0.08 }}s"
                    >
                        <span class="hb-about-value-number">
                            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>

                        <div class="hb-about-value-line"></div>

                        <h3>{{ $v['title'] }}</h3>
                        <p>{{ $v['text'] }}</p>
                    </article>
                @endforeach
            </div>

        </div>
    </section>

    {{-- NOTRE MÉTHODE --}}
    <section class="hb-about-section">
        <div class="hb-about-wrap">

            <div class="hb-about-method-heading">
                <p class="hb-eyebrow">NOTRE MÉTHODE</p>
                <h2>De l'étude à la mise en service.</h2>
                <p>
                    Une démarche structurée pour vous accompagner
                    à chaque étape de votre projet.
                </p>
            </div>

            <div class="hb-about-steps">
                @foreach($a['method'] as $i => $s)
                    <article
                        class="hb-about-step reveal"
                        style="--d:{{ $i * 0.08 }}s"
                    >
                        <span class="hb-about-step-number">
                            {{ $s['n'] }}
                        </span>

                        <div class="hb-about-step-content">
                            <h3>{{ $s['title'] }}</h3>
                            <p>{{ $s['text'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

        </div>
    </section>

</div>{{-- /hb-about-page --}}

    {{-- CTA EXISTANT --}}
    @include('partials.hb-cta')

    {{-- STYLES DE CETTE PAGE --}}
    <style>
        /* ====== FOND DE LA PAGE (image about) ====== */
        .hb-about-page {
            position: relative;
            isolation: isolate;
            clip-path: inset(0);          /* garde le fond fixe à l'intérieur de la page */
            background: #090908;
        }

        .hb-about-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
        }

        /* Voile sombre : augmentez les valeurs (.78 / .9) pour assombrir davantage */
        .hb-about-bg::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                rgba(9, 9, 8, .72) 0%,
                rgba(9, 9, 8, .88) 100%
            );
        }

        .hb-about-hero,
        .hb-about-section {
            --about-gold: #d8b15b;
            --about-gold-light: #f2d58b;
            --about-bg: #090908;
            --about-card: #10100e;
            --about-border: rgba(216, 177, 91, .23);
            --about-muted: #aaa9a2;
            color: #f5f1e8;
        }

        .hb-about-hero {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, .92fr);
            align-items: center;
            gap: clamp(36px, 6vw, 90px);
            min-height: 520px;
            padding: clamp(64px, 8vw, 112px) max(5%, calc((100vw - 1320px) / 2));
            background: transparent;
            overflow: hidden;
        }

        .hb-about-hero-content {
            position: relative;
            z-index: 2;
            max-width: 650px;
        }

        .hb-about-hero .hb-eyebrow,
        .hb-about-section .hb-eyebrow {
            margin: 0 0 22px;
            color: var(--about-gold);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .hb-about-hero h1 {
            margin: 0;
            color: #f8f4eb;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(38px, 4.1vw, 64px);
            font-weight: 400;
            line-height: 1.08;
            letter-spacing: -1.8px;
        }

        .hb-about-hero h1 span {
            display: block;
            color: var(--about-gold-light);
        }

        .hb-about-intro {
            max-width: 570px;
            margin: 25px 0 0;
            color: #c9c8c1;
            font-size: 15px;
            line-height: 1.95;
        }

        .hb-about-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 24px;
            margin-top: 34px;
        }

        .hb-about-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 28px;
            min-height: 48px;
            padding: 0 20px;
            background: linear-gradient(135deg, #f1d38b, #c99b40);
            color: #17130a;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: transform .25s ease, filter .25s ease;
        }

        .hb-about-btn:hover {
            transform: translateY(-3px);
            filter: brightness(1.08);
        }

        .hb-about-btn span,
        .hb-about-link span {
            font-size: 18px;
        }

        .hb-about-link {
            color: #e7dfcd;
            font-size: 12px;
            text-decoration: none;
            transition: color .2s ease;
        }

        .hb-about-link:hover {
            color: var(--about-gold-light);
        }

        .hb-about-hero-visual {
            position: relative;
            min-height: 390px;
            height: 100%;
            max-height: 490px;
            overflow: hidden;
            border: 1px solid var(--about-border);
            background: #171612;
        }

        .hb-about-hero-visual img,
        .hb-about-hero-visual video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform .8s ease;
        }

        .hb-about-hero-visual:hover img,
        .hb-about-hero-visual:hover video {
            transform: scale(1.035);
        }

        .hb-about-image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                rgba(0, 0, 0, .02) 35%,
                rgba(0, 0, 0, .78) 100%
            );
        }

        .hb-about-image-caption {
            position: absolute;
            right: 25px;
            bottom: 25px;
            left: 25px;
        }

        .hb-about-caption-line {
            display: block;
            width: 38px;
            height: 2px;
            margin-bottom: 15px;
            background: var(--about-gold);
        }

        .hb-about-image-caption p {
            margin: 0 0 8px;
            color: var(--about-gold-light);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .hb-about-image-caption > span:last-child {
            color: #e6e1d6;
            font-size: 13px;
        }

        .hb-about-section {
            padding: clamp(65px, 8vw, 105px) 5%;
            background: rgba(9, 9, 8, .55);
            border-top: 1px solid rgba(216, 177, 91, .15);
        }

        .hb-about-section-alt {
            background: rgba(17, 17, 15, .72);
        }

        .hb-about-wrap {
            width: 100%;
            max-width: 1250px;
            margin-inline: auto;
        }

        .hb-about-section-heading {
            display: grid;
            grid-template-columns: 1fr minmax(280px, .8fr);
            align-items: end;
            gap: 50px;
            margin-bottom: 42px;
        }

        .hb-about-section-heading .hb-eyebrow {
            margin-bottom: 16px;
        }

        .hb-about-section-heading h2,
        .hb-about-method-heading h2 {
            margin: 0;
            color: #f5f0e5;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(32px, 3.3vw, 46px);
            font-weight: 400;
            line-height: 1.15;
            letter-spacing: -.8px;
        }

        .hb-about-section-description {
            max-width: 500px;
            margin: 0 0 5px;
            color: #bcbbb4;
            font-size: 14px;
            line-height: 1.9;
        }

        .hb-about-grid {
            display: grid;
            gap: 17px;
        }

        .hb-about-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .hb-about-card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 245px;
            padding: 25px 22px;
            border: 1px solid var(--about-border);
            background: linear-gradient(145deg, rgba(20, 20, 17, .92), rgba(12, 12, 11, .92));
            transition: border-color .3s ease, transform .3s ease,
                        background .3s ease;
        }

        .hb-about-card:hover {
            transform: translateY(-5px);
            border-color: rgba(232, 193, 106, .7);
            background: linear-gradient(145deg, #1a1812, #0e0e0c);
        }

        .hb-about-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .hb-about-card-icon {
            display: grid;
            width: 48px;
            height: 48px;
            place-items: center;
            border: 1px solid rgba(216, 177, 91, .65);
            border-radius: 50%;
            color: var(--about-gold-light);
        }

        .hb-about-card-number,
        .hb-about-value-number {
            color: #8f7f5c;
            font-size: 11px;
            letter-spacing: 1px;
        }

        .hb-about-card h3,
        .hb-about-value h3,
        .hb-about-step h3 {
            margin: 0 0 12px;
            color: #f1dfb5;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 21px;
            font-weight: 400;
            line-height: 1.25;
        }

        .hb-about-card p,
        .hb-about-value p,
        .hb-about-step p {
            margin: 0;
            color: #b4b3ac;
            font-size: 12px;
            line-height: 1.85;
        }

        .hb-about-card-bottom {
            align-self: flex-end;
            margin-top: auto;
            padding-top: 18px;
            color: var(--about-gold);
            font-size: 18px;
            opacity: 0;
            transform: translateX(-5px);
            transition: opacity .25s ease, transform .25s ease;
        }

        .hb-about-card:hover .hb-about-card-bottom {
            opacity: 1;
            transform: translateX(0);
        }

        .hb-about-value {
            min-height: 205px;
            padding: 23px 22px;
            border: 1px solid var(--about-border);
            background: rgba(0, 0, 0, .35);
            transition: border-color .3s ease, transform .3s ease;
        }

        .hb-about-value:hover {
            transform: translateY(-4px);
            border-color: rgba(232, 193, 106, .65);
        }

        .hb-about-value-number {
            display: block;
            margin-bottom: 21px;
        }

        .hb-about-value-line {
            width: 35px;
            height: 2px;
            margin-bottom: 22px;
            background: var(--about-gold);
        }

        .hb-about-value h3 {
            font-size: 20px;
        }

        .hb-about-method-heading {
            max-width: 650px;
            margin-bottom: 48px;
        }

        .hb-about-method-heading .hb-eyebrow {
            margin-bottom: 16px;
        }

        .hb-about-method-heading > p:last-child {
            margin: 17px 0 0;
            color: #bcbbb4;
            font-size: 14px;
            line-height: 1.8;
        }

        .hb-about-steps {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            border-top: 1px solid var(--about-border);
        }

        .hb-about-step {
            position: relative;
            padding: 27px 20px 10px 0;
        }

        .hb-about-step:not(:first-child) {
            padding-left: 20px;
            border-left: 1px solid rgba(216, 177, 91, .12);
        }

        .hb-about-step-number {
            display: block;
            margin-bottom: 25px;
            color: var(--about-gold);
            font-family: Georgia, "Times New Roman", serif;
            font-size: 29px;
        }

        .hb-about-step h3 {
            font-size: 19px;
        }

        /* Responsive */
        @media (max-width: 1050px) {
            .hb-about-hero {
                grid-template-columns: 1fr 0.8fr;
                gap: 35px;
            }

            .hb-about-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .hb-about-steps {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                row-gap: 25px;
            }

            .hb-about-step:nth-child(4) {
                border-left: 0;
                padding-left: 0;
            }
        }

        @media (max-width: 700px) {
            .hb-about-hero {
                grid-template-columns: 1fr;
                gap: 35px;
                padding: 65px 6%;
            }

            .hb-about-hero h1 {
                font-size: clamp(37px, 10vw, 52px);
                letter-spacing: -1px;
            }

            .hb-about-hero-visual {
                min-height: 300px;
                max-height: 380px;
            }

            .hb-about-section {
                padding: 65px 6%;
            }

            .hb-about-section-heading {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .hb-about-grid-4 {
                grid-template-columns: 1fr;
            }

            .hb-about-card {
                min-height: 210px;
            }

            .hb-about-steps {
                grid-template-columns: 1fr;
            }

            .hb-about-step,
            .hb-about-step:not(:first-child),
            .hb-about-step:nth-child(4) {
                display: grid;
                grid-template-columns: 55px 1fr;
                gap: 12px;
                padding: 23px 0;
                border-left: 0;
                border-bottom: 1px solid var(--about-border);
            }

            .hb-about-step-number {
                margin: 0;
            }

            .hb-about-step-content {
                min-width: 0;
            }

            .hb-about-actions {
                align-items: flex-start;
                flex-direction: column;
                gap: 18px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .hb-about-hero-visual img,
            .hb-about-hero-visual video,
            .hb-about-card,
            .hb-about-value,
            .hb-about-btn {
                transition: none;
            }
        }
    </style>

</x-hb-layout>
