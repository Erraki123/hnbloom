@php
    $c = config('hnbloom');

    // Image de fond (optionnelle) : hero.jpg, sinon about.jpg
    $bgPath = collect([$c['images']['hero'] ?? null, $c['images']['about'] ?? null])
        ->first(fn ($p) => $p && file_exists(public_path($p)));
    $bg = $bgPath ? asset($bgPath) : null;

    // Cubes 3D décoratifs (animation CSS uniquement) : taille, position, durée, délai
    $cubes = [
        ['s' => 120, 'x' => '5%',  'y' => '14%', 'd' => 34, 'o' => 0],
        ['s' => 70,  'x' => '84%', 'y' => '10%', 'd' => 26, 'o' => -6],
        ['s' => 150, 'x' => '86%', 'y' => '58%', 'd' => 40, 'o' => -12],
        ['s' => 60,  'x' => '14%', 'y' => '74%', 'd' => 22, 'o' => -3],
    ];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace revendeur — {{ $c['name'] }}</title>
    <meta name="robots" content="noindex">
    <script>document.documentElement.classList.add('hb-js');</script>
    <link rel="stylesheet" href="{{ asset('css/Site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/hnbloom-laravel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/hb-pages.css') }}">
    @vite(['resources/js/app.js'])
    @stack('head')

    <style>
        /* ================= SCÈNE ================= */
        .lg-scene {
            --g: #d8b45e; --gl: #f0d69a; --muted: #aaa69c;
            --border: rgba(216,180,94,.28); --panel: rgba(16,16,14,.9);
            position: relative; isolation: isolate; overflow: hidden;
            min-height: 100vh; min-height: 100svh;
            display: grid; place-items: center;
            padding: 48px clamp(18px, 4vw, 56px);
            background: #070706; color: #f6f1e7;
        }
        .lg-bg { position: absolute; inset: 0; z-index: -4; background: #070706 center / cover no-repeat; }
        .lg-bg::after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7,7,6,.78), rgba(7,7,6,.93)); }

        /* Lueurs : dégradé simple, sans filtre ni animation (léger) */
        .lg-glow { position: absolute; z-index: -3; width: 700px; height: 700px; border-radius: 50%; pointer-events: none; }
        .lg-glow.a { top: -260px; left: -200px; background: radial-gradient(circle, rgba(216,180,94,.22), transparent 68%); }
        .lg-glow.b { right: -240px; bottom: -280px; background: radial-gradient(circle, rgba(190,140,50,.18), transparent 68%); }

        /* Sol en perspective */
        .lg-floor-wrap {
            position: absolute; inset: 0; z-index: -2; perspective: 600px; overflow: hidden; pointer-events: none;
            -webkit-mask-image: linear-gradient(to top, #000 5%, transparent 72%);
            mask-image: linear-gradient(to top, #000 5%, transparent 72%);
        }
        .lg-floor {
            position: absolute; left: -60%; right: -60%; bottom: -30%; height: 85%;
            background-image: linear-gradient(rgba(216,180,94,.22) 1px, transparent 1px), linear-gradient(90deg, rgba(216,180,94,.22) 1px, transparent 1px);
            background-size: 64px 64px;
            transform: rotateX(64deg); will-change: transform;
            animation: lg-floor 6s linear infinite;
        }
        @keyframes lg-floor { from { transform: rotateX(64deg) translateY(0); } to { transform: rotateX(64deg) translateY(64px); } }

        /* Cubes */
        .lg-cubes { position: absolute; inset: 0; z-index: -1; perspective: 900px; pointer-events: none; }
        .lg-float { position: absolute; will-change: transform; animation: lg-float 9s ease-in-out infinite alternate; }
        .lg-cube { position: relative; width: var(--s); height: var(--s); transform-style: preserve-3d; will-change: transform; animation: lg-spin var(--dur) linear infinite; animation-delay: var(--delay); }
        .lg-cube i { position: absolute; inset: 0; border: 1px solid rgba(216,180,94,.38); background: linear-gradient(135deg, rgba(216,180,94,.12), rgba(216,180,94,.02)); }
        .lg-cube i:nth-child(1) { transform: translateZ(calc(var(--s) / 2)); }
        .lg-cube i:nth-child(2) { transform: rotateY(180deg) translateZ(calc(var(--s) / 2)); }
        .lg-cube i:nth-child(3) { transform: rotateY(90deg) translateZ(calc(var(--s) / 2)); }
        .lg-cube i:nth-child(4) { transform: rotateY(-90deg) translateZ(calc(var(--s) / 2)); }
        .lg-cube i:nth-child(5) { transform: rotateX(90deg) translateZ(calc(var(--s) / 2)); }
        .lg-cube i:nth-child(6) { transform: rotateX(-90deg) translateZ(calc(var(--s) / 2)); }
        @keyframes lg-spin { from { transform: rotateX(0) rotateY(0); } to { transform: rotateX(360deg) rotateY(360deg); } }
        @keyframes lg-float { from { transform: translateY(-16px); } to { transform: translateY(16px); } }

        /* ================= MISE EN PAGE ================= */
        .lg-wrap { position: relative; z-index: 1; width: min(100%, 1060px); display: grid; grid-template-columns: 1.05fr 1fr; gap: clamp(28px, 6vw, 80px); align-items: center; }

        .lg-brand { animation: lg-in .7s cubic-bezier(.2,.7,.2,1) both; }
        .lg-logo { display: inline-flex; align-items: center; gap: 14px; margin-bottom: 38px; text-decoration: none; }
        .lg-mono { display: grid; place-items: center; width: 52px; height: 52px; border: 1px solid var(--g); color: var(--gl); font-family: 'Playfair Display', serif; font-size: 30px; background: linear-gradient(145deg, rgba(216,180,94,.18), rgba(216,180,94,.03)); box-shadow: 0 0 0 5px rgba(216,180,94,.08), 0 14px 30px rgba(0,0,0,.5); }
        .lg-logo span:last-child { font-family: 'Playfair Display', serif; font-size: 20px; letter-spacing: .18em; color: var(--gl); }
        .lg-kicker { margin: 0 0 14px; color: var(--g); font-size: 10px; font-weight: 600; letter-spacing: 2.6px; }
        .lg-brand h2 { margin: 0; font-family: 'Playfair Display', Georgia, serif; font-size: clamp(34px, 4.6vw, 56px); font-weight: 400; line-height: 1.08; letter-spacing: -1px; color: #f6f1e7; }
        .lg-brand h2 em { font-style: normal; color: var(--gl); }
        .lg-lead { max-width: 430px; margin: 20px 0 0; color: #c2beb3; font-size: 14px; line-height: 1.9; }
        .lg-points { display: grid; gap: 14px; margin: 34px 0 0; padding: 0; list-style: none; }
        .lg-points li { display: flex; align-items: center; gap: 14px; color: #e4ded0; font-size: 13px; }
        .lg-points b { display: grid; flex: none; place-items: center; width: 30px; height: 30px; border: 1px solid var(--border); border-radius: 50%; color: var(--g); }

        /* ================= CARTE (statique, pas de suivi souris) ================= */
        .lg-card-wrap { animation: lg-in .7s .08s cubic-bezier(.2,.7,.2,1) both; }
        .lg-card {
            position: relative; padding: 42px 38px 34px;
            border: 1px solid var(--border); background: var(--panel);
            box-shadow: 0 40px 80px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.03) inset, 0 0 70px rgba(216,180,94,.08);
        }
        .lg-card::before { content: ""; position: absolute; inset: 0; pointer-events: none; background: linear-gradient(160deg, rgba(240,214,154,.07), transparent 38%); }
        .lg-card::after { content: ""; position: absolute; top: -1px; left: 12%; right: 12%; height: 2px; pointer-events: none; background: linear-gradient(90deg, transparent, var(--g), transparent); }
        .lg-card > * { position: relative; }

        .lg-card h1 { margin: 0 0 26px; font-family: 'Playfair Display', Georgia, serif; font-size: clamp(30px, 3.4vw, 38px); font-weight: 400; color: #f6f1e7; }

        /* ---- Champs (Breeze : x-input-label, x-text-input, x-input-error…) ---- */
        .lg-card label:not(.inline-flex):not(.lg-check) { display: block; margin-bottom: 8px; color: var(--muted); font-size: 10px; font-weight: 500; letter-spacing: .16em; text-transform: uppercase; }
        .lg-card input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]):not([type="submit"]),
        .lg-card select, .lg-card textarea {
            display: block; width: 100%; padding: 15px 16px; border: 1px solid var(--border); border-radius: 0;
            background: #0a0a08; color: #f6f1e7; font: inherit; font-size: 15px;
            box-shadow: inset 0 3px 10px rgba(0,0,0,.55); transition: border-color .25s, box-shadow .25s, background .25s;
        }
        .lg-card input::placeholder { color: #5f5b52; }
        .lg-card input:focus, .lg-card select:focus, .lg-card textarea:focus {
            outline: none; border-color: var(--g); background: #0d0d0a;
            box-shadow: inset 0 3px 10px rgba(0,0,0,.55), 0 0 0 3px rgba(216,180,94,.16);
        }
        .lg-card input:-webkit-autofill { -webkit-text-fill-color: #f6f1e7; -webkit-box-shadow: 0 0 0 40px #0a0a08 inset; caret-color: #f6f1e7; }
        .lg-input-wrap { position: relative; }
        .lg-input-wrap input.has-eye { padding-right: 54px !important; }
        .lg-eye { position: absolute; top: 50%; right: 6px; display: grid; place-items: center; width: 42px; height: 42px; transform: translateY(-50%); border: 0; background: transparent; color: var(--muted); cursor: pointer; }
        .lg-eye:hover, .lg-eye[aria-pressed="true"] { color: var(--gl); }
        .lg-eye svg { width: 20px; height: 20px; } .lg-eye .off { display: none; }
        .lg-eye[aria-pressed="true"] .on { display: none; } .lg-eye[aria-pressed="true"] .off { display: block; }
        .lg-caps { display: none; margin: 7px 0 0; color: var(--gl); font-size: 12px; } .lg-caps.is-on { display: block; }

        .lg-card ul.text-red-600, .lg-card .text-red-600 { margin: 7px 0 0; padding: 0; list-style: none; color: #ef8a86; font-size: 12px; }
        .lg-card .text-green-600 { color: var(--gl); font-size: 13px; line-height: 1.6; }
        .lg-card .text-gray-600, .lg-card .text-gray-700, .lg-card .text-gray-500 { color: #c9c5ba; }
        .lg-card p { line-height: 1.7; }
        .lg-card a { color: var(--gl); text-decoration: none; border-bottom: 1px solid transparent; transition: border-color .2s; }
        .lg-card a:hover { border-bottom-color: var(--gl); }
        .lg-card a.underline, .lg-card .underline { text-decoration: none; }

        /* Case à cocher */
        .lg-card input[type="checkbox"] {
            -webkit-appearance: none; appearance: none; flex: none; width: 20px; height: 20px; margin: 0; cursor: pointer;
            border: 1px solid var(--border); border-radius: 0; background: #0a0a08 center / 12px no-repeat; transition: .2s;
        }
        .lg-card input[type="checkbox"]:checked {
            border-color: var(--g); background-color: var(--g);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2315130d' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6 9 17l-5-5'/%3E%3C/svg%3E");
        }
        .lg-card input[type="checkbox"]:focus-visible { box-shadow: 0 0 0 3px rgba(216,180,94,.3); outline: none; }
        .lg-card label.inline-flex { color: #cfcabe; font-size: 13px; cursor: pointer; user-select: none; }

        /* Bouton 3D (login : .lg-submit ; autres pages : bouton submit de Breeze) */
        .lg-submit, .lg-card button[type="submit"]:not(.underline):not(.lg-plain) {
            position: relative; display: inline-flex; align-items: center; justify-content: center; gap: 14px;
            min-height: 52px; padding: 0 28px; border: 0; border-radius: 0; cursor: pointer;
            background: linear-gradient(110deg, #c7983e, #f0d58e); color: #15130d;
            font: inherit; font-size: 12px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
            box-shadow: 0 6px 0 #8a6a25, 0 18px 30px rgba(0,0,0,.5);
            transition: transform .15s, box-shadow .15s, filter .2s;
        }
        .lg-submit { width: 100%; }
        .lg-submit:hover, .lg-card button[type="submit"]:not(.underline):not(.lg-plain):hover { filter: brightness(1.07); transform: translateY(-2px); box-shadow: 0 8px 0 #8a6a25, 0 22px 36px rgba(0,0,0,.55); }
        .lg-submit:active, .lg-card button[type="submit"]:not(.underline):not(.lg-plain):active { transform: translateY(5px); box-shadow: 0 1px 0 #8a6a25, 0 6px 12px rgba(0,0,0,.5); }
        .lg-card button.is-loading { pointer-events: none; filter: brightness(.88); }
        .lg-card button.is-loading::after { content: ""; width: 16px; height: 16px; border: 2px solid rgba(21,19,13,.25); border-top-color: #15130d; border-radius: 50%; animation: lg-rot .7s linear infinite; }
        @keyframes lg-rot { to { transform: rotate(360deg); } }

        /* Bouton texte (ex. « Se déconnecter » sur la page de vérification) */
        .lg-card button.underline { border: 0; background: none; color: var(--gl); font: inherit; font-size: 13px; cursor: pointer; }

        .lg-field { margin-bottom: 20px; }
        .lg-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px; margin: 4px 0 26px; }
        .lg-foot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px; margin-top: 26px; padding-top: 22px; border-top: 1px solid var(--border); color: var(--muted); font-size: 13px; }
        .lg-back { display: inline-flex; margin-top: 22px; color: #bdb8ad; font-size: 12px; text-decoration: none; transition: color .2s; }
        .lg-back:hover { color: var(--gl); }

        /* Petit utilitaire pour les classes Tailwind de Breeze utilisées dans les pages d'authentification */
        .lg-card .mt-1 { margin-top: .25rem; } .lg-card .mt-2 { margin-top: .5rem; }
        .lg-card .mt-3, .lg-card .mt-4 { margin-top: 1rem; } .lg-card .mt-5, .lg-card .mt-6 { margin-top: 1.5rem; } .lg-card .mt-8 { margin-top: 2rem; }
        .lg-card .mb-2 { margin-bottom: .5rem; } .lg-card .mb-3, .lg-card .mb-4 { margin-bottom: 1rem; } .lg-card .mb-6 { margin-bottom: 1.5rem; } .lg-card .mb-8 { margin-bottom: 2rem; }
        .lg-card .block { display: block; } .lg-card .inline-flex { display: inline-flex; } .lg-card .flex { display: flex; }
        .lg-card .w-full { width: 100%; } .lg-card .items-center { align-items: center; }
        .lg-card .justify-end { justify-content: flex-end; } .lg-card .justify-between { justify-content: space-between; }
        .lg-card .gap-2 { gap: .5rem; } .lg-card .gap-4 { gap: 1rem; }
        .lg-card .ms-2 { margin-inline-start: .5rem; } .lg-card .ms-3 { margin-inline-start: .75rem; } .lg-card .ms-4 { margin-inline-start: 1rem; }
        .lg-card .text-sm { font-size: 13px; } .lg-card .font-serif { font-family: 'Playfair Display', serif; }
        .lg-card .space-y-1 > * + * { margin-top: .25rem; }
        .lg-card .sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }

        @keyframes lg-in { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: none; } }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 900px) {
            .lg-wrap { grid-template-columns: 1fr; max-width: 480px; }
            .lg-brand { text-align: center; }
            .lg-logo { margin-bottom: 20px; }
            .lg-lead, .lg-points { display: none; }
            .lg-brand h2 { font-size: 30px; }
        }
        @media (max-width: 760px) { .lg-scene { min-height: calc(100svh - 58px); padding-top: 34px; } }
        @media (max-width: 480px) {
            .lg-card { padding: 32px 22px 26px; }
            .lg-row { flex-direction: column; align-items: flex-start; }
        }
        @media (prefers-reduced-motion: reduce) {
            .lg-floor, .lg-float, .lg-cube, .lg-brand, .lg-card-wrap { animation: none !important; }
        }
    </style>
</head>
<body>

<div class="hb-page" x-data="hbLayout()" data-theme="mixte" :data-theme="theme">

    {{-- SIDEBAR (ordinateur) --}}
    <aside class="hb-sidebar">
        <a class="hb-brand" href="{{ route('home') }}">
            <span class="hb-monogram">{{ $c['monogram'] }}</span>
            <span>{{ $c['name'] }}</span>
        </a>
        <div class="hb-sidebar-nav">@include('partials.hb-nav')</div>
        <div class="hb-sidebar-bottom">
            <a href="{{ route('contact', ['sujet' => 'quote']) }}" class="hb-button hb-button-gold hb-side-quote">Demander un devis</a>
            <span class="hb-mini-label">Choisir le fond</span>
            @include('partials.hb-themes')
            <div class="hb-side-contact">
                @if(!empty($c['contact']['phones'][0]))<p>{{ $c['contact']['phones'][0] }}</p>@endif
                @if(!empty($c['contact']['email']))<p>{{ $c['contact']['email'] }}</p>@endif
            </div>
            <div class="hb-sidebar-note">SMART BUILDING · CASABLANCA</div>
        </div>
    </aside>

    {{-- HEADER MOBILE --}}
    <header class="hb-mobile-header">
        <a href="{{ route('home') }}" class="hb-mobile-brand">{{ $c['name'] }}</a>
        <div class="hb-mobile-controls">
            @include('partials.hb-themes')
            <button type="button" aria-label="Menu" class="hb-icon-button" @click="open = !open">
                <span x-show="!open"><x-icon name="menu" :size="22" /></span>
                <span x-show="open" x-cloak><x-icon name="x" :size="22" /></span>
            </button>
        </div>
    </header>
    <div class="hb-mobile-menu" x-show="open" x-cloak>
        @include('partials.hb-nav')
        <a href="{{ route('contact', ['sujet' => 'quote']) }}" class="hb-button hb-button-gold hb-mobile-quote">Demander un devis</a>
    </div>

    {{-- CONTENU --}}
    <main class="hb-main">
        <div class="lg-scene">
            <div class="lg-bg" @if($bg) style="background-image:url('{{ $bg }}')" @endif></div>
            <div class="lg-glow a"></div><div class="lg-glow b"></div>
            <div class="lg-floor-wrap" aria-hidden="true"><div class="lg-floor"></div></div>
            <div class="lg-cubes" aria-hidden="true">
                @foreach($cubes as $cu)
                    <div class="lg-float" style="left:{{ $cu['x'] }};top:{{ $cu['y'] }};animation-delay:{{ $cu['o'] }}s">
                        <div class="lg-cube" style="--s:{{ $cu['s'] }}px;--dur:{{ $cu['d'] }}s;--delay:{{ $cu['o'] }}s">
                            <i></i><i></i><i></i><i></i><i></i><i></i>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="lg-wrap">
                <section class="lg-brand">
                    <a href="{{ route('home') }}" class="lg-logo">
                        <span class="lg-mono">{{ $c['monogram'] }}</span>
                        <span>{{ $c['name'] }}</span>
                    </a>
                    <p class="lg-kicker">ESPACE PROFESSIONNELS</p>
                    <h2>Accédez à vos <em>tarifs</em><br>et à votre catalogue.</h2>
                    <p class="lg-lead">Consultez le catalogue, préparez vos listes et suivez vos commandes en toute simplicité.</p>
                    <ul class="lg-points">
                        @foreach(['Catalogue produits', 'Ma liste et commandes', 'Suivi de vos demandes'] as $pt)
                            <li><b><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></b>{{ $pt }}</li>
                        @endforeach
                    </ul>
                </section>

                <div class="lg-card-wrap">
                    <div class="lg-card">
                        {{ $slot }}
                    </div>
                    <a href="{{ route('home') }}" class="lg-back">← Retour au site</a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function hbLayout() {
    return {
        theme: 'mixte',
        open: false,
        themes: [
            { id: 'mixte', label: 'Mixte',  swatch: 'linear-gradient(135deg,#ffffff 50%,#080808 50%)' },
            { id: 'white', label: 'Blanc',  swatch: '#fcfbf8' },
            { id: 'ivory', label: 'Ivoire', swatch: '#f1eadb' },
            { id: 'dark',  label: 'Sombre', swatch: '#161616' },
        ],
        init() {
            try {
                const saved = localStorage.getItem('hn-theme-v2');
                if (this.themes.some(t => t.id === saved)) this.theme = saved;
            } catch (e) {}
            document.documentElement.dataset.theme = this.theme;
            this.$watch('theme', v => {
                document.documentElement.dataset.theme = v;
                try { localStorage.setItem('hn-theme-v2', v) } catch (e) {}
            });
        },
        go(id) {
            this.open = false;
            const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            document.getElementById(id)?.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth' });
        },
    };
}
</script>

{{-- Améliorations des formulaires (toutes les pages d'authentification) --}}
<script>
(function () {
    var card = document.querySelector('.lg-card');
    if (!card) return;

    var EYE = '<svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>'
            + '<svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.9 10.9 0 0 1 12 19c-6.4 0-10-7-10-7a18.4 18.4 0 0 1 5.1-5.9M9.9 5.2A10.4 10.4 0 0 1 12 5c6.4 0 10 7 10 7a18.5 18.5 0 0 1-2.2 3.2M1 1l22 22"/></svg>';

    /* Bouton « afficher le mot de passe » + alerte Verr. Maj. sur chaque champ mot de passe */
    card.querySelectorAll('input[type="password"]').forEach(function (inp) {
        var wrap = document.createElement('div');
        wrap.className = 'lg-input-wrap';
        inp.parentNode.insertBefore(wrap, inp);
        wrap.appendChild(inp);
        inp.classList.add('has-eye');

        var eye = document.createElement('button');
        eye.type = 'button'; eye.className = 'lg-eye lg-plain';
        eye.setAttribute('aria-label', 'Afficher le mot de passe');
        eye.setAttribute('aria-pressed', 'false');
        eye.innerHTML = EYE;
        wrap.appendChild(eye);

        var caps = document.createElement('p');
        caps.className = 'lg-caps'; caps.textContent = 'Verr. Maj. activé';
        wrap.parentNode.insertBefore(caps, wrap.nextSibling);

        eye.addEventListener('click', function () {
            var show = inp.type === 'password';
            inp.type = show ? 'text' : 'password';
            eye.setAttribute('aria-pressed', show ? 'true' : 'false');
            eye.setAttribute('aria-label', show ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
            inp.focus();
        });
        function check(e) { if (e.getModifierState) caps.classList.toggle('is-on', e.getModifierState('CapsLock')); }
        inp.addEventListener('keydown', check);
        inp.addEventListener('keyup', check);
        inp.addEventListener('blur', function () { caps.classList.remove('is-on'); });
    });

    /* Bouton d'envoi : état de chargement */
    card.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            if (form.checkValidity && !form.checkValidity()) return;
            var btn = form.querySelector('button[type="submit"]');
            if (btn) btn.classList.add('is-loading');
        });
    });
    window.addEventListener('pageshow', function () {
        card.querySelectorAll('button.is-loading').forEach(function (b) { b.classList.remove('is-loading'); });
    });
})();
</script>
</body>
</html>
