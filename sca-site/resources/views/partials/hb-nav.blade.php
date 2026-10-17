@php
    $items = [
        ['route' => 'home',            'match' => 'home',          'label' => 'Accueil',         'icon' => 'house'],
        ['route' => 'about',           'match' => 'about',         'label' => 'Qui sommes-nous', 'icon' => 'users'],
        ['route' => 'solutions.index', 'match' => 'solutions.*',   'label' => 'Nos solutions',   'icon' => 'shield-check'],
        ['route' => 'domotique',       'match' => 'domotique',     'label' => 'Domotique',       'icon' => 'cpu'],
        ['route' => 'projects',        'match' => 'projects',      'label' => 'Nos projets',     'icon' => 'folder-open'],
        ['route' => 'contact',         'match' => 'contact',       'label' => 'Contact',         'icon' => 'phone'],
    ];
@endphp
<nav aria-label="Navigation principale" class="hb-nav">
    @foreach($items as $it)
        <a href="{{ route($it['route']) }}" class="hb-nav-item {{ request()->routeIs($it['match']) ? 'is-active' : '' }}"
           @if(request()->routeIs($it['match'])) aria-current="page" @endif>
            <x-icon :name="$it['icon']" :size="17" :stroke="1.5" /><span>{{ $it['label'] }}</span>
        </a>
    @endforeach

    {{-- ESPACE REVENDEUR --}}
    <a href="{{ route('reseller') }}" class="hb-nav-item hb-nav-reseller {{ request()->routeIs('reseller') ? 'is-active' : '' }}">
        <x-icon name="lock-keyhole" :size="17" :stroke="1.5" /><span>Espace revendeur</span>
    </a>
    @auth
        <a href="{{ route('dashboard') }}" class="hb-nav-item"><x-icon name="log-in" :size="17" :stroke="1.5" /><span>Mon espace</span></a>
    @endauth
</nav>
