@php
    $user     = auth()->user();
    $initial  = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1));
    $isAdmin  = $user->isAdmin();
    $approved = $isAdmin || $user->is_approved;
@endphp

<x-hb-layout title="Mon profil" description="Gérez votre compte HN BLOOM.">
<div class="pf-page">

    {{-- HERO --}}
    <section class="pf-hero">
        <div class="pf-hero-inner">
            <div class="pf-avatar">{{ $initial }}</div>

            <div class="pf-hero-text">
                <p class="pf-eyebrow">MON COMPTE</p>
                <h1>{{ $user->name }}</h1>
                <p class="pf-mail">{{ $user->email }}</p>
                <div class="pf-badges">
                    <span class="pf-badge">{{ $isAdmin ? 'ADMINISTRATEUR' : 'CLIENT PROFESSIONNEL' }}</span>
                    <span class="pf-badge {{ $approved ? 'is-ok' : 'is-wait' }}">
                        <i></i>{{ $approved ? 'COMPTE ACTIVÉ' : 'EN ATTENTE DE VALIDATION' }}
                    </span>
                </div>
            </div>

            <div class="pf-hero-actions">
                <a href="{{ route('dashboard') }}" class="pf-btn">
                    Mon espace <x-icon name="arrow-right" :size="16" />
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="pf-btn is-outline pf-plain">Se déconnecter</button>
                </form>
            </div>
        </div>
    </section>

    {{-- CONTENU --}}
    <section class="pf-section">
        <div class="pf-layout">

            <aside class="pf-nav">
                <p class="pf-eyebrow">PARAMÈTRES</p>
                <a href="#informations"><span>01</span> Informations</a>
                <a href="#mot-de-passe"><span>02</span> Mot de passe</a>
                <a href="#suppression" class="is-danger"><span>03</span> Zone sensible</a>
            </aside>

            <div class="pf-stack">

                <article class="pf-card" id="informations">
                    <header class="pf-card-head">
                        <span class="pf-card-icon"><x-icon name="user" :size="20" :stroke="1.5" /></span>
                        <div>
                            <p class="pf-eyebrow">01 · PROFIL</p>
                            <h2>Informations personnelles</h2>
                        </div>
                    </header>
                    <div class="pf-card-body">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </article>

                <article class="pf-card" id="mot-de-passe">
                    <header class="pf-card-head">
                        <span class="pf-card-icon"><x-icon name="shield-check" :size="20" :stroke="1.5" /></span>
                        <div>
                            <p class="pf-eyebrow">02 · SÉCURITÉ</p>
                            <h2>Mot de passe</h2>
                        </div>
                    </header>
                    <div class="pf-card-body">
                        @include('profile.partials.update-password-form')
                    </div>
                </article>

                <article class="pf-card is-danger" id="suppression">
                    <header class="pf-card-head">
                        <span class="pf-card-icon"><x-icon name="flame" :size="20" :stroke="1.5" /></span>
                        <div>
                            <p class="pf-eyebrow">03 · ZONE SENSIBLE</p>
                            <h2>Supprimer le compte</h2>
                        </div>
                    </header>
                    <div class="pf-card-body">
                        @include('profile.partials.delete-user-form')
                    </div>
                </article>

            </div>
        </div>
    </section>
</div>

