@php
    $img = $p['image'] ?? ($p['gallery'][0] ?? null);
@endphp
<a class="hb-card hb-card-product reveal" href="{{ route('solutions.product', $p['slug']) }}">
    <div class="hb-card-media">
        @if($img)
            <img src="{{ asset($img) }}" alt="{{ $p['title'] }}" loading="lazy" decoding="async">
        @else
            <span class="hb-card-noimg">Image bientôt disponible</span>
        @endif
    </div>
    <div class="hb-card-body">
        <h3 class="hb-card-title">{{ $p['title'] }}</h3>
        @if(!empty($p['subtitle']))
            <p class="hb-card-text">{{ \Illuminate\Support\Str::limit($p['subtitle'], 90) }}</p>
        @endif
        <span class="hb-card-link">Voir le produit <span aria-hidden="true">→</span></span>
    </div>
</a>
