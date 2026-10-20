@php
    $count = \App\Support\SolutionCatalog::count($c);
    if ($count === 0) {
        $count = count(array_filter(
            \App\Support\SolutionCatalog::allProducts(),
            fn ($x) => in_array($c['slug'], [$x['category'] ?? null, \Illuminate\Support\Str::slug($x['category'] ?? '')], true)
        ));
    }
    $img = $c['image'] ?? null;
@endphp

<a class="hb-cat reveal" href="{{ route('solutions.category', [$f['slug'], $c['slug']]) }}">
    <div class="hb-cat-media">
        @if($img)
            <img src="{{ asset($img) }}" alt="{{ $c['title'] }}" loading="lazy" decoding="async">
        @else
            <span class="hb-cat-noimg">{{ $c['title'] }}</span>
        @endif
    </div>
    <div class="hb-cat-body">
        <h3>{{ $c['title'] }}</h3>
        <p class="hb-cat-count">
            @if($count > 0)
                {{ $count }} produit{{ $count > 1 ? 's' : '' }}
            @else
                Sur demande
            @endif
        </p>
        <span class="hb-cat-link">
            {{ $count > 0 ? 'Voir les produits' : 'Nous consulter' }}
            <span aria-hidden="true">→</span>
        </span>
    </div>
</a>
