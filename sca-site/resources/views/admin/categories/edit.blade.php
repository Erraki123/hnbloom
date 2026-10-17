<x-admin-layout title="Catégories">
    <a href="{{ route('admin.categories.index') }}" class="ad-back"><x-ad-icon name="arrow-left" :size="15" /> Retour aux catégories</a>

    <div class="ad-page-head">
        <div>
            <p class="ad-eyebrow">Catalogue</p>
            <h2>Modifier la catégorie</h2>
            <p class="ad-muted">{{ $category->name }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" class="ad-card ad-form-card">
        @csrf @method('PUT')
        <div class="ad-form">
            @include('admin.categories._form')
        </div>
        <div class="ad-form-foot">
            <a href="{{ route('admin.categories.index') }}" class="ad-btn">Annuler</a>
            <button type="submit" class="ad-btn ad-btn-gold"><x-ad-icon name="save" :size="16" /> Mettre à jour</button>
        </div>
    </form>
</x-admin-layout>
