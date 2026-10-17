<x-client-layout title="Mes commandes">
    <nav class="jm-crumbs" aria-label="Fil d'Ariane">
        <a href="{{ Route::has('client.dashboard') ? route('client.dashboard') : url('/') }}">Accueil</a><x-jm-icon name="chevron-right" :size="13" />
        <span>Mes commandes</span>
    </nav>

    <div class="jm-card">
        <div class="jm-cart-head"><h1>Mes commandes</h1><p class="jm-muted">Historique de vos achats</p></div>

        @if($orders->isEmpty())
            <div class="jm-empty">
                <x-jm-icon name="package" :size="48" />
                <p><b>Aucune commande.</b><br>Vos commandes apparaîtront ici.</p>
                @if(Route::has('client.catalog.index'))<a href="{{ route('client.catalog.index') }}" class="jm-btn jm-btn-gold">Voir le catalogue</a>@endif
            </div>
        @else
            <div class="jm-table-wrap">
                <table class="jm-table">
                    <thead><tr><th>N°</th><th>Date</th><th>Total</th><th>Statut</th><th></th></tr></thead>
                    <tbody>
                        @foreach($orders as $o)
                            <tr>
                                <td><b>#{{ $o->id }}</b></td>
                                <td class="jm-nowrap">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                                <td class="jm-nowrap"><b>{{ number_format($o->total, 2, ',', ' ') }} MAD</b></td>
                                <td><span class="jm-pill {{ $o->status === 'pending' ? 'is-pending' : ($o->status === 'cancelled' ? 'is-cancelled' : 'is-ok') }}">{{ $o->status_label }}</span></td>
                                <td class="ta-r"><a href="{{ route('client.orders.show', $o) }}" class="jm-link">Détails <x-jm-icon name="chevron-right" :size="14" /></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if(method_exists($orders, 'links'))<div class="jm-pager">{{ $orders->links() }}</div>@endif
</x-client-layout>
