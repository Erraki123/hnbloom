<x-admin-layout title="Produits">
    <div class="ad-page-head">
        <div>
            <p class="ad-eyebrow">Catalogue</p>
            <h2>Produits</h2>
            <p class="ad-muted">Gérez vos produits, leurs photos et leur visibilité.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="ad-btn ad-btn-gold"><x-ad-icon name="plus" :size="16" /> Ajouter un produit</a>
    </div>

    <article class="ad-card">
        @if($products->isEmpty())
            <div class="ad-empty"><x-ad-icon name="package" :size="28" /><p>Aucun produit pour le moment.</p></div>
        @else
            <div class="ad-table-wrap">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Prix</th>
                            <th>Statut</th>
                            <th class="ta-r">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $p)
                            <tr>
                                <td>
                                    @if($p->images->first())
                                        <img src="{{ asset('storage/'.$p->images->first()->path) }}" class="ad-thumb" alt="">
                                    @else
                                        <span class="ad-thumb ad-thumb-empty"><x-ad-icon name="image" :size="20" /></span>
                                    @endif
                                </td>
                                <td><strong>{{ $p->name }}</strong></td>
                                <td>{{ $p->category->name ?? '—' }}</td>
                                <td class="ad-nowrap">{{ number_format($p->price, 2, ',', ' ') }} MAD</td>
                                <td>
                                    @if($p->is_active)
                                        <span class="ad-pill s-active">Actif</span>
                                    @else
                                        <span class="ad-pill s-hidden">Masqué</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="ad-row-actions">
                                        <a href="{{ route('admin.products.edit', $p) }}" class="ad-icon-btn" title="Modifier" aria-label="Modifier"><x-ad-icon name="pencil" :size="17" /></a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $p) }}" data-confirm="Supprimer ce produit ?">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="ad-icon-btn ad-icon-danger" title="Supprimer" aria-label="Supprimer"><x-ad-icon name="trash-2" :size="17" /></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </article>

    <div class="ad-pager">{{ $products->links() }}</div>
</x-admin-layout>
