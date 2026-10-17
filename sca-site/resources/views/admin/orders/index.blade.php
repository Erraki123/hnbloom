<x-admin-layout title="Commandes">
    <div class="ad-page-head">
        <div>
            <p class="ad-eyebrow">Commerce</p>
            <h2>Commandes</h2>
            <p class="ad-muted">Suivez et traitez les commandes de vos revendeurs.</p>
        </div>
    </div>

    <article class="ad-card">
        <form method="GET" class="ad-toolbar">
            <select name="status" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                @foreach(\App\Models\Order::STATUSES as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') == $key)>{{ $label }}</option>
                @endforeach
            </select>
            @if(request('status'))
                <a href="{{ route('admin.orders.index') }}" class="ad-btn">Réinitialiser</a>
            @endif
        </form>

        @if($orders->isEmpty())
            <div class="ad-empty"><x-ad-icon name="shopping-cart" :size="28" /><p>Aucune commande.</p></div>
        @else
            <div class="ad-table-wrap">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Client</th>
                            <th>Société</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $o)
                            <tr>
                                <td><strong>#{{ $o->id }}</strong></td>
                                <td>{{ $o->user->name ?? '—' }}</td>
                                <td>{{ $o->user->company_name ?? '—' }}</td>
                                <td class="ad-nowrap">{{ number_format($o->total, 2, ',', ' ') }} MAD</td>
                                <td><span class="ad-pill s-{{ $o->status }}">{{ $o->status_label }}</span></td>
                                <td class="ad-nowrap ad-muted">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="ad-row-actions">
                                        <a href="{{ route('admin.orders.show', $o) }}" class="ad-icon-btn" title="Voir" aria-label="Voir"><x-ad-icon name="eye" :size="17" /></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </article>

    <div class="ad-pager">{{ $orders->links() }}</div>
</x-admin-layout>
