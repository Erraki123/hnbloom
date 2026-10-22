@php
    $cats   = $f['categories'] ?? [];
    $offers = $f['offers'] ?? [];
    $direct = \App\Support\SolutionCatalog::products($f['products'] ?? []);
@endphp

<x-hb-layout :title="$f['title']" :description="$f['lead']">
<div class="hb-sol-page">
    @include('partials.hb-solution-bg')

    <section class="hb-page-hero has-photo">
        <div class="hb-wrap hb-page-hero-content">
            <a href="{{ route('solutions.index') }}" class="hb-crumb">← Toutes les solutions</a>
            <p class="hb-eyebrow">{{ strtoupper($f['eyebrow'] ?? 'SOLUTION') }}</p>
            <h1>{{ $f['title'] }}</h1>
            <p class="hb-lead">{{ $f['lead'] }}</p>
            <div class="hb-actions">
                <a class="hb-button hb-button-gold" href="{{ route('contact', ['sujet' => 'quote']) }}">Demander un devis <x-icon name="arrow-right" :size="15" /></a>
                <a class="hb-button hb-button-outline hb-on-photo" href="{{ route('contact', ['sujet' => 'product']) }}">Fiche technique</a>
            </div>
        </div>
    </section>

    @if(count($cats))
    <section class="hb-block">
        <div class="hb-wrap">
            <p class="hb-eyebrow">NOTRE GAMME</p>
            <h2>Ce que nous proposons</h2>
            <div class="hb-product-grid">
                @foreach($cats as $c)
                    @include('partials.hb-category-card', ['c' => $c, 'f' => $f])
                @endforeach
            </div>
        </div>
    </section>
    @elseif(count($offers))
    <section class="hb-block">
        <div class="hb-wrap">
            <p class="hb-eyebrow">NOTRE GAMME</p>
            <h2>Ce que nous proposons</h2>
            <div class="hb-grid hb-grid-3">
                @foreach($offers as $i => $o)
                    <div class="hb-card hb-card-offer reveal" style="--d:{{ ($i % 3) * 0.07 }}s">
                        <div class="hb-card-body">
                            <h3 class="hb-card-title">{{ $o['title'] }}</h3>
                            <p class="hb-card-text">{{ $o['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if(count($direct))
    <section class="hb-block">
        <div class="hb-wrap">
            <p class="hb-eyebrow">NOS PRODUITS</p>
            <h2>Produits</h2>
            <div class="hb-product-grid">
                @foreach($direct as $p)
                    @include('partials.hb-product-card', ['p' => $p])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if(! empty($f['benefits']))
    <section class="hb-block hb-block-alt">
        <div class="hb-wrap hb-two-col">
            <div class="reveal">
                <p class="hb-eyebrow">LES BÉNÉFICES</p>
                <h2>Pourquoi cette solution</h2>
                <ul class="hb-check">
                    @foreach($f['benefits'] as $b)
                        <li><x-icon name="check" :size="16" :stroke="2" /><span>{{ $b }}</span></li>
                    @endforeach
                </ul>
            </div>
            @if(! empty($f['targets']))
                <div class="reveal" style="--d:.1s">
                    <p class="hb-eyebrow">POUR QUI</p>
                    <h2>Nos cibles</h2>
                    <div class="hb-tags">
                        @foreach($f['targets'] as $t)<span class="hb-tag">{{ $t }}</span>@endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
    @endif

    @include('partials.hb-cta', ['heading' => 'Votre projet : '.$f['title']])
</div>

<style>
    .hb-sol-page { position: relative; isolation: isolate; }

    /* Sections transparentes : la vidéo reste visible sur toute la page */
    .hb-sol-page .hb-page-hero,
    .hb-sol-page .hb-block,
    .hb-sol-page .hb-block-alt,
    .hb-sol-page .hb-cta,
    .hb-sol-page .hb-cta-section { background: rgba(9, 9, 8, .3) !important; }

    .hb-sol-page .hb-catalog-bg,
    .hb-sol-page .hb-page-hero-bg,
    .hb-sol-page .hb-page-hero-shade { display: none !important; }

    /* Cartes sans fond */
    .hb-sol-page .hb-product-grid > *,
    .hb-sol-page .hb-product-card,
    .hb-sol-page .hb-category-card,
    .hb-sol-page .hb-cat {
        background: transparent !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    /* Conteneur de l'image : grand */
    .hb-sol-page .hb-product-grid [class*="img"],
    .hb-sol-page .hb-product-grid [class*="pic"],
    .hb-sol-page .hb-product-grid [class*="thumb"],
    .hb-sol-page .hb-product-grid [class*="media"] {
        background: transparent !important;
        width: 100% !important;
        height: 320px !important;      /* augmentez pour agrandir encore */
        overflow: visible !important;
    }

    /* Image : grande, sans filtre */
    .hb-sol-page .hb-product-grid img {
        background: transparent !important;
        object-fit: contain !important;
        width: 100% !important;
        height: 100% !important;
        max-width: none !important;
        max-height: none !important;
        padding: 0 !important;
    }
</style>
</x-hb-layout>
