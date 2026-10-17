@props(['title' => null])
@php
    use App\Models\ContactMessage;
    use App\Models\Order;
    use App\Models\User;

    $c = config('hnbloom');

    // Titre automatique selon la page (si aucun titre n'est donné)
    $routeTitles = [
        'admin.dashboard'  => 'Tableau de bord',
        'admin.products.*' => 'Produits',
        'admin.categories.*' => 'Catégories',
        'admin.orders.*'   => 'Commandes',
        'admin.clients.*'  => 'Revendeurs',
        'admin.users.*'    => 'Revendeurs',
        'admin.messages.*' => 'Messages',
        'admin.projects.*' => 'Projets',
    ];
    $pageTitle = $title;
    if (!$pageTitle) {
        foreach ($routeTitles as $pattern => $label) {
            if (request()->routeIs($pattern)) { $pageTitle = $label; break; }
        }
    }
    $pageTitle = $pageTitle ?: 'Administration';

    // Pastilles de notification
    $badges = [
        'admin.clients.index'  => User::where('role', 'client')->where('is_approved', false)->count(),
        'admin.orders.index'   => Order::where('status', 'pending')->count(),
        'admin.messages.index' => ContactMessage::where('status', 'new')->count(),
    ];

    // Menu : [groupe => [[libellé, route, motif d'activation, icône], ...]]
    $menu = [
        'Principal' => [
            ['Tableau de bord', 'admin.dashboard', 'admin.dashboard', 'layout-dashboard'],
        ],
        'Catalogue' => [
            ['Produits',   'admin.products.index',   'admin.products.*',   'package'],
            ['Catégories', 'admin.categories.index', 'admin.categories.*', 'tags'],
        ],
        'Commerce' => [
            ['Commandes',  'admin.orders.index',  'admin.orders.*',  'shopping-cart'],
            ['Revendeurs', 'admin.clients.index', 'admin.clients.*', 'users'],
        ],
        'Communication' => [
            ['Messages', 'admin.messages.index', 'admin.messages.*', 'mail'],
        ],
    ];
    $alerts = array_sum($badges);
