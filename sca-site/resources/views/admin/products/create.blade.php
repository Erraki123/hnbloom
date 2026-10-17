<x-admin-layout title="Produits">
    <a href="{{ route('admin.products.index') }}" class="ad-back"><x-ad-icon name="arrow-left" :size="15" /> Retour aux produits</a>

    <div class="ad-page-head">
        <div>
            <p class="ad-eyebrow">Catalogue</p>
            <h2>Nouveau produit</h2>
            <p class="ad-muted">Renseignez les informations du produit.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="ad-card ad-form-card">
        @csrf
        <div class="ad-form">
            @include('admin.products._form', ['product' => null])
        </div>
        <div class="ad-form-foot">
            <a href="{{ route('admin.products.index') }}" class="ad-btn">Annuler</a>
            <button type="submit" class="ad-btn ad-btn-gold"><x-ad-icon name="save" :size="16" /> Enregistrer</button>
        </div>
    </form>
</x-admin-layout>
