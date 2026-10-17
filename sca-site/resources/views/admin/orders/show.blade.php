<x-admin-layout title="Commandes">
    <a href="{{ route('admin.orders.index') }}" class="ad-back"><x-ad-icon name="arrow-left" :size="15" /> Retour aux commandes</a>

    <div class="ad-page-head">
        <div>
            <p class="ad-eyebrow">Reçue le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            <h2>Commande #{{ $order->id }}</h2>
        </div>
        <span class="ad-pill s-{{ $order->status }}">{{ $order->status_label }}</span>
    </div>

    <section class="ad-detail">
        <article class="ad-card">
            <header class="ad-card-head"><div><h3>Client</h3></div></header>
            <dl class="ad-dl">
                <div><dt>Nom</dt><dd>{{ $order->user->name }}</dd></div>
                <div><dt>Société</dt><dd>{{ $order->user->company_name }}@if($order->user->activity) <span class="ad-muted">({{ $order->user->activity }})</span>@endif</dd></div>
                <div><dt>E-mail</dt><dd><a href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a></dd></div>
                @if($order->user->phone)<div><dt>Téléphone</dt><dd><a href="tel:{{ $order->user->phone }}">{{ $order->user->phone }}</a></dd></div>@endif
            </dl>
        </article>

        <article class="ad-card">
            <header class="ad-card-head"><div><h3>Statut</h3><p class="ad-muted">Faire avancer la commande</p></div></header>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="ad-status-form">
                @csrf @method('PATCH')
                <select name="status">
                    @foreach(\App\Models\Order::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="ad-btn ad-btn-gold"><x-ad-icon name="check" :size="16" /> Mettre à jour</button>
            </form>
        </article>
    </section>

    <article class="ad-card">
        <header class="ad-card-head"><div><h3>Articles</h3><p class="ad-muted">{{ $order->items->count() }} ligne(s)</p></div></header>
        <div class="ad-table-wrap">
            <table class="ad-table">
                <thead>
                    <tr><th>Produit</th><th>Prix</th><th>Qté</th><th class="ta-r">Sous-total</th></tr>
                </thead>
                <tbody>
                    @foreach($order->items as $i)
                        <tr>
                            <td><strong>{{ $i->product_name }}</strong></td>
                            <td class="ad-nowrap">{{ number_format($i->price, 2, ',', ' ') }} MAD</td>
                            <td>{{ $i->quantity }}</td>
                            <td class="ta-r ad-nowrap">{{ number_format($i->price * $i->quantity, 2, ',', ' ') }} MAD</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="ad-total"><span class="ad-muted">Total</span><b>{{ number_format($order->total, 2, ',', ' ') }} MAD</b></div>
    </article>

    @if($order->note)
        <div class="ad-note">
            <x-ad-icon name="mail" :size="18" />
            <div><strong>Message du client</strong><p>{{ $order->note }}</p></div>
        </div>
    @endif
</x-admin-layout>
