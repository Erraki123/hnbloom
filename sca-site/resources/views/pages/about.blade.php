@php
    $c = config('hnbloom');
    $a = $c['about_page'];
    $aboutPoster = $c['images']['about'] ?? 'images/about.jpg';
    $aboutVideo  = $c['about_video'] ?? null;
    $hasVideo    = $aboutVideo && file_exists(public_path($aboutVideo));

    // Image utilisée comme fond de la page + backup de la vidéo
    $aboutBg = $aboutPoster;
@endphp

<x-hb-layout
    title="Qui sommes-nous"
    description="HN BLOOM, partenaire technique au Maroc en sécurité électronique, contrôle d'accès, domotique et solutions intelligentes pour les bâtiments."
>

<div class="hb-about-page">

    {{-- Barre de progression dorée --}}
    <div class="hb-ab-progress" aria-hidden="true"><span></span></div>

    {{-- FOND DE LA PAGE (photo) : visible dans les sections en dessous du hero --}}
    <div class="hb-about-bg" style="background-image:url('{{ asset($aboutBg) }}')" aria-hidden="true"></div>

    {{-- HERO (plein écran) : gauche noire + lumière, vidéo/photo à droite --}}
    <section class="hb-about-hero">

        <div class="hb-about-hero-media" style="background-image:url('{{ asset($aboutBg) }}')" aria-hidden="true">
            @if($hasVideo)
                <video
                    class="hb-ab-video"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="auto"
                    poster="{{ asset($aboutPoster) }}"
                >
                    <source src="{{ asset($aboutVideo) }}" type="video/mp4">
                </video>
            @endif
            <div class="hb-about-hero-shade"></div>
        </div>

        <div class="hb-about-hero-content">
            <p class="hb-eyebrow hb-ab-in" style="--i:0">QUI SOMMES-NOUS</p>

            <h1 class="hb-ab-in" style="--i:1">
                Un partenaire de confiance
                <span>pour des bâtiments plus intelligents</span>
            </h1>

            <p class="hb-about-intro hb-ab-in" style="--i:2">
                {{ $a['lead'] }}
            </p>

            <div class="hb-about-actions hb-ab-in" style="--i:3">
                <a href="{{ url('/solutions') }}" class="hb-about-btn">
                    Découvrir nos solutions
                    <span aria-hidden="true">↗</span>
                </a>

                <a href="{{ url('/contact') }}" class="hb-about-link">
                    Parlons de votre projet <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        <div class="hb-about-image-caption hb-ab-in" style="--i:4">
            <span class="hb-about-caption-line"></span>
            <p>SMART BUILDING · CASABLANCA</p>
            <span>Des solutions pensées pour vos espaces.</span>
        </div>

        <a href="#nos-metiers" class="hb-ab-scroll" aria-label="Défiler vers nos métiers"><span></span></a>
    </section>

    {{-- NOS MÉTIERS --}}
    <section class="hb-about-section" id="nos-metiers">
        <div class="hb-about-wrap">

            <div class="hb-about-section-heading">
                <div>
                    <p class="hb-eyebrow hb-ab-rv" style="--d:0s">NOTRE EXPERTISE</p>
                    <h2 class="hb-ab-rv" style="--d:.12s">Quatre rôles,<br>une seule équipe.</h2>
                </div>

                <p class="hb-about-section-description hb-ab-rv" style="--d:.28s">
                    De la sélection des équipements à leur intégration,
                    nous accompagnons vos projets avec une approche
                    technique cohérente et adaptée à vos besoins.
                </p>
            </div>

            <div class="hb-about-grid hb-about-grid-4">
                @foreach($a['roles'] as $i => $r)
                    <article
                        class="hb-about-card hb-ab-rv"
                        style="--d:{{ $i * 0.12 }}s"
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
                    <p class="hb-eyebrow hb-ab-rv" style="--d:0s">NOS ENGAGEMENTS</p>
                    <h2 class="hb-ab-rv" style="--d:.12s">Ce qui guide<br>notre travail.</h2>
                </div>

                <p class="hb-about-section-description hb-ab-rv" style="--d:.28s">
                    Chaque projet repose sur des principes essentiels :
                    la qualité des équipements, l'innovation, la durabilité
                    et un accompagnement technique de proximité.
                </p>
            </div>

            <div class="hb-about-grid hb-about-grid-4">
                @foreach($a['values'] as $i => $v)
                    <article
                        class="hb-about-value hb-ab-rv"
                        style="--d:{{ $i * 0.12 }}s"
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
                <p class="hb-eyebrow hb-ab-rv" style="--d:0s">NOTRE MÉTHODE</p>
                <h2 class="hb-ab-rv" style="--d:.12s">De l'étude à la mise en service.</h2>
                <p class="hb-ab-rv" style="--d:.28s">
                    Une démarche structurée pour vous accompagner
                    à chaque étape de votre projet.
                </p>
            </div>

            <div class="hb-about-steps">
                @foreach($a['method'] as $i => $s)
                    <article
                        class="hb-about-step hb-ab-rv"
                        style="--d:{{ $i * 0.12 }}s"
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
        /* ====== FOND DE LA PAGE (photo) ====== */
        .hb-about-page {
            position: relative;
            isolation: isolate;
            clip-path: inset(0);          /* garde le fond fixe à l'intérieur de la page */
            background: #090908;
            font-family: Arial, Helvetica, sans-serif;
        }

        .hb-about-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
        }

        /* Voile sombre cinéma : bords assombris, lumière au centre. */
        .hb-about-bg::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 50% 40%, rgba(5, 6, 5, 0) 0%, rgba(5, 6, 5, .35) 60%, rgba(5, 6, 5, .8) 100%),
                linear-gradient(180deg, rgba(9, 9, 8, .62) 0%, rgba(9, 9, 8, .88) 100%);
        }

        .hb-about-hero,
        .hb-about-section {
            --about-gold: #d6ad55;
            --about-gold-light: #f1d58e;
            --about-bg: #090908;
            --about-card: #10100e;
            --about-border: rgba(214, 173, 85, .25);
            --about-muted: #b1b0a8;
            color: #f5f1e8;
        }

        /* ====== HERO : PLEIN ÉCRAN ====== */
        .hb-about-hero {
            position: relative;
            display: flex;
            align-items: center;
            min-height: 100vh;
            min-height: 100svh;
            padding: 110px max(5%, calc((100vw - 1320px) / 2));
            background: #090908;
            overflow: hidden;
        }

        /* Lumière dorée douce derrière le texte (côté noir) */
        .hb-about-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            background:
                radial-gradient(ellipse 50% 70% at 20% 50%, rgba(214, 173, 85, .14) 0%, rgba(214, 173, 85, .04) 45%, transparent 70%);
        }

        /* Photo / vidéo : zone de droite.
           Le logo est décalé vers la droite grâce à "30% center" (background ET video).
           Logo encore plus à droite : 30% -> 15%.
           Logo plus à gauche : 30% -> 50%. */
        .hb-about-hero-media {
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            width: 58%;
            z-index: 0;
            background-color: #090908;
            background-position: 30% center;
            background-size: cover;
            background-repeat: no-repeat;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, .3) 16%, rgba(0, 0, 0, .8) 36%, #000 52%);
                    mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, .3) 16%, rgba(0, 0, 0, .8) 36%, #000 52%);
            animation: hbAbFade 1.4s .1s ease-out backwards;
        }

        .hb-about-hero-media video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: 30% center;
            animation: hbAbImgIn 2.2s .2s cubic-bezier(.2, .7, .2, 1) backwards;
        }

        /* Ombre légère en haut et en bas uniquement */
        .hb-about-hero-shade {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(9, 9, 8, .45) 0%, rgba(9, 9, 8, 0) 35%, rgba(9, 9, 8, .7) 100%);
        }

        .hb-about-hero-content {
            position: relative;
            z-index: 2;
            max-width: 620px;
        }

        /* ====== TYPOGRAPHIE UNIQUE (identique à la page Domotique) ====== */
        .hb-about-hero .hb-eyebrow,
        .hb-about-section .hb-eyebrow {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 0 0 18px;
            color: var(--about-gold-light);
            font: 700 10.5px/1.5 Arial, Helvetica, sans-serif;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .hb-about-hero .hb-eyebrow::before,
        .hb-about-section .hb-eyebrow::before {
            content: "";
            flex: 0 0 38px;
            height: 1px;
            background: #dfb95f;
            transform-origin: left;
            animation: hbAbRule 1s .5s ease-out both;
        }

        .hb-about-hero h1 {
            margin: 0;
            color: #fffdf7;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(32px, 3.7vw, 52px);
            font-weight: 400;
            line-height: 1.15;
            letter-spacing: -.6px;
            text-shadow: 0 2px 20px rgba(0, 0, 0, .45);
        }

        .hb-about-hero h1 span {
            display: block;
            margin-top: 4px;
            color: #f2cf7b;
            font-style: italic;
        }

        .hb-about-intro {
            max-width: 520px;
            margin: 20px 0 0;
            color: #e8e4db;
            font: 400 13.5px/1.9 Arial, Helvetica, sans-serif;
            text-shadow: 0 1px 12px rgba(0, 0, 0, .55);
        }

        .hb-about-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 24px;
            margin-top: 28px;
        }

        .hb-about-btn {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 22px;
            min-height: 48px;
            padding: 0 24px;
            background: linear-gradient(135deg, #f1d38b, #c99b40);
            color: #17130a;
            font: 700 11px/1.4 Arial, Helvetica, sans-serif;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-decoration: none;
            transition: transform .3s ease, filter .3s ease;
        }

        .hb-about-btn:hover {
            transform: translateY(-3px);
            filter: brightness(1.08);
        }

        .hb-about-btn::after {
            content: "";
            position: absolute;
            top: 0; bottom: 0; left: -70%;
            width: 40%;
            pointer-events: none;
            background: linear-gradient(100deg, transparent, rgba(255, 244, 214, .5), transparent);
            transform: skewX(-18deg);
        }
        .hb-about-btn:hover::after { left: 130%; transition: left .9s ease; }

        .hb-about-btn span,
        .hb-about-link span {
            font-size: 18px;
            transition: transform .3s ease;
        }
        .hb-about-btn:hover span,
        .hb-about-link:hover span { transform: translateX(4px); }

        .hb-about-link {
            color: #e7dfcd;
            font: 700 11px/1.4 Arial, Helvetica, sans-serif;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-decoration: none;
            transition: color .2s ease;
        }

        .hb-about-link:hover {
            color: var(--about-gold-light);
        }

        /* Légende en bas à droite du hero */
        .hb-about-image-caption {
            position: absolute;
            z-index: 2;
            right: max(5%, calc((100vw - 1320px) / 2));
            bottom: 40px;
            max-width: 280px;
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
            font: 700 10px/1.4 Arial, Helvetica, sans-serif;
            letter-spacing: 2px;
        }

        .hb-about-image-caption > span:last-child {
            color: #e6e1d6;
            font: 400 13px/1.5 Arial, Helvetica, sans-serif;
        }

        /* ====== SECTIONS (fond = photo fixe de la page) ====== */
        .hb-about-section {
            position: relative;
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
            margin-bottom: 18px;
        }

        .hb-about-section-heading h2,
        .hb-about-method-heading h2 {
            margin: 0;
            color: #f8f4eb;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(34px, 4vw, 49px);
            font-weight: 400;
            line-height: 1.12;
            letter-spacing: -.7px;
        }

        .hb-about-section-description {
            max-width: 500px;
            margin: 0 0 5px;
            color: var(--about-muted);
            font: 400 13px/1.95 Arial, Helvetica, sans-serif;
        }

        .hb-about-grid {
            display: grid;
            gap: 17px;
        }

        .hb-about-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .hb-about-card,
        .hb-about-value {
            position: relative;
            overflow: hidden;
        }

        .hb-about-card {
            display: flex;
            flex-direction: column;
            min-height: 245px;
            padding: 25px 22px;
            border: 1px solid var(--about-border);
            background: linear-gradient(145deg, rgba(20, 20, 17, .92), rgba(12, 12, 11, .92));
            transform: perspective(1100px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg));
            transition: border-color .4s ease, transform .35s ease-out,
                        background .4s ease, box-shadow .5s ease;
        }

        .hb-about-card:hover {
            transform: perspective(1100px) translateY(-8px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg));
            border-color: rgba(232, 193, 106, .7);
            background: linear-gradient(145deg, #1a1812, #0e0e0c);
            box-shadow: 0 30px 60px -20px rgba(0, 0, 0, .7), 0 0 40px -10px rgba(214, 173, 85, .35);
        }

        /* Ligne dorée + halo qui suit la souris */
        .hb-about-card::before,
        .hb-about-value::before {
            content: "";
            position: absolute;
            z-index: 2;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #f1d58e, transparent);
            transform: scaleX(0);
            transition: transform .6s cubic-bezier(.2, .7, .2, 1);
        }
        .hb-about-card:hover::before,
        .hb-about-value:hover::before { transform: scaleX(1); }

        .hb-about-card::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: 0;
            transition: opacity .4s ease;
            background: radial-gradient(360px circle at var(--mx, 50%) var(--my, 50%), rgba(241, 213, 142, .13), transparent 60%);
        }
        .hb-about-card:hover::after { opacity: 1; }

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
            transition: background .35s ease, color .35s ease, transform .6s ease;
        }
        .hb-about-card:hover .hb-about-card-icon {
            background: var(--about-gold);
            color: #11100c;
            transform: rotate(360deg);
        }

        .hb-about-card-number,
        .hb-about-value-number {
            color: #8f7f5c;
            font: 400 11px/1 Arial, Helvetica, sans-serif;
            letter-spacing: 1px;
        }

        .hb-about-card h3,
        .hb-about-value h3,
        .hb-about-step h3 {
            margin: 0 0 12px;
            color: #f5e4bd;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 21px;
            font-weight: 400;
            line-height: 1.25;
            transition: color .3s ease;
        }
        .hb-about-card:hover h3,
        .hb-about-value:hover h3 { color: #fff1cf; }

        .hb-about-card p,
        .hb-about-value p,
        .hb-about-step p {
            margin: 0;
            color: #b4b3ac;
            font: 400 12px/1.85 Arial, Helvetica, sans-serif;
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
            transition: border-color .4s ease, transform .4s ease, box-shadow .5s ease;
        }

        .hb-about-value:hover {
            transform: translateY(-6px);
            border-color: rgba(232, 193, 106, .65);
            box-shadow: 0 24px 50px -20px rgba(0, 0, 0, .7), 0 0 36px -12px rgba(214, 173, 85, .3);
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
            transition: width .5s cubic-bezier(.2, .7, .2, 1);
        }
        .hb-about-value:hover .hb-about-value-line { width: 70px; }

        .hb-about-value h3 {
            font-size: 20px;
        }

        .hb-about-method-heading {
            max-width: 650px;
            margin-bottom: 48px;
        }

        .hb-about-method-heading .hb-eyebrow {
            margin-bottom: 18px;
        }

        .hb-about-method-heading > p:last-child {
            margin: 17px 0 0;
            color: var(--about-muted);
            font: 400 13px/1.95 Arial, Helvetica, sans-serif;
        }

        .hb-about-steps {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            border-top: 1px solid var(--about-border);
        }

        .hb-about-step {
            position: relative;
            padding: 27px 20px 10px 0;
            transition: transform .4s ease;
        }
        .hb-about-step:hover { transform: translateY(-4px); }

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
            transition: color .3s ease;
        }
        .hb-about-step:hover .hb-about-step-number { color: var(--about-gold-light); }

        .hb-about-step h3 {
            font-size: 19px;
        }

        /* ====== ANIMATIONS ====== */
        html { scroll-behavior: smooth; }

        @keyframes hbAbUp     { from { opacity: 0; transform: translateY(26px); } to { opacity: 1; transform: none; } }
        @keyframes hbAbRise   { from { opacity: 0; transform: translateY(48px) scale(.97); } to { opacity: 1; transform: none; } }
        @keyframes hbAbRule   { from { transform: scaleX(0); } to { transform: scaleX(1); } }
        @keyframes hbAbFade   { from { opacity: 0; } to { opacity: 1; } }
        @keyframes hbAbScroll { 0% { transform: translateY(-100%); } 60%, 100% { transform: translateY(100%); } }
        @keyframes hbAbImgIn  { from { transform: scale(1.2); } to { transform: scale(1); } }

        /* Hero en cascade */
        .hb-ab-in { animation: hbAbUp 1s calc(.15s + var(--i, 0) * .16s) cubic-bezier(.2, .7, .2, 1) backwards; }

        /* Apparition au scroll */
        .hb-ab-rv:not(.is-in) { opacity: 0; }
        .hb-ab-rv.is-in { animation: hbAbRise 1.1s var(--d, 0s) cubic-bezier(.2, .7, .2, 1) backwards; }

        /* Filet doré en haut de chaque section */
        .hb-about-section::before {
            content: "";
            position: absolute;
            top: 0; left: 6%; right: 6%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--about-gold), transparent);
            animation: hbAbFade 1.6s .2s ease-out both;
        }

        /* Barre de progression dorée */
        .hb-ab-progress { position: fixed; z-index: 9999; top: 0; left: 0; right: 0; height: 2px; pointer-events: none; }
        .hb-ab-progress span { display: block; width: 100%; height: 100%; background: linear-gradient(90deg, #d6ad55, #f1d58e); transform: scaleX(0); transform-origin: left; }

        /* Indicateur de défilement */
        .hb-ab-scroll { position: absolute; z-index: 2; left: 50%; bottom: 28px; display: block; width: 1px; height: 56px; overflow: hidden; background: rgba(242, 211, 138, .25); transform: translateX(-50%); animation: hbAbFade 1s 1.6s backwards; }
        .hb-ab-scroll span { display: block; width: 100%; height: 100%; background: #f2d38a; animation: hbAbScroll 2.2s 2s ease-in-out infinite; }

        /* Responsive */
        @media (max-width: 1050px) {
            /* Écran moyen : photo un peu plus large mais atténuée */
            .hb-about-hero-media {
                width: 64%;
                opacity: .8;
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

        @media (max-width: 760px) {
            .hb-about-card { transform: none !important; }
        }

        @media (max-width: 700px) {
            .hb-about-hero {
                padding: 100px 6% 90px;
            }

            .hb-about-hero h1 {
                font-size: clamp(30px, 8.2vw, 40px);
            }

            /* Mobile : photo/vidéo en plein fond, atténuée */
            .hb-about-hero-media {
                width: 100%;
                opacity: .5;
                background-position: center center;
                -webkit-mask-image: linear-gradient(180deg, #000 0%, rgba(0, 0, 0, .6) 70%, transparent 100%);
                        mask-image: linear-gradient(180deg, #000 0%, rgba(0, 0, 0, .6) 70%, transparent 100%);
            }

            .hb-about-hero-media video {
                object-position: center center;
            }

            /* Légende masquée sur mobile pour ne pas surcharger */
            .hb-about-image-caption {
                display: none;
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
            .hb-ab-rv:not(.is-in) { opacity: 1; }
            .hb-ab-in, .hb-about-hero-media, .hb-about-hero-media video, .hb-ab-rv, .hb-ab-scroll, .hb-ab-scroll span,
            .hb-about-section::before, .hb-about-hero .hb-eyebrow::before, .hb-about-section .hb-eyebrow::before { animation: none !important; }
            .hb-about-card { transform: none !important; }
            .hb-about-card,
            .hb-about-value,
            .hb-about-btn {
                transition: none;
            }
            html { scroll-behavior: auto; }
        }
    </style>
    <noscript><style>.hb-ab-rv:not(.is-in){opacity:1 !important}</style></noscript>

    <script>
    (function () {
        var reduce = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
        var all = function (s) { return Array.prototype.slice.call(document.querySelectorAll(s)); };

        /* Apparition au scroll */
        var rv = all('.hb-ab-rv');
        if (!('IntersectionObserver' in window) || reduce) {
            rv.forEach(function (el) { el.classList.add('is-in'); });
        } else {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
            rv.forEach(function (el) { io.observe(el); });
        }

        /* Vidéo de fond : pause hors écran ; en mode économie de données ou
           mouvement réduit, la vidéo est retirée et la photo (backup) reste visible */
        var v = document.querySelector('.hb-ab-video');
        if (v) {
            v.muted = true;
            var saver = navigator.connection && navigator.connection.saveData;
            if (saver || reduce) {
                v.pause();
                v.remove();
            } else if ('IntersectionObserver' in window) {
                new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) { var p = v.play(); if (p && p.catch) p.catch(function () {}); }
                        else { v.pause(); }
                    });
                }, { threshold: 0.1 }).observe(v);
            }
        }

        /* Barre de progression */
        var bar = document.querySelector('.hb-ab-progress span');
        if (bar && !reduce) {
            var ticking = false;
            var upd = function () {
                var h = document.documentElement.scrollHeight - window.innerHeight;
                bar.style.transform = 'scaleX(' + (h > 0 ? Math.min(window.scrollY / h, 1) : 0) + ')';
                ticking = false;
            };
            window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(upd); } }, { passive: true });
            upd();
        }

        /* Inclinaison 3D + halo doré sur les cartes (souris uniquement) */
        if (!reduce && window.matchMedia('(hover:hover) and (pointer:fine)').matches) {
            all('.hb-about-card').forEach(function (card) {
                var raf = 0, ev = null;
                var apply = function () {
                    raf = 0;
                    var r = card.getBoundingClientRect();
                    var x = (ev.clientX - r.left) / r.width, y = (ev.clientY - r.top) / r.height;
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
    })();
    </script>

</x-hb-layout>
