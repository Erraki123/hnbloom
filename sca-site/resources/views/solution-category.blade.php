@php
    // يستعمل النسخة الشفافة (products_nobg/*.png) إن وُجدت، وإلا الصورة الأصلية
    $file = function ($p) {
        if (! $p) return null;
        $nobg = preg_replace('#^images/products/#', 'images/products_nobg/', $p);
        $nobg = preg_replace('#\.(jpe?g|png|webp)$#i', '.png', $nobg);
        if ($nobg !== $p && file_exists(public_path($nobg))) return asset($nobg);
        return file_exists(public_path($p)) ? asset($p) : null;
    };

    $catTitle = $cat['title'] ?? $cat['name'] ?? 'Catégorie';
    $catText  = $cat['text'] ?? $cat['lead'] ?? $f['lead'] ?? '';
    $others   = collect($f['categories'] ?? [])->filter(fn ($c) => ($c['slug'] ?? null) !== ($cat['slug'] ?? $category));

    // Vidéo : catégorie > public/videos/solutions/{solution}-{category}.mp4 > famille
    $f['video'] = collect([
        $cat['video'] ?? null,
        'videos/solutions/'.$solution.'-'.$category.'.mp4',
        $f['video'] ?? null,
    ])->first(fn ($p) => $p && file_exists(public_path($p)));
@endphp

<x-hb-layout :title="$catTitle.' — '.$f['title']" :description="$catText">
<div class="hb-sol-page">
    @include('partials.hb-solution-bg')

    <section class="hb-mk">
        <div class="hb-wrap">

            <a class="hb-mk-back" href="{{ route('solutions.show', $solution) }}">
                <x-icon name="arrow-left" :size="14" /> Retour à {{ $f['title'] }}
            </a>

            <p class="hb-mk-eyebrow">{{ strtoupper($f['title']) }}</p>
            <h1>{{ $catTitle }}</h1>
            @if($catText)<p class="hb-mk-lead">{{ $catText }}</p>@endif

            <div class="hb-mk-search">
                <input type="search" id="hbMkSearch" placeholder="Rechercher un produit ({{ count($products) }})…" autocomplete="off">
                <span id="hbMkCount">{{ count($products) }} produits</span>
            </div>

            <div class="hb-mk-grid" id="hbMkGrid">
                @foreach($products as $i => $p)
                    @php
                        $name = $p['name'] ?? $p['title'] ?? '';
                        $text = $p['text'] ?? $p['lead'] ?? null;
                        $pic  = $file($p['image'] ?? null);
                        $href = (\Illuminate\Support\Facades\Route::has('solutions.product') && !empty($p['slug']))
                                ? route('solutions.product', $p['slug']) : null;
                    @endphp
                    @if($href)
                        <a href="{{ $href }}" class="hb-mk-card" data-s="{{ strtolower($name.' '.$text) }}">
                    @else
                        <article class="hb-mk-card" data-s="{{ strtolower($name.' '.$text) }}">
                    @endif
                        <div class="hb-mk-pic">
                            @if($pic)
                                <img src="{{ $pic }}" alt="{{ $name }}" loading="lazy" decoding="async">
                            @else
                                <span class="hb-mk-noimg">{{ $name }}</span>
                            @endif
                        </div>
                        <div class="hb-mk-body">
                            <span class="hb-mk-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $name }}</h3>
                            @if($text)<p>{{ $text }}</p>@endif
                        </div>
                    @if($href) </a> @else </article> @endif
                @endforeach
            </div>
            <p class="hb-mk-empty" id="hbMkEmpty" hidden>Aucun produit trouvé.</p>

            <div class="hb-mk-cta">
                <h3>Un projet {{ $catTitle }} ?</h3>
                <a class="hb-button hb-button-gold" href="{{ route('contact', ['sujet' => 'quote']) }}">
                    Demander une étude <x-icon name="arrow-up-right" :size="16" />
                </a>
            </div>

            @if($others->isNotEmpty())
            <div class="hb-mk-others">
                <p>Découvrir aussi</p>
                @foreach($others as $o)
                    <a href="{{ route('solutions.category', [$solution, $o['slug']]) }}">
                        {{ $o['title'] ?? $o['name'] }} <x-icon name="arrow-right" :size="14" />
                    </a>
                @endforeach
            </div>
            @endif
        </div>
    </section>
</div>

