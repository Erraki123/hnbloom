@props(['title' => null, 'description' => null])
@php $c = config('hnbloom'); @endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' — ' : '' }}{{ $c['name'] }}</title>
    <meta name="description" content="{{ $description ?: $c['tagline'] }}">
    <script>document.documentElement.classList.add('hb-js');</script>
    <link rel="stylesheet" href="{{ asset('css/Site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/hnbloom-laravel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/hb-pages.css') }}">
    <style>
        /* Par défaut tout est VISIBLE. On ne cache que si le JavaScript fonctionne (classe hb-js). */
        .reveal { opacity: 1; transform: none; }
        html.hb-js .reveal:not(.is-in) { opacity: 0; transform: translateY(22px); }
        html.hb-js .reveal { transition: opacity .7s cubic-bezier(.2,.7,.2,1) var(--d, 0s), transform .7s cubic-bezier(.2,.7,.2,1) var(--d, 0s); }
        @media (prefers-reduced-motion: reduce) {
            html.hb-js .reveal:not(.is-in) { opacity: 1; transform: none; }
        }
    </style>
    @vite(['resources/js/app.js'])
    @stack('head')
</head>
<body>

{{-- Thème fixe "mixte" : le choix du fond a été supprimé --}}
<div class="hb-page" x-data="hbLayout()" data-theme="mixte">

    {{-- SIDEBAR (ordinateur) --}}
    <aside class="hb-sidebar">
        <a class="hb-brand" href="{{ route('home') }}">
            <span class="hb-monogram">{{ $c['monogram'] }}</span>
            <span>{{ $c['name'] }}</span>
        </a>
        <div class="hb-sidebar-nav">@include('partials.hb-nav')</div>
        <div class="hb-sidebar-bottom">
            <a href="{{ route('contact', ['sujet' => 'quote']) }}" class="hb-button hb-button-gold hb-side-quote">Demander un devis</a>
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

    <main class="hb-main">
        {{ $slot }}
        @include('partials.hb-footer')
    </main>
</div>

{{-- Apparition au scroll : indépendant d'Alpine, avec filet de sécurité --}}
<script>
(function () {
    function showAll() {
        document.querySelectorAll('.reveal:not(.is-in)').forEach(function (el) { el.classList.add('is-in'); });
    }
    function revealVisible() {
        document.querySelectorAll('.reveal:not(.is-in)').forEach(function (el) {
            var r = el.getBoundingClientRect();
            if (r.top < window.innerHeight - 40) el.classList.add('is-in');
        });
    }
    window.addEventListener('scroll', revealVisible, { passive: true });
    window.addEventListener('resize', revealVisible);
    window.addEventListener('load', revealVisible);
    document.addEventListener('DOMContentLoaded', revealVisible);
    setTimeout(revealVisible, 300);
    setTimeout(showAll, 2500);   // filet de sécurité : plus rien ne reste caché
})();
</script>

<script>
function hbLayout() {
    return {
        open: false,
        init() {
            // Thème fixe : on efface l'ancien choix mémorisé dans le navigateur
            try { localStorage.removeItem('hn-theme-v2'); } catch (e) {}
            document.documentElement.dataset.theme = 'mixte';
        },
        go(id) {
            this.open = false;
            const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            document.getElementById(id)?.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth' });
        },
    };
}
</script>
</body>
</html>
