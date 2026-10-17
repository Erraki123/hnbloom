<x-client-layout title="Accueil">
@php
    $cats = collect();
    try { $cats = \App\Models\Category::withCount('products')->orderBy('name')->get(); } catch (\Throwable $e) {}
    $hasCatalog = Route::has('client.catalog.index');
@endphp

    {{-- ====== Haut de page : catégories | héros | promos ====== --}}
    <section class="jm-home">
        <aside class="jm-side jm-card">
            <p class="jm-side-title"><x-jm-icon name="grid" :size="17" /> Catégories</p>
            @forelse($cats->take(9) as $c)
                <a href="{{ $hasCatalog ? route('client.catalog.index', ['category' => $c->id]) : '#' }}">
                    <span>{{ $c->name }}</span><x-jm-icon name="chevron-right" :size="15" />
                </a>
            @empty
                <span class="jm-side-empty">Bientôt disponible</span>
            @endforelse
            @if($hasCatalog)<a href="{{ route('client.catalog.index') }}" class="jm-side-all"><span>Voir tout le catalogue</span><x-jm-icon name="arrow-right" :size="15" /></a>@endif
        </aside>

        <div class="jm-hero" data-jm-slider>
            <div class="jm-slide is-on">
                <div class="jm-slide-txt">
                    <span class="jm-chip">Espace revendeur premium</span>
                    <h1>Bienvenue,<em>{{ $user->name }}</em></h1>
                    <p>Découvrez nos solutions automatisées de contrôle d'accès, consultez vos tarifs revendeurs et gérez vos commandes.</p>
                    <div class="jm-slide-cta">
                        @if($hasCatalog)<a href="{{ route('client.catalog.index') }}" class="jm-btn jm-btn-gold">Explorer le catalogue <x-jm-icon name="arrow-right" :size="17" /></a>@endif
                        @if(Route::has('client.cart.index'))<a href="{{ route('client.cart.index') }}" class="jm-btn jm-btn-ghost">Ma liste</a>@endif
                    </div>
                </div>
                <div class="jm-slide-art"><x-barrier-art /></div>
            </div>
            <div class="jm-slide">
                <div class="jm-slide-txt">
                    <span class="jm-chip">Tarifs revendeurs</span>
                    <h1>Vos prix professionnels,<em>toujours visibles</em></h1>
                    <p>Composez votre liste, ajoutez un message et envoyez votre commande en quelques clics.</p>
                    <div class="jm-slide-cta">
                        @if($hasCatalog)<a href="{{ route('client.catalog.index') }}" class="jm-btn jm-btn-gold">Voir les produits <x-jm-icon name="arrow-right" :size="17" /></a>@endif
                    </div>
                </div>
                <div class="jm-slide-art jm-slide-icon"><x-jm-icon name="tag" :size="150" /></div>
            </div>
            <div class="jm-slide">
                <div class="jm-slide-txt">
                    <span class="jm-chip">Support technique</span>
                    <h1>Une équipe d'ingénieurs<em>à votre écoute</em></h1>
                    <p>Un conseil, un devis, une question technique : nous vous répondons rapidement.</p>
                    <div class="jm-slide-cta">
                        @if(Route::has('contact'))<a href="{{ route('contact') }}" class="jm-btn jm-btn-gold">Nous contacter <x-jm-icon name="arrow-right" :size="17" /></a>@endif
                    </div>
                </div>
                <div class="jm-slide-art jm-slide-icon"><x-jm-icon name="headset" :size="150" /></div>
            </div>
            <button type="button" class="jm-sl-prev" data-jm-prev aria-label="Précédent"><x-jm-icon name="chevron-left" :size="22" /></button>
            <button type="button" class="jm-sl-next" data-jm-next aria-label="Suivant"><x-jm-icon name="chevron-right" :size="22" /></button>
            <div class="jm-dots" data-jm-dots></div>
        </div>

        <div class="jm-promos">
            <a href="{{ Route::has('client.orders.index') ? route('client.orders.index') : '#' }}" class="jm-promo">
                <span class="jm-promo-ic"><x-jm-icon name="package" :size="24" /></span>
                <div><b>Mes commandes</b><small>{{ $ordersCount }} commande(s)</small></div>
            </a>
            <a href="{{ Route::has('client.cart.index') ? route('client.cart.index') : '#' }}" class="jm-promo">
                <span class="jm-promo-ic"><x-jm-icon name="cart" :size="24" /></span>
                <div><b>Ma liste</b><small>Préparez votre commande</small></div>
            </a>
            <a href="{{ Route::has('contact') ? route('contact') : '#' }}" class="jm-promo">
                <span class="jm-promo-ic"><x-jm-icon name="headset" :size="24" /></span>
                <div><b>Support technique</b><small>Nos ingénieurs vous répondent</small></div>
            </a>
        </div>
    </section>

    {{-- ====== Atouts ====== --}}
    <section class="jm-usp jm-card">
        <div><x-jm-icon name="factory" :size="30" /><p><b>Fabriqué au Maroc</b><small>Conception et production locales</small></p></div>
        <div><x-jm-icon name="award" :size="30" /><p><b>Normes internationales</b><small>Qualité et conformité</small></p></div>
        <div><x-jm-icon name="users" :size="30" /><p><b>Équipe d'ingénieurs</b><small>Produits adaptés à vos clients</small></p></div>
        <div><x-jm-icon name="headset" :size="30" /><p><b>Support technique</b><small>Disponibilité et délais maîtrisés</small></p></div>
    </section>

    {{-- ====== Catégories en pastilles ====== --}}
    @if($cats->count())
        <section class="jm-block">
            <div class="jm-sec-head"><h2>Acheter par catégorie</h2>@if($hasCatalog)<a href="{{ route('client.catalog.index') }}">Tout voir <x-jm-icon name="chevron-right" :size="15" /></a>@endif</div>
            <div class="jm-sec-body jm-cats">
                @foreach($cats->take(12) as $c)
                    <a href="{{ $hasCatalog ? route('client.catalog.index', ['category' => $c->id]) : '#' }}" class="jm-cat">
                        <span class="jm-cat-img">
                            @if($c->image)<img src="{{ asset('storage/'.$c->image) }}" alt="{{ $c->name }}" loading="lazy">@else<x-jm-icon name="package" :size="28" />@endif
                        </span>
                        <b>{{ $c->name }}</b>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ====== Nouveautés ====== --}}
    @if($newProducts->count())
        <section class="jm-block">
            <div class="jm-sec-head"><h2>Nouveautés · Derniers produits</h2>@if($hasCatalog)<a href="{{ route('client.catalog.index') }}">Voir tout <x-jm-icon name="chevron-right" :size="15" /></a>@endif</div>
            <div class="jm-sec-body jm-grid jm-grid-6">
                @foreach($newProducts as $p)
                    <x-product-card :product="$p" :show-price="true" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ====== Commandes récentes ====== --}}
    <section class="jm-block">
        <div class="jm-sec-head"><h2>Commandes récentes</h2>@if(Route::has('client.orders.index'))<a href="{{ route('client.orders.index') }}">Toutes mes commandes <x-jm-icon name="chevron-right" :size="15" /></a>@endif</div>
        <div class="jm-sec-body jm-flush">
            @forelse($recentOrders as $o)
                <a href="{{ Route::has('client.orders.show') ? route('client.orders.show', $o) : '#' }}" class="jm-order-row">
                    <span class="jm-order-ic"><x-jm-icon name="package" :size="20" /></span>
                    <span class="jm-order-main"><b>Commande #{{ $o->id }}</b><small>{{ $o->created_at->format('d/m/Y à H:i') }}</small></span>
                    <span class="jm-order-total">{{ number_format($o->total, 2, ',', ' ') }} MAD</span>
                    <span class="jm-pill {{ $o->status === 'pending' ? 'is-pending' : ($o->status === 'cancelled' ? 'is-cancelled' : 'is-ok') }}">{{ $o->status_label }}</span>
                    <x-jm-icon name="chevron-right" :size="18" class="jm-order-go" />
                </a>
            @empty
                <div class="jm-empty">
                    <x-jm-icon name="package" :size="40" />
                    <p>Aucune commande pour le moment.</p>
                    @if($hasCatalog)<a href="{{ route('client.catalog.index') }}" class="jm-btn jm-btn-gold">Découvrir le catalogue</a>@endif
                </div>
            @endforelse
        </div>
    </section>
</x-client-layout>
