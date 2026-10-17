@props(['title' => null])
@php
    $brand = config('hnbloom.name', config('app.name', 'HN Bloom'));
    $user  = auth()->user();
    $home  = Route::has('client.dashboard') ? route('client.dashboard') : (Route::has('dashboard') ? route('dashboard') : url('/'));

    $cartCount = 0;
    $navCats = collect();
    try {
        if ($user) {
            if (method_exists($user, 'cartItems')) { $cartCount = (int) $user->cartItems()->sum('quantity'); }
            elseif (class_exists(\App\Models\CartItem::class)) { $cartCount = (int) \App\Models\CartItem::where('user_id', $user->id)->sum('quantity'); }
        }
    } catch (\Throwable $e) {}
    try { $navCats = \App\Models\Category::orderBy('name')->get(['id', 'name']); } catch (\Throwable $e) {}
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' — ' : '' }}{{ $brand }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@vite(['resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/hb-client.css') }}">
</head>
<body class="jm-body">

    {{-- Bandeau supérieur --}}
    <div class="jm-top">
        <div class="jm-wrap jm-top-in">
            <span><b>Espace revendeur</b> · Solutions de contrôle d'accès fabriquées au Maroc</span>
            @if(Route::has('contact'))
                <a href="{{ route('contact') }}">Besoin d'aide ? Contactez-nous</a>
            @endif
        </div>
    </div>

    {{-- En-tête --}}
    <header class="jm-head">
        <div class="jm-wrap jm-head-in">
            <button type="button" class="jm-burger" data-jm-drawer-open aria-label="Ouvrir le menu"><x-jm-icon name="menu" :size="24" /></button>

            <a href="{{ $home }}" class="jm-logo" aria-label="{{ $brand }}">
                <span class="jm-logo-mark">{{ mb_strtoupper(mb_substr($brand, 0, 1)) }}</span>
                <span class="jm-logo-text">{{ $brand }}</span>
            </a>

            @if(Route::has('client.catalog.index'))
                <form method="GET" action="{{ route('client.catalog.index') }}" class="jm-search" role="search">
                    <x-jm-icon name="search" :size="19" class="jm-search-ic" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit, une référence…" autocomplete="off">
                    <select name="category" aria-label="Catégorie">
                        <option value="">Toutes les catégories</option>
                        @foreach($navCats as $c)
                            <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit">Rechercher</button>
                </form>
            @endif

            <nav class="jm-actions" aria-label="Compte">
                @if($user)
                    <div class="jm-dd" data-jm-dd>
                        <button type="button" class="jm-act" data-jm-dd-btn aria-haspopup="true">
                            <x-jm-icon name="user" :size="22" />
                            <span class="jm-act-txt">Bonjour, {{ \Illuminate\Support\Str::of($user->name)->before(' ') }}</span>
                            <x-jm-icon name="chevron-down" :size="14" class="jm-act-chev" />
                        </button>
                        <div class="jm-menu">
                            <a href="{{ $home }}"><x-jm-icon name="home" :size="17" /> Tableau de bord</a>
                            @if(Route::has('client.orders.index'))<a href="{{ route('client.orders.index') }}"><x-jm-icon name="package" :size="17" /> Mes commandes</a>@endif
                            @if(Route::has('client.cart.index'))<a href="{{ route('client.cart.index') }}"><x-jm-icon name="list" :size="17" /> Ma liste</a>@endif
                            @if(Route::has('profile.edit'))<a href="{{ route('profile.edit') }}"><x-jm-icon name="user" :size="17" /> Mon profil</a>@endif
                            @if(Route::has('logout'))
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <button type="submit" class="jm-menu-out"><x-jm-icon name="log-out" :size="17" /> Se déconnecter</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @elseif(Route::has('login'))
                    <a href="{{ route('login') }}" class="jm-act"><x-jm-icon name="user" :size="22" /><span class="jm-act-txt">Connexion</span></a>
                @endif

                @if(Route::has('contact'))
                    <a href="{{ route('contact') }}" class="jm-act jm-act-help"><x-jm-icon name="help" :size="22" /><span class="jm-act-txt">Aide</span></a>
                @endif

                @if(Route::has('client.cart.index'))
                    <a href="{{ route('client.cart.index') }}" class="jm-act jm-act-cart" aria-label="Ma liste">
                        <span class="jm-cart-ic"><x-jm-icon name="cart" :size="24" />@if($cartCount > 0)<b class="jm-badge">{{ $cartCount > 99 ? '99+' : $cartCount }}</b>@endif</span>
                        <span class="jm-act-txt">Ma liste</span>
                    </a>
                @endif
            </nav>
        </div>

        {{-- Barre de navigation --}}
        <div class="jm-nav">
            <div class="jm-wrap jm-nav-in">
                <div class="jm-dd" data-jm-dd>
                    <button type="button" class="jm-allcats" data-jm-dd-btn><x-jm-icon name="grid" :size="18" /> Toutes les catégories <x-jm-icon name="chevron-down" :size="14" /></button>
                    <div class="jm-menu jm-menu-left">
                        @forelse($navCats as $c)
                            <a href="{{ Route::has('client.catalog.index') ? route('client.catalog.index', ['category' => $c->id]) : '#' }}">{{ $c->name }}</a>
                        @empty
                            <span class="jm-menu-empty">Aucune catégorie</span>
                        @endforelse
                    </div>
                </div>
                <a href="{{ $home }}" class="{{ request()->routeIs('client.dashboard', 'dashboard') ? 'is-on' : '' }}">Accueil</a>
                @if(Route::has('client.catalog.index'))<a href="{{ route('client.catalog.index') }}" class="{{ request()->routeIs('client.catalog.*') ? 'is-on' : '' }}">Catalogue</a>@endif
                @foreach($navCats->take(5) as $c)
                    <a href="{{ Route::has('client.catalog.index') ? route('client.catalog.index', ['category' => $c->id]) : '#' }}" class="jm-nav-cat">{{ $c->name }}</a>
                @endforeach
                @if(Route::has('client.orders.index'))<a href="{{ route('client.orders.index') }}" class="jm-nav-right {{ request()->routeIs('client.orders.*') ? 'is-on' : '' }}">Mes commandes</a>@endif
            </div>
        </div>
    </header>

    {{-- Menu mobile --}}
    <div class="jm-drawer" data-jm-drawer aria-hidden="true">
        <div class="jm-drawer-bg" data-jm-drawer-close></div>
        <aside class="jm-drawer-panel">
            <div class="jm-drawer-head">
                <span class="jm-logo-text">{{ $brand }}</span>
                <button type="button" data-jm-drawer-close aria-label="Fermer"><x-jm-icon name="x" :size="22" /></button>
            </div>
            <a href="{{ $home }}"><x-jm-icon name="home" :size="18" /> Accueil</a>
            @if(Route::has('client.catalog.index'))<a href="{{ route('client.catalog.index') }}"><x-jm-icon name="grid" :size="18" /> Catalogue</a>@endif
            @if(Route::has('client.cart.index'))<a href="{{ route('client.cart.index') }}"><x-jm-icon name="cart" :size="18" /> Ma liste @if($cartCount)<b class="jm-badge jm-badge-inline">{{ $cartCount }}</b>@endif</a>@endif
            @if(Route::has('client.orders.index'))<a href="{{ route('client.orders.index') }}"><x-jm-icon name="package" :size="18" /> Mes commandes</a>@endif
            @if(Route::has('contact'))<a href="{{ route('contact') }}"><x-jm-icon name="headset" :size="18" /> Support technique</a>@endif
            @if($navCats->count())
                <p class="jm-drawer-label">Catégories</p>
                @foreach($navCats as $c)
                    <a href="{{ Route::has('client.catalog.index') ? route('client.catalog.index', ['category' => $c->id]) : '#' }}" class="jm-drawer-cat">{{ $c->name }}</a>
                @endforeach
            @endif
            @if($user && Route::has('logout'))
                <form method="POST" action="{{ route('logout') }}" class="jm-drawer-out">@csrf
                    <button type="submit"><x-jm-icon name="log-out" :size="18" /> Se déconnecter</button>
                </form>
            @endif
        </aside>
    </div>

    {{-- Contenu --}}
    <main class="jm-main">
        <div class="jm-wrap">
            @if(session('success') || session('status'))
                <div class="jm-flash jm-flash-ok" data-jm-flash><x-jm-icon name="check" :size="18" /> {{ session('success') ?? session('status') }}</div>
            @endif
            @if(session('error'))
                <div class="jm-flash jm-flash-bad" data-jm-flash><x-jm-icon name="x" :size="18" /> {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="jm-flash jm-flash-bad"><x-jm-icon name="x" :size="18" /> {{ $errors->first() }}</div>
            @endif

            {{ $slot }}
        </div>
    </main>

    {{-- Pied de page --}}
    <footer class="jm-foot">
        <div class="jm-wrap jm-foot-grid">
            <div>
                <a href="{{ $home }}" class="jm-logo jm-logo-light"><span class="jm-logo-mark">{{ mb_strtoupper(mb_substr($brand, 0, 1)) }}</span><span class="jm-logo-text">{{ $brand }}</span></a>
                <p class="jm-foot-text">Solutions professionnelles de contrôle d'accès, conçues et fabriquées au Maroc.</p>
            </div>
            <div>
                <h4>Mon espace</h4>
                <a href="{{ $home }}">Tableau de bord</a>
                @if(Route::has('client.catalog.index'))<a href="{{ route('client.catalog.index') }}">Catalogue</a>@endif
                @if(Route::has('client.cart.index'))<a href="{{ route('client.cart.index') }}">Ma liste</a>@endif
                @if(Route::has('client.orders.index'))<a href="{{ route('client.orders.index') }}">Mes commandes</a>@endif
            </div>
            <div>
                <h4>Assistance</h4>
                @if(Route::has('contact'))<a href="{{ route('contact') }}">Support technique</a>@endif
                @if(Route::has('profile.edit'))<a href="{{ route('profile.edit') }}">Mon profil</a>@endif
            </div>
            <div>
                <h4>Nos engagements</h4>
                <span>Fabriqué au Maroc</span>
                <span>Normes internationales</span>
                <span>Équipe d'ingénieurs</span>
            </div>
        </div>
        <div class="jm-foot-bar"><div class="jm-wrap">© {{ date('Y') }} {{ $brand }} — Tous droits réservés.</div></div>
    </footer>

    <button type="button" class="jm-totop" data-jm-totop aria-label="Haut de page"><x-jm-icon name="arrow-up" :size="20" /></button>
    <script src="{{ asset('js/hb-client.js') }}" defer></script>
</body>
</html>
