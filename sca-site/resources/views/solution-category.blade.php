<x-hb-layout :title="$cat['title'].' — '.$f['title']" :description="\Illuminate\Support\Str::limit(($cat['lead'] ?? '') ?: $f['lead'], 160)">
<div class="hb-catalog-page">
<x-hb-page-bg :image="$f['image'] ?? null" />

@php
    $hero  = $f['image'] ?? '';
    $lead  = $cat['lead'] ?? '';
    $items = ! empty($cat['children']) || count($products);
    $wa    = 'https://wa.me/212661864602?text=' . urlencode('Bonjour, je souhaite un devis pour : ' . $cat['title']);
@endphp

<section class="hb-page-hero has-photo">
    <div class="hb-page-hero-bg">
        @if($hero && file_exists(public_path($hero)))
            <x-hb-photo :local="$hero" :fallback="null" alt="" />
        @endif
    </div>
    <div class="hb-page-hero-shade"></div>
    <div class="hb-wrap hb-page-hero-content">
        <a href="{{ route('solutions.show', $f['slug']) }}" class="hb-crumb">← {{ $f['title'] }}</a>
        <p class="hb-eyebrow">{{ strtoupper($f['title']) }}</p>
        <h1>{{ $cat['title'] }}</h1>
        @if($lead)<p class="hb-lead">{{ \Illuminate\Support\Str::limit($lead, 200) }}</p>@endif
        <div class="hb-actions">
            <a class="hb-button hb-button-gold" href="{{ route('contact', ['sujet' => 'quote']) }}">Demander un devis</a>
            <a class="hb-button hb-button-outline hb-on-photo" href="{{ $wa }}" target="_blank" rel="noopener">WhatsApp</a>
        </div>
    </div>
</section>

@if($lead && (! $items || mb_strlen($lead) > 200))
<section class="hb-block">
    <div class="hb-wrap hb-split">
        <div class="hb-split-text reveal">
            <p class="hb-eyebrow">À PROPOS</p>
            <h2>{{ $cat['title'] }}</h2>
            <p>{{ $lead }}</p>
            <div class="hb-actions">
                <a class="hb-button hb-button-gold" href="{{ route('contact', ['sujet' => 'quote']) }}">Demande devis</a>
                <a class="hb-button hb-button-outline" href="{{ $wa }}" target="_blank" rel="noopener">Devis via WhatsApp</a>
            </div>
        </div>
        @if(! empty($cat['image']))
            <div class="hb-split-media reveal"><img src="{{ asset($cat['image']) }}" alt="{{ $cat['title'] }}"></div>
        @endif
    </div>
</section>
@endif

@if(! empty($cat['children']))
<section class="hb-block hb-block-alt">
    <div class="hb-wrap">
        <p class="hb-eyebrow">CATÉGORIES</p>
        <div class="hb-product-grid">
            @foreach($cat['children'] as $c)
                @include('partials.hb-category-card', ['c' => $c, 'f' => $f])
            @endforeach
        </div>
    </div>
</section>
@endif

@if(count($products))
<section class="hb-block">
    <div class="hb-wrap">
        <p class="hb-eyebrow">PRODUITS</p>
        <h2>{{ $cat['title'] }}</h2>
        <div class="hb-product-grid">
            @foreach($products as $p)
                @include('partials.hb-product-card', ['p' => $p])
            @endforeach
        </div>
    </div>
</section>
@endif

@include('partials.hb-cta', ['heading' => 'Votre projet : '.$cat['title']])
</div>
</x-hb-layout>
