<x-client-layout title="Catalogue">
@php
    $activeCat = $categories->firstWhere('id', (int) request('category'));
    $total = method_exists($products, 'total') ? $products->total() : $products->count();
@endphp
    <nav class="jm-crumbs" aria-label="Fil d'Ariane">
        <a href="{{ Route::has('client.dashboard') ? route('client.dashboard') : url('/') }}">Accueil</a><x-jm-icon name="chevron-right" :size="13" />
        <a href="{{ route('client.catalog.index') }}">Catalogue</a>
        @if($activeCat)<x-jm-icon name="chevron-right" :size="13" /><span>{{ $activeCat->name }}</span>@endif
    </nav>

    <div class="jm-cols">
        {{-- Filtres --}}
        <aside class="jm-filters jm-card">
            <p class="jm-filters-title">Catégories</p>
            <a href="{{ route('client.catalog.index', array_filter(['q' => request('q')])) }}" class="{{ !$activeCat ? 'is-on' : '' }}">Toutes les catégories</a>
            @foreach($categories as $c)
                <a href="{{ route('client.catalog.index', array_filter(['q' => request('q'), 'category' => $c->id])) }}" class="{{ $activeCat && $activeCat->id === $c->id ? 'is-on' : '' }}">{{ $c->name }}</a>
            @endforeach
        </aside>

        <section class="jm-results">
            <div class="jm-results-head jm-card">
                <div>
                    <h1>{{ $activeCat->name ?? 'Tous les produits' }}</h1>
                    <p class="jm-muted">
                        {{ $total }} produit{{ $total > 1 ? 's' : '' }}
                        @if(request('q')) · résultats pour « <b>{{ request('q') }}</b> »@endif
                    </p>
                </div>
                @if(request('q') || $activeCat)
                    <a href="{{ route('client.catalog.index') }}" class="jm-btn jm-btn-line jm-btn-sm"><x-jm-icon name="x" :size="15" /> Effacer les filtres</a>
                @endif
            </div>

            @if($products->isEmpty())
                <div class="jm-card jm-empty">
                    <x-jm-icon name="search" :size="42" />
                    <p><b>Aucun produit trouvé.</b><br>Essayez un autre mot-clé ou une autre catégorie.</p>
                    <a href="{{ route('client.catalog.index') }}" class="jm-btn jm-btn-gold">Voir tout le catalogue</a>
                </div>
            @else
                <div class="jm-grid jm-grid-4 jm-grid-flat">
                    @foreach($products as $p)
                        <x-product-card :product="$p" />
                    @endforeach
                </div>
            @endif

            <div class="jm-pager">{{ $products->links() }}</div>
        </section>
    </div>
</x-client-layout>
