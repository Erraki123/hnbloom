<x-client-layout :title="$product->name">
    <nav class="jm-crumbs" aria-label="Fil d'Ariane">
        <a href="{{ Route::has('client.dashboard') ? route('client.dashboard') : url('/') }}">Accueil</a><x-jm-icon name="chevron-right" :size="13" />
        <a href="{{ route('client.catalog.index') }}">Catalogue</a><x-jm-icon name="chevron-right" :size="13" />
        @if($product->category)
            <a href="{{ route('client.catalog.index', ['category' => $product->category->id]) }}">{{ $product->category->name }}</a><x-jm-icon name="chevron-right" :size="13" />
        @endif
        <span>{{ $product->name }}</span>
    </nav>

    <section class="jm-pdp jm-card">
        {{-- Galerie --}}
        <div class="jm-gallery">
            @if($product->images->count() > 1)
                <div class="jm-thumbs">
                    @foreach($product->images as $k => $img)
                        <button type="button" class="jm-thumb {{ $k === 0 ? 'is-on' : '' }}" data-jm-thumb="{{ asset('storage/'.$img->path) }}" aria-label="Photo {{ $k + 1 }}">
                            <img src="{{ asset('storage/'.$img->path) }}" alt="{{ $product->name }}">
                        </button>
                    @endforeach
                </div>
            @endif
            <div class="jm-stage">
                @if($product->images->count())
                    <img id="jm-main-img" src="{{ asset('storage/'.$product->images->first()->path) }}" alt="{{ $product->name }}">
                @else
                    <span class="jm-pc-noimg"><x-jm-icon name="image" :size="64" /></span>
                @endif
            </div>
        </div>

        {{-- Infos --}}
        <div class="jm-pinfo">
            <p class="jm-pinfo-cat">{{ $product->category->name ?? 'Produit' }}</p>
            <h1>{{ $product->name }}</h1>
            <div class="jm-pinfo-rule"></div>
            <p class="jm-pinfo-price">{{ number_format($product->price, 2, ',', ' ') }} <span>MAD</span></p>
            <p class="jm-muted jm-small">Tarif revendeur · prix hors livraison</p>

            @if(Route::has('client.cart.add'))
                <form method="POST" action="{{ route('client.cart.add', $product) }}" class="jm-buy">
                    @csrf
                    <div class="jm-qty" data-jm-qty>
                        <button type="button" data-step="-1" aria-label="Moins"><x-jm-icon name="minus" :size="16" /></button>
                        <input type="number" name="quantity" value="1" min="1" aria-label="Quantité">
                        <button type="button" data-step="1" aria-label="Plus"><x-jm-icon name="plus" :size="16" /></button>
                    </div>
                    <button type="submit" class="jm-btn jm-btn-gold jm-btn-lg"><x-jm-icon name="cart" :size="19" /> Ajouter à ma liste</button>
                </form>
            @endif

            @if($product->description)
                <div class="jm-desc">
                    <h2>Description</h2>
                    <p>{{ $product->description }}</p>
                </div>
            @endif
        </div>

        {{-- Bandeau d'atouts --}}
        <aside class="jm-pside">
            <h3>Pourquoi nous choisir</h3>
            <ul>
                <li><x-jm-icon name="factory" :size="22" /><p><b>Fabriqué au Maroc</b><small>Conception et production locales</small></p></li>
                <li><x-jm-icon name="award" :size="22" /><p><b>Normes internationales</b><small>Qualité et conformité</small></p></li>
                <li><x-jm-icon name="shield" :size="22" /><p><b>Produit professionnel</b><small>Fiable et durable</small></p></li>
            </ul>
            @if(Route::has('contact'))
                <a href="{{ route('contact') }}" class="jm-btn jm-btn-dark jm-btn-block"><x-jm-icon name="headset" :size="18" /> Une question ? Contactez-nous</a>
            @endif
        </aside>
    </section>

    @isset($related)
        @if($related->count())
            <section class="jm-block">
                <div class="jm-sec-head"><h2>Produits similaires</h2></div>
                <div class="jm-sec-body jm-grid jm-grid-6">
                    @foreach($related as $p)<x-product-card :product="$p" />@endforeach
                </div>
            </section>
        @endif
    @endisset
</x-client-layout>
