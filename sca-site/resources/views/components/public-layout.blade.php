@props(['title' => null])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' — ' : '' }}{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@vite(['resources/js/app.js'])
</head>
<body class="bg-white font-sans text-ink antialiased">
<header x-data="{ open: false }" class="sticky top-0 z-50 border-b border-black/5 bg-white/95 backdrop-blur">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-6">
        <a href="{{ route('home') }}" class="font-serif text-2xl tracking-wide">
            {{ config('app.name') }}<span class="text-gold">.</span>
        </a>

        <nav class="hidden items-center gap-10 text-xs uppercase tracking-[0.2em] md:flex">
            <a href="{{ route('home') }}" class="transition hover:text-gold {{ request()->routeIs('home') ? 'text-gold' : '' }}">Accueil</a>
            <a href="{{ route('catalog.index') }}" class="transition hover:text-gold {{ request()->routeIs('catalog.*') ? 'text-gold' : '' }}">Catalogue</a>
            <a href="{{ route('contact') }}" class="transition hover:text-gold {{ request()->routeIs('contact') ? 'text-gold' : '' }}">Contact</a>
        </nav>

        <div class="hidden items-center gap-4 md:flex">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-dark !py-2.5">Mon espace</a>
            @else
                <a href="{{ route('login') }}" class="text-xs uppercase tracking-[0.2em] transition hover:text-gold">Connexion</a>
                <a href="{{ route('register') }}" class="btn-dark !py-2.5">Espace revendeur</a>
            @endauth
        </div>

        <button @click="open = !open" class="md:hidden" aria-label="Menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path x-show="!open" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                <path x-show="open" x-cloak stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/>
            </svg>
        </button>
    </div>

    <div x-show="open" x-cloak class="border-t border-black/5 bg-white px-6 py-6 md:hidden">
        <div class="flex flex-col gap-5 text-sm uppercase tracking-[0.2em]">
            <a href="{{ route('home') }}">Accueil</a>
            <a href="{{ route('catalog.index') }}">Catalogue</a>
            <a href="{{ route('contact') }}">Contact</a>
            @auth
                <a href="{{ route('dashboard') }}" class="text-gold-dark">Mon espace</a>
            @else
                <a href="{{ route('login') }}">Connexion</a>
                <a href="{{ route('register') }}" class="text-gold-dark">Espace revendeur</a>
            @endauth
        </div>
    </div>
</header>

<main>{{ $slot }}</main>

<footer class="bg-ink text-white/70">
    <div class="mx-auto grid max-w-7xl gap-12 px-6 py-16 md:grid-cols-3">
        <div>
            <p class="font-serif text-2xl text-white">{{ config('app.name') }}<span class="text-gold">.</span></p>
            <p class="mt-4 text-sm leading-relaxed">Votre partenaire pour les professionnels et revendeurs.</p>
        </div>
        <div>
            <p class="eyebrow mb-4">Navigation</p>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('catalog.index') }}" class="hover:text-gold">Catalogue</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-gold">Contact</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-gold">Espace revendeur</a></li>
            </ul>
        </div>
        <div>
            <p class="eyebrow mb-4">Contact</p>
            <ul class="space-y-2 text-sm">
                <li>Votre adresse, Casablanca</li>
                <li>+212 6 00 00 00 00</li>
                <li>contact@votre-site.com</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10 py-6 text-center text-xs tracking-wider">
        © {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
    </div>
</footer>
</body>
</html>
