<x-admin-layout title="Produits">
    <a href="{{ route('admin.products.index') }}" class="ad-back"><x-ad-icon name="arrow-left" :size="15" /> Retour aux produits</a>

    <div class="ad-page-head">
        <div>
            <p class="ad-eyebrow">Catalogue</p>
            <h2>Modifier le produit</h2>
            <p class="ad-muted">{{ $product->name }}</p>
        </div>
    </div>

    @if($product->images->isNotEmpty())
        <article class="ad-card ad-form-card ad-mb">
            <header class="ad-card-head"><div><h3>Photos actuelles</h3><p class="ad-muted">Cliquez sur ✕ pour supprimer une photo.</p></div></header>
            <div class="ad-gallery">
                @foreach($product->images as $img)
                    <div class="ad-gal-item">
                        <img src="{{ asset('storage/'.$img->path) }}" class="ad-thumb-lg" alt="">
                        <form method="POST" action="{{ route('admin.products.images.destroy', $img) }}" data-confirm="Supprimer cette photo ?">
                            @csrf @method('DELETE')
                            <button type="submit" class="ad-gal-x" title="Supprimer la photo" aria-label="Supprimer la photo"><x-ad-icon name="x" :size="14" /></button>
                        </form>
                    </div>
                @endforeach
            </div>
        </article>
    @endif

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="ad-card ad-form-card">
        @csrf @method('PUT')
        <div class="ad-form">
            @include('admin.products._form')
        </div>
        <div class="ad-form-foot">
            <a href="{{ route('admin.products.index') }}" class="ad-btn">Annuler</a>
            <button type="submit" class="ad-btn ad-btn-gold"><x-ad-icon name="save" :size="16" /> Mettre à jour</button>
        </div>
    </form>
</x-admin-layout>
