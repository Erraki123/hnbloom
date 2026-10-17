@php
    $maxCat = max(1, (int) ($categories->max('products_count') ?? 1));
    $maxTop = max(1, (int) ($topProducts->max('qty') ?? 1));
@endphp
<x-admin-layout title="Tableau de bord">

    {{-- ===== Bandeau de bienvenue + actions rapides ===== --}}
    <section class="ad-welcome">
        <div>
            <p class="ad-eyebrow">{{ now()->locale('fr')->translatedFormat('l j F Y') }}</p>
            <h2>Bonjour, {{ \Illuminate\Support\Str::of(auth()->user()->name)->before(' ') }}.</h2>
            <p class="ad-muted">Voici l’état de votre activité.</p>
        </div>
        <div class="ad-actions">
            <a href="{{ route('admin.products.create') }}" class="ad-btn ad-btn-gold"><x-ad-icon name="plus" :size="16" /> Produit</a>
            <a href="{{ route('admin.categories.create') }}" class="ad-btn"><x-ad-icon name="plus" :size="16" /> Catégorie</a>
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="ad-btn"><x-ad-icon name="clock" :size="16" /> À traiter</a>
        </div>
    </section>

    {{-- ===== KPI ===== --}}
    <section class="ad-kpis">
        @foreach($kpis as $k)
            <a href="{{ $k['href'] }}" class="ad-kpi" style="--i:{{ $loop->index }}">
                <div class="ad-kpi-top">
                    <span class="ad-kpi-icon"><x-ad-icon :name="$k['icon']" :size="20" /></span>
                    @if(!is_null($k['trend']))
                        <span class="ad-trend {{ $k['trend'] >= 0 ? 'up' : 'down' }}">
                            <x-ad-icon :name="$k['trend'] >= 0 ? 'trending-up' : 'trending-down'" :size="14" />
                            {{ $k['trend'] > 0 ? '+' : '' }}{{ $k['trend'] }}%
                        </span>
                    @endif
                </div>
                <strong class="ad-kpi-value" data-count="{{ $k['value'] }}" data-suffix="{{ $k['suffix'] ?? '' }}">{{ number_format($k['value'], 0, ',', ' ') }}{{ $k['suffix'] ?? '' }}</strong>
                <span class="ad-kpi-label">{{ $k['label'] }}</span>
                <span class="ad-kpi-note">{{ $k['note'] }}</span>
                <svg class="ad-spark" viewBox="0 0 100 28" preserveAspectRatio="none" data-spark="{{ implode(',', $k['spark']) }}"></svg>
            </a>
        @endforeach
    </section>

    {{-- ===== Graphiques ===== --}}
    <section class="ad-grid ad-grid-main">
        <article class="ad-card">
            <header class="ad-card-head">
                <div><h3>Activité</h3><p class="ad-muted">Évolution des ventes</p></div>
                <div class="ad-tools">
                    <div class="ad-tabs" data-group="metric">
                        <button type="button" class="is-active" data-metric="revenue">Chiffre d’affaires</button>
                        <button type="button" data-metric="orders">Commandes</button>
                    </div>
                    <div class="ad-tabs" data-group="range">
                        <button type="button" data-range="7">7 j</button>
                        <button type="button" class="is-active" data-range="30">30 j</button>
                    </div>
                </div>
            </header>
            <div class="ad-chart" id="ad-chart"></div>
        </article>

        <article class="ad-card">
            <header class="ad-card-head">
                <div><h3>Commandes par statut</h3><p class="ad-muted">Toutes périodes</p></div>
            </header>
            <div class="ad-donut-wrap">
                <div class="ad-donut" id="ad-donut"></div>
                <ul class="ad-legend">
                    @foreach($statusData as $s)
                        <li>
                            <i style="background:{{ $s['color'] }}"></i>
                            <span>{{ $s['label'] }}</span>
                            <b>{{ $s['n'] }}</b>
                        </li>
                    @endforeach
                </ul>
            </div>
        </article>
    </section>

    {{-- ===== Commandes récentes + revendeurs en attente ===== --}}
    <section class="ad-grid ad-grid-main">
        <article class="ad-card">
            <header class="ad-card-head">
                <div><h3>Commandes récentes</h3><p class="ad-muted">Les 6 dernières</p></div>
                <a href="{{ route('admin.orders.index') }}" class="ad-link">Tout voir <x-ad-icon name="arrow-right" :size="14" /></a>
            </header>

            @if($recentOrders->isEmpty())
                <div class="ad-empty"><x-ad-icon name="inbox" :size="28" /><p>Aucune commande pour le moment.</p></div>
            @else
                <div class="ad-table-wrap">
                    <table class="ad-table">
                        <thead><tr><th>N°</th><th>Client</th><th>Total</th><th>Statut</th><th>Date</th><th></th></tr></thead>
                        <tbody>
                            @foreach($recentOrders as $o)
                                <tr>
                                    <td>#{{ $o->id }}</td>
                                    <td>
                                        <strong>{{ $o->user->name ?? '—' }}</strong>
                                        <small>{{ $o->user->company_name ?? '' }}</small>
                                    </td>
                                    <td class="ad-nowrap">{{ number_format($o->total, 2, ',', ' ') }} MAD</td>
                                    <td><span class="ad-pill s-{{ $o->status }}">{{ $o->status_label }}</span></td>
                                    <td class="ad-nowrap ad-muted">{{ $o->created_at->locale('fr')->diffForHumans() }}</td>
                                    <td><a href="{{ route('admin.orders.show', $o) }}" class="ad-icon-btn" aria-label="Ouvrir"><x-ad-icon name="arrow-up-right" :size="16" /></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </article>

        <article class="ad-card">
            <header class="ad-card-head">
                <div><h3>Revendeurs à valider</h3><p class="ad-muted">Comptes en attente</p></div>
                <a href="{{ route('admin.clients.index') }}" class="ad-link">Gérer <x-ad-icon name="arrow-right" :size="14" /></a>
            </header>

            @if($pendingClients->isEmpty())
                <div class="ad-empty"><x-ad-icon name="check" :size="28" /><p>Aucun compte en attente.</p></div>
            @else
                <ul class="ad-list">
                    @foreach($pendingClients as $u)
                        <li>
                            <span class="ad-avatar">{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                            <div class="ad-list-main">
                                <strong>{{ $u->name }}</strong>
                                <small>{{ $u->company_name ?: $u->email }}</small>
                            </div>
                            <form method="POST" action="{{ route('admin.clients.approve', $u) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="ad-btn ad-btn-gold ad-btn-sm">Approuver</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>
    </section>

    {{-- ===== Top produits / catégories / messages ===== --}}
    <section class="ad-grid ad-grid-3">
        <article class="ad-card">
            <header class="ad-card-head"><div><h3>Produits les plus commandés</h3><p class="ad-muted">En quantité</p></div></header>
            @if($topProducts->isEmpty())
                <div class="ad-empty"><x-ad-icon name="package" :size="28" /><p>Pas encore de ventes.</p></div>
            @else
                <ul class="ad-bars">
                    @foreach($topProducts as $p)
                        <li>
                            <div class="ad-bar-row"><span>{{ $p->name }}</span><b>{{ (int) $p->qty }}</b></div>
                            <div class="ad-bar"><i style="width:{{ round($p->qty / $maxTop * 100) }}%"></i></div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>

        <article class="ad-card">
            <header class="ad-card-head">
                <div><h3>Catalogue par catégorie</h3><p class="ad-muted">Produits actifs</p></div>
                <a href="{{ route('admin.categories.index') }}" class="ad-link">Gérer <x-ad-icon name="arrow-right" :size="14" /></a>
            </header>
            @if($categories->isEmpty())
                <div class="ad-empty"><x-ad-icon name="tags" :size="28" /><p>Aucune catégorie.</p></div>
            @else
                <ul class="ad-bars">
                    @foreach($categories as $c)
                        <li>
                            <div class="ad-bar-row"><span>{{ $c->name }}</span><b>{{ $c->products_count }}</b></div>
                            <div class="ad-bar"><i style="width:{{ round($c->products_count / $maxCat * 100) }}%"></i></div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>

        <article class="ad-card">
            <header class="ad-card-head">
                <div><h3>Derniers messages</h3><p class="ad-muted">Formulaire de contact</p></div>
                <a href="{{ route('admin.messages.index') }}" class="ad-link">Tout voir <x-ad-icon name="arrow-right" :size="14" /></a>
            </header>
            @if($recentMessages->isEmpty())
                <div class="ad-empty"><x-ad-icon name="mail" :size="28" /><p>Aucun message.</p></div>
            @else
                <ul class="ad-list">
                    @foreach($recentMessages as $m)
                        <li>
                            <span class="ad-avatar {{ $m->status === 'new' ? 'is-new' : '' }}">{{ mb_strtoupper(mb_substr($m->name, 0, 1)) }}</span>
                            <div class="ad-list-main">
                                <strong>{{ $m->name }}</strong>
                                <small>{{ $m->subject_label }} · {{ $m->created_at->locale('fr')->diffForHumans() }}</small>
                            </div>
                            <a href="{{ route('admin.messages.show', $m) }}" class="ad-icon-btn" aria-label="Ouvrir"><x-ad-icon name="arrow-up-right" :size="16" /></a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>
    </section>

    <script type="application/json" id="ad-chart-data">@json($chart)</script>
    <script type="application/json" id="ad-donut-data">@json($statusData)</script>
</x-admin-layout>
