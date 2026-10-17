<x-public-layout title="Catalogue">
    <section class="bg-paper py-16">
        <div class="mx-auto max-w-7xl px-6">
            <p class="eyebrow">Catalogue</p>
            <h1 class="title-serif mt-3 text-4xl">Tous nos produits</h1>
        </div>
    </section>

    <div class="mx-auto grid max-w-7xl gap-12 px-6 py-14 lg:grid-cols-4" x-data="{ open: false }">
        <aside class="lg:col-span-1">
            <button @click="open = !open" class="btn-outline w-full lg:hidden">Filtres</button>

            <form method="GET" action="{{ route('catalog.index') }}"
                  :class="open ? 'mt-4 block space-y-6' : 'hidden space-y-6 lg:block'">
                <div>
                    <label class="label">Recherche</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, mot-clé..." class="field">
                </div>

                <div>
                    <label class="label">Catégorie</label>
                    <select name="category" class="field">
                        <option value="">Toutes</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }} ({{ $c->products_count }})</option>
                        @endforeach
                    </select>
                </div>

                @if($canSeePrices)
                    <div>
                        <label class="label">Prix (MAD)</label>
                        <div class="flex gap-2">
                            <input type="number" min="0" name="min" value="{{ request('min') }}" placeholder="Min" class="field">
                            <input type="number" min="0" name="max" value="{{ request('max') }}" placeholder="Max" class="field">
                        </div>
                    </div>
                @endif

                <div>
                    <label class="label">Trier par</label>
                    <select name="sort" class="field">
                        <option value="new" @selected(request('sort', 'new') == 'new')>Nouveautés</option>
                        <option value="name" @selected(request('sort') == 'name')>Ordre alphabétique</option>
                        @if($canSeePrices)
                            <option value="price_asc" @selected(request('sort') == 'price_asc')>Prix croissant</option>
                            <option value="price_desc" @selected(request('sort') == 'price_desc')>Prix décroissant</option>
                        @endif
                    </select>
                </div>

                <div class="flex gap-2">
                    <button class="btn-dark flex-1">Appliquer</button>
                    <a href="{{ route('catalog.index') }}" class="btn-outline">Réinitialiser</a>
                </div>
            </form>
        </aside>

        <div class="lg:col-span-3">
            <p class="mb-8 text-sm text-stone">
                <strong class="text-ink">{{ $products->total() }}</strong> produit(s) trouvé(s)
            </p>

            @if($products->count())
                <div class="grid grid-cols-2 gap-x-6 gap-y-12 xl:grid-cols-3">
                    @foreach($products as $p)
                        <x-product-card :product="$p" :show-price="$canSeePrices" />
                    @endforeach
                </div>
                <div class="mt-14">{{ $products->links() }}</div>
            @else
                <div class="border border-dashed border-gray-300 py-20 text-center">
                    <p class="font-serif text-xl">Aucun produit ne correspond à votre recherche.</p>
                    <a href="{{ route('catalog.index') }}" class="btn-outline mt-6">Réinitialiser les filtres</a>
                </div>
            @endif
        </div>
    </div>
</x-public-layout>
