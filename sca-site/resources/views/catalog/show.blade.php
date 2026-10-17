<x-public-layout :title="$product->name">
    <div class="mx-auto max-w-7xl px-6 py-12">
        <a href="{{ route('catalog.index') }}" class="text-xs uppercase tracking-[0.2em] hover:text-gold">← Retour au catalogue</a>

        <div class="mt-8 grid gap-12 md:grid-cols-2">
            <div x-data="{ main: '{{ $product->images->first() ? asset('storage/'.$product->images->first()->path) : '' }}' }">
                <div class="aspect-square overflow-hidden bg-paper">
                    <template x-if="main"><img :src="main" alt="{{ $product->name }}" class="h-full w-full object-cover"></template>
                    <template x-if="!main"><div class="flex h-full items-center justify-center text-gold/60">Photo à venir</div></template>
                </div>
                @if($product->images->count() > 1)
                    <div class="mt-4 grid grid-cols-5 gap-3">
                        @foreach($product->images as $img)
                            <button type="button" @click="main = '{{ asset('storage/'.$img->path) }}'"
                                    class="aspect-square overflow-hidden border border-transparent bg-paper transition hover:border-gold">
                                <img src="{{ asset('storage/'.$img->path) }}" class="h-full w-full object-cover" alt="">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <p class="eyebrow">{{ $product->category->name }}</p>
                <h1 class="title-serif mt-3 text-4xl leading-tight">{{ $product->name }}</h1>

                <div class="my-6 border-y border-black/10 py-6">
                    @if($canSeePrices)
                        <p class="text-3xl font-medium">{{ number_format($product->price, 2) }} <span class="text-base text-stone">MAD</span></p>
                    @else
                        <p class="text-sm uppercase tracking-wider text-stone">Prix réservé aux revendeurs approuvés</p>
                    @endif
                </div>

                @if($product->description)
                    <p class="whitespace-pre-line leading-relaxed text-stone">{{ $product->description }}</p>
                @endif

                <div class="mt-10">
                    @auth
                        @if(auth()->user()->is_approved && !auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('client.cart.add', $product) }}" class="flex gap-3">
                                @csrf
                                <input type="number" name="quantity" value="1" min="1" class="field !w-24">
                                <button class="btn-dark flex-1">Ajouter à ma liste</button>
                            </form>
                        @elseif(!auth()->user()->is_approved)
                            <p class="border border-gold/50 bg-paper p-4 text-sm">Votre compte est en attente de validation.</p>
                        @endif
                    @else
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('login') }}" class="btn-dark">Se connecter</a>
                            <a href="{{ route('register') }}" class="btn-outline">Devenir revendeur</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        @if($related->count())
            <div class="mt-24">
                <h2 class="title-serif mb-10 text-2xl">Dans la même catégorie</h2>
                <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
                    @foreach($related as $p)
                        <x-product-card :product="$p" :show-price="$canSeePrices" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-public-layout>
