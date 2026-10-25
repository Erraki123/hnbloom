@php
    $name = $p['name'] ?? $p['title'] ?? '';
    $text = $p['text'] ?? $p['lead'] ?? null;
    $raw  = $p['image'] ?? null;
    $pic  = null;
    if ($raw) {
        $nb = preg_replace('#^images/products/#', 'images/products_nobg/', $raw);
        $nb = preg_replace('#\.(jpe?g|png|webp)$#i', '.png', $nb);
        if ($nb !== $raw && file_exists(public_path($nb))) $pic = asset($nb);
        elseif (file_exists(public_path($raw))) $pic = asset($raw);
    }
    $href = (\Illuminate\Support\Facades\Route::has('solutions.product') && !empty($p['slug']))
        ? route('solutions.product', $p['slug']) : null;
    $search = \Illuminate\Support\Str::lower($name.' '.$text.' '.($p['group'] ?? ''));
    $dataAttrs = 'data-s="'.e($search).'" data-g="'.e($p['group'] ?? '').'" data-i="'.($idx ?? 0).'" data-n="'.e(\Illuminate\Support\Str::lower($name)).'"';
@endphp

@if($href)
<a href="{{ $href }}" class="hb-shop-card" {!! $dataAttrs !!}>
@else
<article class="hb-shop-card" {!! $dataAttrs !!}>
@endif
    <div class="hb-shop-pic">
        @if($pic)
            <img src="{{ $pic }}" alt="{{ $name }}" loading="lazy" decoding="async">
        @else
            <span class="hb-shop-noimg">{{ $name }}</span>
        @endif
        @if(!empty($p['group']))<span class="hb-shop-badge">{{ $p['group'] }}</span>@endif
    </div>
    <div class="hb-shop-body">
        <h3>{{ $name }}</h3>
        @if($text)<p>{{ $text }}</p>@endif
        @if($href)<span class="hb-shop-btn">Voir le produit <x-icon name="arrow-right" :size="14" /></span>@endif
    </div>
@if($href)
</a>
@else
</article>
@endif