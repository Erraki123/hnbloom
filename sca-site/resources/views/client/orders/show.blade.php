<x-client-layout :title="'Commande #'.$order->id">
    <nav class="jm-crumbs" aria-label="Fil d'Ariane">
        <a href="{{ Route::has('client.dashboard') ? route('client.dashboard') : url('/') }}">Accueil</a><x-jm-icon name="chevron-right" :size="13" />
        <a href="{{ route('client.orders.index') }}">Mes commandes</a><x-jm-icon name="chevron-right" :size="13" />
        <span>#{{ $order->id }}</span>
    </nav>

    <div class="jm-cols jm-cols-cart">
        <section class="jm-card">
            <div class="jm-cart-head jm-between">
                <div>
                    <h1>Commande #{{ $order->id }}</h1>
                    <p class="jm-muted">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <span class="jm-pill jm-pill-lg {{ $order->status === 'pending' ? 'is-pending' : ($order->status === 'cancelled' ? 'is-cancelled' : 'is-ok') }}">{{ $order->status_label }}</span>
            </div>

            <div class="jm-table-wrap">
                <table class="jm-table">
                    <thead><tr><th>Produit</th><th>Prix</th><th>Qté</th><th class="ta-r">Sous-total</th></tr></thead>
                    <tbody>
                        @foreach($order->items as $i)
                            <tr>
                                <td><b>{{ $i->product_name }}</b></td>
                                <td class="jm-nowrap">{{ number_format($i->price, 2, ',', ' ') }} MAD</td>
                                <td>{{ $i->quantity }}</td>
                                <td class="ta-r jm-nowrap">{{ number_format($i->price * $i->quantity, 2, ',', ' ') }} MAD</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <aside>
            <div class="jm-card jm-summary">
                <h2>Récapitulatif</h2>
                <dl>
                    <div><dt>Numéro</dt><dd>#{{ $order->id }}</dd></div>
                    <div><dt>Date</dt><dd>{{ $order->created_at->format('d/m/Y') }}</dd></div>
                    <div><dt>Statut</dt><dd>{{ $order->status_label }}</dd></div>
                    <div class="jm-sum-total"><dt>Total</dt><dd>{{ number_format($order->total, 2, ',', ' ') }} <span>MAD</span></dd></div>
                </dl>
                @if($order->note)
                    <div class="jm-note"><b>Votre message</b><p>{{ $order->note }}</p></div>
                @endif
                @if(Route::has('contact'))
                    <a href="{{ route('contact') }}" class="jm-btn jm-btn-dark jm-btn-block"><x-jm-icon name="headset" :size="18" /> Une question sur cette commande ?</a>
                @endif
            </div>
            <a href="{{ route('client.orders.index') }}" class="jm-back"><x-jm-icon name="chevron-left" :size="16" /> Retour à mes commandes</a>
        </aside>
    </div>
</x-client-layout>