<style>
    .pf-page {
        --g:#b8902f; --gl:#8a6a25; --gold-bright:#d8b45e;
        --ink:#1a1812; --muted:#6b665a; --border:rgba(184,144,47,.28); --line:#ece6d8;
        --panel:#ffffff; --danger:#c2453e;
        background:#fbfaf7; color:var(--ink); min-height:100vh;
    }
    .pf-eyebrow { margin:0 0 10px; color:var(--g); font-size:10px; font-weight:700; letter-spacing:2.6px; }

    /* HERO */
    .pf-hero { position:relative; padding:clamp(60px,8vw,100px) 6% 50px; border-bottom:1px solid var(--line);
        background:radial-gradient(ellipse at 88% 10%, rgba(216,180,94,.2), transparent 50%), #ffffff; }
    .pf-hero::after { content:""; position:absolute; left:0; right:0; bottom:-1px; height:2px;
        background:linear-gradient(90deg, transparent, var(--gold-bright), transparent); }
    .pf-hero-inner { display:flex; flex-wrap:wrap; align-items:center; gap:34px; max-width:1250px; margin:0 auto; }
    .pf-avatar { display:grid; place-items:center; flex:0 0 96px; width:96px; height:96px;
        background:#15130d; color:var(--gold-bright); font-family:Georgia,serif; font-size:48px;
        box-shadow:0 0 0 6px rgba(216,180,94,.18), 0 18px 34px rgba(60,45,10,.22); }
    .pf-hero-text { flex:1; min-width:240px; }
    .pf-hero h1 { margin:0; font-family:Georgia,"Times New Roman",serif; font-size:clamp(32px,4.5vw,54px); font-weight:400; line-height:1.1; letter-spacing:-1px; color:var(--ink); }
    .pf-mail { margin:10px 0 0; color:var(--muted); font-size:13px; }
    .pf-badges { display:flex; flex-wrap:wrap; gap:10px; margin-top:20px; }
    .pf-badge { display:inline-flex; align-items:center; gap:8px; padding:7px 12px; border:1px solid var(--border); background:#fff; color:var(--gl); font-size:9px; font-weight:700; letter-spacing:1.6px; }
    .pf-badge i { width:6px; height:6px; border-radius:50%; background:var(--g); }
    .pf-badge.is-ok { color:#2f7a4d; border-color:rgba(47,122,77,.35); } .pf-badge.is-ok i { background:#2f9e5f; }
    .pf-badge.is-wait { color:#a8741a; } .pf-badge.is-wait i { background:#d9962b; }
    .pf-hero-actions { display:flex; flex-direction:column; gap:12px; min-width:210px; }

    /* BOUTONS */
    .pf-btn { display:inline-flex; align-items:center; justify-content:space-between; gap:18px; width:100%; min-height:48px; padding:0 20px;
        border:1px solid #15130d; background:#15130d; color:#f0d69a; font:inherit; font-size:12px; font-weight:700; text-decoration:none; cursor:pointer; transition:background .25s,color .25s,transform .25s; }
    .pf-btn:hover { transform:translateY(-2px); background:transparent; color:#15130d; }
    .pf-btn.is-outline { background:transparent; color:#15130d; justify-content:center; border-color:var(--border); }
    .pf-btn.is-outline:hover { background:var(--gold-bright); border-color:var(--gold-bright); color:#15130d; }

    /* LAYOUT */
    .pf-section { padding:60px 6% 90px; }
    .pf-layout { display:grid; grid-template-columns:230px minmax(0,1fr); gap:44px; align-items:start; max-width:1250px; margin:0 auto; }
    .pf-nav { position:sticky; top:90px; display:grid; border-top:1px solid var(--line); padding-top:22px; }
    .pf-nav .pf-eyebrow { margin-bottom:14px; }
    .pf-nav a { display:flex; align-items:center; gap:16px; padding:16px 0; border-bottom:1px solid var(--line); color:var(--ink); font-family:Georgia,serif; font-size:17px; text-decoration:none; transition:color .2s,padding .25s; }
    .pf-nav a span { color:var(--g); font-size:12px; letter-spacing:1.4px; }
    .pf-nav a:hover { color:var(--g); padding-left:8px; }
    .pf-nav a.is-danger:hover { color:var(--danger); }
    .pf-stack { display:grid; gap:26px; min-width:0; }

    /* CARTES (sans transform/backdrop-filter : la modale reste plein écran) */
    .pf-card { scroll-margin-top:90px; border:1px solid var(--line); background:var(--panel);
        box-shadow:0 18px 40px rgba(60,45,10,.07), 0 2px 6px rgba(60,45,10,.04); }
    .pf-card.is-danger { border-color:rgba(194,69,62,.3); }
    .pf-card-head { display:flex; align-items:center; gap:18px; padding:26px 34px; border-bottom:1px solid var(--line);
        background:linear-gradient(160deg, rgba(216,180,94,.1), transparent 50%); }
    .pf-card.is-danger .pf-card-head { border-bottom-color:rgba(194,69,62,.2); background:linear-gradient(160deg, rgba(194,69,62,.06), transparent 50%); }
    .pf-card-icon { display:grid; flex:0 0 46px; width:46px; height:46px; place-items:center; border:1px solid var(--border); border-radius:50%; color:var(--g); background:#fff; }
    .pf-card.is-danger .pf-card-icon { color:var(--danger); border-color:rgba(194,69,62,.4); }
    .pf-card-head .pf-eyebrow { margin-bottom:6px; }
    .pf-card-head h2 { margin:0; font-family:Georgia,serif; font-size:clamp(22px,2.4vw,28px); font-weight:400; color:var(--ink); }
    .pf-card-body { padding:34px; }
    .pf-card-body > section { max-width:560px; }
    .pf-card-body header { margin-bottom:6px; }
    .pf-card-body header h2 { margin:0; font-family:Georgia,serif; font-size:19px; font-weight:400; color:var(--ink); }
    .pf-card-body header p { margin:8px 0 0; color:var(--muted); font-size:13px; line-height:1.8; }
    .pf-card-body form > * + * { margin-top:22px; }

    /* ===== Composants Breeze (partials inchangés) ===== */
    .pf-page .text-gray-900, .pf-page .text-gray-800 { color:var(--ink); }
    .pf-page .text-gray-600, .pf-page .text-gray-700, .pf-page .text-gray-500 { color:var(--muted); }
    .pf-page .text-green-600 { color:#2f7a4d; }
    .pf-page .text-red-600 { color:var(--danger); }
    .pf-page .text-lg { font-size:19px; }
    .pf-page .text-sm { font-size:13px; line-height:1.8; }

    .pf-page label { display:block; margin-bottom:8px; color:var(--muted); font-size:10px; font-weight:600; letter-spacing:.16em; text-transform:uppercase; }
    .pf-page input:not([type=checkbox]):not([type=radio]):not([type=hidden]):not([type=submit]),
    .pf-page select, .pf-page textarea {
        display:block; width:100%; padding:15px 16px; border:1px solid #ddd5c1; border-radius:0;
        background:#fdfcf9; color:var(--ink); font:inherit; font-size:15px;
        box-shadow:inset 0 2px 5px rgba(60,45,10,.05); transition:border-color .25s,box-shadow .25s,background .25s; }
    .pf-page input:focus, .pf-page select:focus, .pf-page textarea:focus {
        outline:none; border-color:var(--g); background:#fff; box-shadow:0 0 0 3px rgba(216,180,94,.25); }
    .pf-page input:-webkit-autofill { -webkit-text-fill-color:var(--ink); -webkit-box-shadow:0 0 0 40px #fdfcf9 inset; }
    .pf-page ul.text-red-600, .pf-page ul.text-sm { margin:7px 0 0; padding:0; list-style:none; font-size:12px; }
    .pf-page .mt-1, .pf-page .mt-2 { margin-top:.5rem; } .pf-page .mt-4 { margin-top:1.4rem; } .pf-page .mt-6 { margin-top:1.8rem; }
    .pf-page .w-full, .pf-page .block { width:100%; }
    .pf-page .flex { display:flex; } .pf-page .items-center { align-items:center; } .pf-page .gap-4 { gap:1rem; }
    .pf-page .justify-end { justify-content:flex-end; }
    .pf-page .ms-3 { margin-inline-start:.75rem; }
    .pf-page .card-link, .pf-page a:not(.pf-btn) { color:var(--gl); }
    .pf-page .underline { text-decoration:none; border-bottom:1px solid rgba(138,106,37,.4); }

    /* Bouton principal : or en relief */
    .pf-page button.bg-gray-800, .pf-page button[type=submit]:not(.pf-plain):not(.bg-red-600) {
        display:inline-flex; align-items:center; justify-content:center; min-height:50px; padding:0 28px; border:0; border-radius:0; cursor:pointer;
        background:linear-gradient(110deg,#c7983e,#f0d58e); color:#15130d;
        font:inherit; font-size:12px; font-weight:600; letter-spacing:.08em; text-transform:uppercase;
        box-shadow:0 5px 0 #8a6a25, 0 14px 24px rgba(60,45,10,.2); transition:transform .15s,box-shadow .15s,filter .2s; }
    .pf-page button.bg-gray-800:hover { filter:brightness(1.06); transform:translateY(-2px); box-shadow:0 7px 0 #8a6a25, 0 18px 30px rgba(60,45,10,.25); }
    .pf-page button.bg-gray-800:active { transform:translateY(4px); box-shadow:0 1px 0 #8a6a25, 0 5px 10px rgba(60,45,10,.2); }
    /* Danger */
    .pf-page button.bg-red-600 { display:inline-flex; align-items:center; justify-content:center; min-height:50px; padding:0 26px; border:0; border-radius:0; cursor:pointer;
        background:#c2453e; color:#fff; font:inherit; font-size:12px; font-weight:600; letter-spacing:.08em; text-transform:uppercase;
        box-shadow:0 5px 0 #7d2b26, 0 14px 24px rgba(60,20,15,.2); transition:transform .15s,filter .2s; }
    .pf-page button.bg-red-600:hover { filter:brightness(1.08); transform:translateY(-2px); }
    /* Secondaire (Annuler) */
    .pf-page button.bg-white { display:inline-flex; align-items:center; justify-content:center; min-height:50px; padding:0 24px; border:1px solid #ddd5c1; border-radius:0; cursor:pointer;
        background:#fff; color:var(--ink); font:inherit; font-size:12px; font-weight:600; letter-spacing:.08em; text-transform:uppercase; transition:.25s; }
    .pf-page button.bg-white:hover { background:var(--gold-bright); border-color:var(--gold-bright); }

    /* Fenêtre de confirmation (x-modal) */
    .pf-page .bg-gray-500 { background:#1a1812; }
    .pf-page div.bg-white { background:#fff; border:1px solid var(--line); border-radius:0; box-shadow:0 40px 80px rgba(20,15,5,.35); }
    .pf-page div.bg-white form { padding:34px; }
    .pf-page div.bg-white h2 { font-family:Georgia,serif; font-size:24px; font-weight:400; color:var(--ink); }

    /* RESPONSIVE */
    @media (max-width:900px){
        .pf-layout { grid-template-columns:1fr; gap:30px; }
        .pf-nav { position:static; grid-auto-flow:column; grid-auto-columns:1fr; border:0; padding:0; gap:8px; }
        .pf-nav .pf-eyebrow { display:none; }
        .pf-nav a { flex-direction:column; align-items:flex-start; gap:6px; padding:14px; border:1px solid var(--line); background:#fff; font-size:14px; }
        .pf-nav a:hover { padding-left:14px; }
        .pf-hero-actions { flex-direction:row; width:100%; min-width:0; }
    }
    @media (max-width:620px){
        .pf-avatar { flex-basis:72px; width:72px; height:72px; font-size:34px; }
        .pf-card-head { padding:20px; } .pf-card-body { padding:22px 20px; }
        .pf-hero-actions { flex-direction:column; }
        .pf-nav { grid-auto-flow:row; }
    }
    @media (prefers-reduced-motion:reduce){
        .pf-btn, .pf-nav a, .pf-page button { transition:none !important; }
    }
</style>
</x-hb-layout>