@endphp
<!DOCTYPE html>
<html lang="fr" data-ad-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $pageTitle }} — Admin {{ $c['name'] }}</title>
    <script>
        /* Thème + menu réduit appliqués avant l'affichage (évite le clignotement) */
        try {
            var t = localStorage.getItem('hn-admin-theme'); if (t) document.documentElement.setAttribute('data-ad-theme', t);
            if (localStorage.getItem('hn-admin-collapsed') === '1') document.documentElement.classList.add('ad-collapsed');
        } catch (e) {}
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    {{-- Tailwind reste chargé : les anciennes pages admin utilisent ses classes utilitaires (mise en page). --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@vite(['resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/hb-admin.css') }}">
    <script src="{{ asset('js/hb-admin.js') }}" defer></script>
</head>
<body class="ad-app">

<div class="ad-shell" id="ad-shell">

    {{-- ================= SIDEBAR ================= --}}
    <aside class="ad-sidebar" id="ad-sidebar" aria-label="Navigation administration">
        <a href="{{ route('admin.dashboard') }}" class="ad-brand">
            <span class="ad-mono">{{ $c['monogram'] }}</span>
            <span class="ad-brand-text"><b>{{ $c['name'] }}</b><small>Administration</small></span>
        </a>

        <nav class="ad-nav">
            @foreach($menu as $group => $items)
                <p class="ad-group">{{ $group }}</p>
                @foreach($items as [$label, $route, $pattern, $icon])
                    @if(Route::has($route))
                        <a href="{{ route($route) }}" class="ad-nav-item {{ request()->routeIs($pattern) ? 'is-active' : '' }}" title="{{ $label }}"
                           @if(request()->routeIs($pattern)) aria-current="page" @endif>
                            <x-ad-icon :name="$icon" :size="19" />
                            <span class="ad-nav-label">{{ $label }}</span>
                            @if(!empty($badges[$route]))<span class="ad-badge">{{ $badges[$route] }}</span>@endif
                        </a>
                    @endif
                @endforeach
            @endforeach
        </nav>

        <div class="ad-side-bottom">
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="ad-nav-item" title="Voir le site">
                <x-ad-icon name="external-link" :size="19" /><span class="ad-nav-label">Voir le site</span>
            </a>
            <button type="button" class="ad-nav-item ad-collapse-btn" id="ad-collapse" title="Réduire le menu">
                <x-ad-icon name="panel-left" :size="19" /><span class="ad-nav-label">Réduire le menu</span>
            </button>
        </div>
    </aside>
    <div class="ad-overlay" id="ad-overlay"></div>

    {{-- ================= CORPS ================= --}}
    <div class="ad-body">

        <header class="ad-topbar">
            <button type="button" class="ad-icon-btn ad-burger" id="ad-menu-btn" aria-label="Ouvrir le menu"><x-ad-icon name="menu" :size="22" /></button>

            <div class="ad-crumb">
                <span>Administration</span>
                <x-ad-icon name="chevron-down" :size="14" class="ad-sep" />
                <b>{{ $pageTitle }}</b>
            </div>

            <label class="ad-search" title="Filtrer les tableaux de la page (raccourci : /)">
                <x-ad-icon name="search" :size="16" />
                <input type="search" id="ad-filter" placeholder="Filtrer la page…" autocomplete="off">
                <kbd>/</kbd>
            </label>

            <div class="ad-top-actions">
                <button type="button" class="ad-icon-btn" id="ad-theme" aria-label="Changer le thème" title="Clair / sombre">
                    <x-ad-icon name="sun" :size="19" class="ico-sun" />
                    <x-ad-icon name="moon" :size="19" class="ico-moon" />
                </button>

                <a href="{{ route('admin.messages.index') }}" class="ad-icon-btn ad-bell" aria-label="Notifications" title="{{ $alerts }} élément(s) à traiter">
                    <x-ad-icon name="bell" :size="19" />
                    @if($alerts > 0)<span class="ad-dot">{{ $alerts > 99 ? '99+' : $alerts }}</span>@endif
                </a>

                <div class="ad-user" id="ad-user">
                    <button type="button" class="ad-user-btn" id="ad-user-btn" aria-haspopup="true" aria-expanded="false">
                        <span class="ad-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="ad-user-name">{{ \Illuminate\Support\Str::of(auth()->user()->name)->before(' ') }}</span>
                        <x-ad-icon name="chevron-down" :size="14" />
                    </button>
                    <div class="ad-menu" id="ad-user-menu" role="menu">
                        <div class="ad-menu-head">
                            <strong>{{ auth()->user()->name }}</strong>
                            <small>{{ auth()->user()->email }}</small>
                        </div>
                        <a href="{{ route('home') }}" target="_blank" rel="noopener" role="menuitem"><x-ad-icon name="external-link" :size="16" /> Voir le site</a>
                        @if(Route::has('profile.edit'))
                            <a href="{{ route('profile.edit') }}" role="menuitem"><x-ad-icon name="user" :size="16" /> Mon profil</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem" class="ad-menu-danger"><x-ad-icon name="log-out" :size="16" /> Déconnexion</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="ad-main" id="ad-main">
            @if($errors->any())
                <div class="ad-alert ad-alert-error">
                    <x-ad-icon name="circle-alert" :size="18" />
                    <div>
                        <strong>Veuillez corriger les erreurs :</strong>
                        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                </div>
            @endif

            {{ $slot }}
        </main>

        <footer class="ad-foot">
            <span>© {{ date('Y') }} {{ $c['name'] }} · Administration</span>
            <span>Connecté en tant que {{ auth()->user()->email }}</span>
        </footer>
    </div>
</div>

{{-- Messages flash -> notifications --}}
<div class="ad-toasts" id="ad-toasts" aria-live="polite">
    @if(session('success'))
        <div class="ad-toast ad-toast-ok"><x-ad-icon name="check" :size="18" /><span>{{ session('success') }}</span><button type="button" class="ad-toast-x" aria-label="Fermer">×</button></div>
    @endif
    @if(session('error'))
        <div class="ad-toast ad-toast-bad"><x-ad-icon name="circle-alert" :size="18" /><span>{{ session('error') }}</span><button type="button" class="ad-toast-x" aria-label="Fermer">×</button></div>
    @endif
    @if(session('status'))
        <div class="ad-toast ad-toast-ok"><x-ad-icon name="check" :size="18" /><span>{{ session('status') }}</span><button type="button" class="ad-toast-x" aria-label="Fermer">×</button></div>
    @endif
</div>

{{-- Fenêtre de confirmation (suppressions…) --}}
<div class="ad-modal" id="ad-modal" hidden>
    <div class="ad-modal-box" role="dialog" aria-modal="true" aria-labelledby="ad-modal-title">
        <h3 id="ad-modal-title">Confirmation</h3>
        <p id="ad-modal-text">Confirmer cette action ?</p>
        <div class="ad-modal-actions">
            <button type="button" class="ad-btn" data-modal-cancel>Annuler</button>
            <button type="button" class="ad-btn ad-btn-danger" data-modal-ok>Confirmer</button>
        </div>
    </div>
</div>

</body>
</html>
