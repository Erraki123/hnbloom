<x-hb-layout :title="$p['title']" :description="($p['subtitle'] ?? '') ?: $p['title']">
<div class="hb-catalog-page">
<x-hb-page-bg :image="$f['image'] ?? null" />

@php
    $gallery = array_values(array_filter($p['gallery'] ?? [] ?: [$p['image'] ?? null]));
    $wa = 'https://wa.me/212661864602?text=' . urlencode('Bonjour, je souhaite un devis pour : ' . $p['title']);
@endphp

<section class="hb-block">
    <div class="hb-wrap">
        <p class="hb-crumb-line">
            <a href="{{ route('solutions.index') }}">Solutions</a> /
            @if($f)
                <a href="{{ route('solutions.show', $f['slug']) }}">{{ $f['title'] }}</a> /
            @endif
            @if($f && $cat)
                <a href="{{ route('solutions.category', [$f['slug'], $cat['slug']]) }}">{{ $cat['title'] }}</a> /
            @endif
            <strong>{{ $p['title'] }}</strong>
        </p>

        <div class="hb-product">
            <div class="hb-product-gallery">
                @if(count($gallery))
                    <div class="hb-product-main">
                        <img id="pMain" src="{{ asset($gallery[0]) }}" alt="{{ $p['title'] }}">
                    </div>
                    @if(count($gallery) > 1)
                        <div class="hb-thumbs">
                            @foreach($gallery as $g)
                                <button type="button" data-src="{{ asset($g) }}" aria-label="Image {{ $loop->iteration }}">
                                    <img src="{{ asset($g) }}" alt="" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

            <div class="hb-product-info">
                <p class="hb-eyebrow">{{ strtoupper($cat['title'] ?? 'PRODUIT') }}</p>
                <h1>{{ $p['title'] }}</h1>
                @if(!empty($p['subtitle']))
                    <p class="hb-lead">{{ $p['subtitle'] }}</p>
                @endif

                @if(! empty($p['features']))
                    <h3>Description</h3>
                    <ul class="hb-check">
                        @foreach($p['features'] as $feat)
                            <li><x-icon name="check" :size="16" :stroke="2" /><span>{{ $feat }}</span></li>
                        @endforeach
                    </ul>
                @endif

                @foreach(($p['paragraphs'] ?? []) as $para)
                    <p>{{ $para }}</p>
                @endforeach

                <div class="hb-actions">
                    <a class="hb-button hb-button-gold" href="{{ route('contact', ['sujet' => 'quote', 'produit' => $p['title']]) }}">Demande devis</a>
                    <a class="hb-button hb-button-outline" href="{{ $wa }}" target="_blank" rel="noopener">Devis via WhatsApp</a>
                    @foreach(($p['pdfs'] ?? []) as $pdf)
                        <a class="hb-button hb-button-outline" href="{{ asset($pdf['file']) }}" target="_blank" rel="noopener">{{ $pdf['title'] }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@if(count($related))
<section class="hb-block hb-block-alt">
    <div class="hb-wrap">
        <p class="hb-eyebrow">À DÉCOUVRIR AUSSI</p>
        <h2>Produits similaires</h2>
        <div class="hb-product-grid">
            @foreach($related as $r)
                @include('partials.hb-product-card', ['p' => $r])
            @endforeach
        </div>
    </div>
</section>
@endif

@include('partials.hb-cta', ['heading' => 'Un devis pour : '.$p['title']])
</div>
</x-hb-layout>
