<x-admin-layout title="Revendeurs">
    <div class="ad-page-head">
        <div>
            <p class="ad-eyebrow">Commerce</p>
            <h2>Revendeurs</h2>
            <p class="ad-muted">Validez ou bloquez les comptes clients professionnels.</p>
        </div>
    </div>

    <article class="ad-card">
        @if($clients->isEmpty())
            <div class="ad-empty"><x-ad-icon name="users" :size="28" /><p>Aucun revendeur pour le moment.</p></div>
        @else
            <div class="ad-table-wrap">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Société</th>
                            <th>Activité</th>
                            <th>Téléphone</th>
                            <th>Statut</th>
                            <th class="ta-r">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td>
                                    <div class="ad-cell-user">
                                        <span class="ad-avatar">{{ mb_strtoupper(mb_substr($client->name, 0, 1)) }}</span>
                                        <div>
                                            <strong>{{ $client->name }}</strong>
                                            <small>{{ $client->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $client->company_name ?: '—' }}</td>
                                <td>{{ $client->activity ?: '—' }}</td>
                                <td class="ad-nowrap">{{ $client->phone ?: '—' }}</td>
                                <td>
                                    @if($client->is_approved)
                                        <span class="ad-pill s-approved">Approuvé</span>
                                    @else
                                        <span class="ad-pill s-waiting">En attente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="ad-row-actions">
                                        @if(!$client->is_approved)
                                            <form method="POST" action="{{ route('admin.clients.approve', $client) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="ad-btn ad-btn-gold ad-btn-sm"><x-ad-icon name="check" :size="15" /> Approuver</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.clients.block', $client) }}" data-confirm="Bloquer ce revendeur ? Il ne pourra plus commander.">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="ad-btn ad-btn-outline-danger ad-btn-sm"><x-ad-icon name="ban" :size="15" /> Bloquer</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </article>

    @if($clients instanceof \Illuminate\Contracts\Pagination\Paginator)
        <div class="ad-pager">{{ $clients->links() }}</div>
    @endif
</x-admin-layout>
