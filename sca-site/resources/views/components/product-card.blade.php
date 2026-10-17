@props(['product', 'showPrice' => true])
@php
    $img = $product->images->first();
    $isNew = $product->created_at && $product->created_at->gt(now()->subDays(30));
@endphp
<article class="jm-pc">
    <a href="{{ route('client.catalog.show', $product) }}" class="jm-pc-img" aria-label="{{ $product->name }}">
        @if($img)
            <img src="{{ asset('storage/'.$img->path) }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <span class="jm-pc-noimg"><x-jm-icon name="image" :size="34" /></span>
        @endif
        @if($isNew)<span class="jm-tag">Nouveau</span>@endif
    </a>
    <div class="jm-pc-body">
        <p class="jm-pc-cat">{{ $product->category->name ?? 'Produit' }}</p>
        <a href="{{ route('client.catalog.show', $product) }}" class="jm-pc-name">{{ $product->name }}</a>
        @if($showPrice)
            <p class="jm-pc-price">{{ number_format($product->price, 2, ',', ' ') }} <span>MAD</span></p>
        @endif
        @if(Route::has('client.cart.add'))
            <form method="POST" action="{{ route('client.cart.add', $product) }}" class="jm-pc-form">
                @csrf
                <button type="submit" class="jm-pc-add"><x-jm-icon name="cart" :size="16" /> Ajouter à ma liste</button>
            </form>
        @endif
    </div>
</article>
