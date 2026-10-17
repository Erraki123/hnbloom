<x-admin-layout title="Catégories">
    <div class="ad-page-head">
        <div>
            <p class="ad-eyebrow">Catalogue</p>
            <h2>Catégories</h2>
            <p class="ad-muted">Organisez votre catalogue par catégories.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="ad-btn ad-btn-gold"><x-ad-icon name="plus" :size="16" /> Ajouter une catégorie</a>
    </div>

    <article class="ad-card">
        @if($categories->isEmpty())
            <div class="ad-empty"><x-ad-icon name="tags" :size="28" /><p>Aucune catégorie pour le moment.</p></div>
        @else
            <div class="ad-table-wrap">
                <table class="ad-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>Produits</th>
                            <th class="ta-r">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $c)
                            <tr>
                                <td>
                                    @if($c->image)
                                        <img src="{{ asset('storage/'.$c->image) }}" class="ad-thumb" alt="">
                                    @else
                                        <span class="ad-thumb ad-thumb-empty"><x-ad-icon name="image" :size="20" /></span>
                                    @endif
                                </td>
                                <td><strong>{{ $c->name }}</strong></td>
                                <td><span class="ad-count">{{ $c->products_count }}</span></td>
                                <td>
                                    <div class="ad-row-actions">
                                        <a href="{{ route('admin.categories.edit', $c) }}" class="ad-icon-btn" title="Modifier" aria-label="Modifier"><x-ad-icon name="pencil" :size="17" /></a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" data-confirm="Supprimer cette catégorie et ses produits ?">
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
</x-admin-layout>
