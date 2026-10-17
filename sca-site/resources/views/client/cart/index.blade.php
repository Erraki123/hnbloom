<x-client-layout title="Ma liste">
    <nav class="jm-crumbs" aria-label="Fil d'Ariane">
        <a href="{{ Route::has('client.dashboard') ? route('client.dashboard') : url('/') }}">Accueil</a><x-jm-icon name="chevron-right" :size="13" />
        <span>Ma liste</span>
    </nav>

    @if($items->isEmpty())
        <div class="jm-card jm-empty jm-empty-lg">
            <x-jm-icon name="cart" :size="56" />
            <h1>Votre liste est vide</h1>
            <p>Parcourez le catalogue et ajoutez les produits qui vous intéressent.</p>
            @if(Route::has('client.catalog.index'))<a href="{{ route('client.catalog.index') }}" class="jm-btn jm-btn-gold jm-btn-lg">Voir le catalogue</a>@endif
        </div>
    @else
        <div class="jm-cols jm-cols-cart">
            <section>
                <div class="jm-card jm-cart">
                    <div class="jm-cart-head">
                        <h1>Ma liste <span>({{ $items->sum('quantity') }} article{{ $items->sum('quantity') > 1 ? 's' : '' }})</span></h1>
                    </div>

                    @foreach($items as $item)
                        @php $img = $item->product->images->first(); @endphp
                        <article class="jm-cart-row">
                            <a href="{{ route('client.catalog.show', $item->product) }}" class="jm-cart-img">
                                @if($img)<img src="{{ asset('storage/'.$img->path) }}" alt="{{ $item->product->name }}" loading="lazy">@else<x-jm-icon name="image" :size="28" />@endif
                            </a>

                            <div class="jm-cart-info">
                                <a href="{{ route('client.catalog.show', $item->product) }}" class="jm-cart-name">{{ $item->product->name }}</a>
                                <p class="jm-muted jm-small">{{ $item->product->category->name ?? 'Produit' }}</p>
                                <p class="jm-cart-unit">{{ number_format($item->product->price, 2, ',', ' ') }} MAD <span>/ unité</span></p>

                                <form method="POST" action="{{ route('client.cart.remove', $item) }}" class="jm-cart-remove" data-jm-confirm="Retirer ce produit de votre liste ?">
                                    @csrf @method('DELETE')
                                    <button type="submit"><x-jm-icon name="trash" :size="16" /> Retirer</button>
                                </form>
                            </div>

                            <div class="jm-cart-side">
                                <p class="jm-cart-sub">{{ number_format($item->product->price * $item->quantity, 2, ',', ' ') }} <span>MAD</span></p>
                                <form method="POST" action="{{ route('client.cart.update', $item) }}" class="jm-qty-form">
                                    @csrf @method('PATCH')
                                    <div class="jm-qty" data-jm-qty data-auto>
                                        <button type="button" data-step="-1" aria-label="Moins"><x-jm-icon name="minus" :size="16" /></button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" aria-label="Quantité">
                                        <button type="button" data-step="1" aria-label="Plus"><x-jm-icon name="plus" :size="16" /></button>
                                    </div>
                                    <button type="submit" class="jm-btn jm-btn-line jm-btn-sm jm-js-hide">OK</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if(Route::has('client.catalog.index'))
                    <a href="{{ route('client.catalog.index') }}" class="jm-back"><x-jm-icon name="chevron-left" :size="16" /> Continuer mes achats</a>
                @endif
            </section>

            <aside>
                <form method="POST" action="{{ route('client.orders.store') }}" class="jm-card jm-summary">
                    @csrf
                    <h2>Résumé de la commande</h2>
                    <dl>
                        <div><dt>Sous-total ({{ $items->sum('quantity') }} article{{ $items->sum('quantity') > 1 ? 's' : '' }})</dt><dd>{{ number_format($total, 2, ',', ' ') }} MAD</dd></div>
                        <div class="jm-sum-total"><dt>Total</dt><dd>{{ number_format($total, 2, ',', ' ') }} <span>MAD</span></dd></div>
                    </dl>

                    <label class="jm-label" for="note">Message pour nous <span class="jm-muted">(optionnel)</span></label>
                    <textarea id="note" name="note" rows="3" class="jm-input" placeholder="Adresse de livraison, remarque…">{{ old('note') }}</textarea>

                    <button type="submit" class="jm-btn jm-btn-gold jm-btn-lg jm-btn-block">Envoyer ma commande</button>
                    <p class="jm-sum-note"><x-jm-icon name="shield" :size="16" /> Notre équipe confirme votre commande rapidement.</p>
                </form>
            </aside>
        </div>
    @endif
</x-client-layout>
