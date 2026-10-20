<x-hb-layout :title="$f['title']" :description="$f['lead']">

@php
    $cats   = $f['categories'] ?? [];
    $offers = $f['offers'] ?? [];
    $direct = \App\Support\SolutionCatalog::products($f['products'] ?? []);
    $fVideo  = (!empty($f['video']) && file_exists(public_path($f['video']))) ? asset($f['video']) : null;
    $hasImg  = !empty($f['image']) && file_exists(public_path($f['image']));
    $fPoster = $hasImg ? asset($f['image']) : ($f['fallback'] ?? null);
@endphp

<section class="hb-page-hero has-photo">
    <div class="hb-page-hero-bg">
        @if($fVideo)
            <video class="hb-page-hero-video" autoplay muted loop playsinline preload="auto" disablepictureinpicture
                   @if($fPoster) poster="{{ $fPoster }}" @endif>
                <source src="{{ $fVideo }}" type="video/mp4">
            </video>
        @elseif($hasImg)
            <x-hb-photo :local="$f['image']" :fallback="$f['fallback'] ?? null" alt="" />
        @endif
    </div>
    <div class="hb-page-hero-shade"></div>
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
                <div class="hb-card reveal" style="--d:{{ ($i % 3) * 0.07 }}s">
                    <h3>{{ $o['title'] }}</h3>
                    <p>{{ $o['text'] }}</p>
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
</x-hb-layout>