<script>
    (function () {
        var q = document.getElementById('hbMkSearch'), cards = document.querySelectorAll('#hbMkGrid .hb-mk-card'),
            cnt = document.getElementById('hbMkCount'), empty = document.getElementById('hbMkEmpty');
        if (!q) return;
        q.addEventListener('input', function () {
            var v = q.value.trim().toLowerCase(), n = 0;
            cards.forEach(function (c) { var ok = !v || c.dataset.s.indexOf(v) > -1; c.hidden = !ok; if (ok) n++; });
            cnt.textContent = n + ' produits'; empty.hidden = n > 0;
        });
    })();
</script>

<style>
    .hb-sol-page { position: relative; isolation: isolate; }
    .hb-mk { --g:#d8b45e; --gl:#f0d69a; --border:rgba(216,180,94,.25); padding:80px 0; color:#f5f1e8; background:rgba(9,9,8,.25); }
    .hb-mk-back { display:inline-flex; gap:8px; align-items:center; color:#bdb8ad; font-size:11px; text-decoration:none; margin-bottom:34px; }
    .hb-mk-back:hover { color:var(--gl); }
    .hb-mk-eyebrow { margin:0 0 14px; color:var(--g); font-size:10px; letter-spacing:2.4px; font-weight:600; }
    .hb-mk h1 { margin:0; font-family:Georgia,serif; font-size:clamp(40px,5.5vw,64px); font-weight:400; color:var(--gl); }
    .hb-mk-lead { max-width:620px; margin:18px 0 30px; color:#c0bcb2; font-size:13px; line-height:1.9; }
    .hb-mk-search { display:flex; align-items:center; gap:16px; margin-bottom:22px; }
    .hb-mk-search input { flex:1; max-width:420px; height:46px; padding:0 16px; border:1px solid var(--border); background:rgba(16,16,14,.6); color:#f5f1e8; font-size:13px; outline:none; }
    .hb-mk-search input:focus { border-color:var(--g); }
    .hb-mk-search span { color:#8f8a7e; font-size:11px; letter-spacing:1px; }

    .hb-mk-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:24px; }
    .hb-mk-card[hidden], .hb-mk-empty[hidden] { display:none; }
    .hb-mk-empty { color:#aaa69c; font-size:13px; }

    /* Carte transparente */
    .hb-mk-card { display:flex; flex-direction:column; overflow:hidden; color:inherit; text-decoration:none;
        border:1px solid var(--border); background:transparent; backdrop-filter:none; -webkit-backdrop-filter:none;
        transition:transform .35s cubic-bezier(.2,.7,.2,1), border-color .3s, box-shadow .3s; }
    .hb-mk-card:hover { transform:translateY(-6px); border-color:var(--g); box-shadow:0 20px 44px rgba(0,0,0,.55); }

    /* Image grande, sans filtre */
    .hb-mk-pic { display:flex; align-items:center; justify-content:center; aspect-ratio:1 / 1; overflow:visible; background:transparent; }
    .hb-mk-pic img { display:block; width:100%; height:100%; padding:0; object-fit:contain; object-position:center;
        background:transparent; transition:transform .55s cubic-bezier(.2,.7,.2,1); }
    .hb-mk-card:hover .hb-mk-pic img { transform:scale(1.07); }
    .hb-mk-noimg { padding:20px; text-align:center; color:#f1ece0; font-family:Georgia,serif; font-size:15px; }

    .hb-mk-body { padding:18px 20px; border-top:1px solid var(--border); }
    .hb-mk-num { color:var(--g); font-size:10px; letter-spacing:1.5px; }
    .hb-mk-body h3 { margin:10px 0 0; font-family:Georgia,serif; font-size:17px; font-weight:400; line-height:1.35; color:#f1ece0; }
    .hb-mk-body p { margin:10px 0 0; color:#aaa69c; font-size:11px; line-height:1.8; }

    .hb-mk-cta { display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap;
        margin-top:40px; padding:26px; border:1px solid var(--border); background:rgba(17,17,14,.55); }
    .hb-mk-cta h3 { margin:0; font-family:Georgia,serif; font-size:24px; font-weight:400; }
    .hb-mk-others { display:flex; align-items:center; gap:22px; flex-wrap:wrap; margin-top:30px; font-size:12px; }
    .hb-mk-others p { margin:0; color:#8f8a7e; }
    .hb-mk-others a { display:inline-flex; gap:8px; align-items:center; color:var(--gl); text-decoration:none; }

    @media (max-width:980px){ .hb-mk-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:620px){ .hb-mk-grid { grid-template-columns:1fr; gap:16px; } .hb-mk { padding:55px 0; } }
</style>
</x-hb-layout>
