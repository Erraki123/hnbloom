@php
    $catTitle = $cat['title'] ?? $cat['name'] ?? 'Catégorie';
    $catText  = $cat['text'] ?? $cat['lead'] ?? $f['lead'] ?? '';
    $others   = collect($f['categories'] ?? [])->filter(fn ($c) => ($c['slug'] ?? null) !== ($cat['slug'] ?? $category));
    $groups   = collect($products)->pluck('group')->filter()->countBy()->all();
    $total    = count($products);

    // Fond : catégorie > solution (le partial ajoute la vidéo générale en dernier recours)
    $pick = fn (array $a) => collect($a)->first(fn ($x) => $x && file_exists(public_path($x)));
    $v = $pick([
        $cat['video'] ?? null,
        'videos/solutions/'.$solution.'-'.$category.'.mp4',
        'videos/'.$solution.'-'.$category.'.mp4',
        $f['video'] ?? null,
        'videos/solutions/'.$solution.'.mp4',
        'videos/'.$solution.'.mp4',
    ]);
    $i = $pick([$cat['image'] ?? null, $cat['bg_image'] ?? null, $f['image'] ?? null]);
    $scopeVideo = $v ? asset($v) : null;
    $scopeImage = $i ? asset($i) : null;
@endphp

<x-hb-layout :title="$catTitle.' — '.$f['title']" :description="$catText">
<div class="hb-shop-page">
    @include('partials.hb-scope-bg', ['scopeVideo' => $scopeVideo, 'scopeImage' => $scopeImage])

    <section class="hb-shop-sec">
        <div class="hb-wrap">

            <p class="hb-cp-crumbs">
                <a href="{{ route('solutions.index') }}">Solutions</a> /
                <a href="{{ route('solutions.show', $solution) }}">{{ $f['title'] }}</a> /
                <strong>{{ $catTitle }}</strong>
            </p>

            {{-- En-tête --}}
            <header class="hb-cp-head">
                <div>
                    <p class="hb-cp-eyebrow">{{ strtoupper($f['title']) }}</p>
                    <h1>{{ $catTitle }}</h1>
                    @if($catText)<p class="hb-cp-lead">{{ $catText }}</p>@endif
                </div>
                <span class="hb-cp-total"><b>{{ str_pad($total, 2, '0', STR_PAD_LEFT) }}</b> produits</span>
            </header>

            <div class="hb-cp-layout">

                {{-- Barre latérale : filtres --}}
                <aside class="hb-cp-side">
                    <div class="hb-cp-box">
                        <p class="hb-cp-box-title">Recherche</p>
                        <input type="search" id="hbMkSearch" placeholder="Rechercher un produit…" autocomplete="off" aria-label="Rechercher un produit">
                    </div>

                    @if(count($groups) > 1)
                    <div class="hb-cp-box">
                        <p class="hb-cp-box-title">Type de produit</p>
                        <div class="hb-cp-filters" id="hbMkChips">
                            <button type="button" class="is-on" data-g=""><span>Tous</span><em>{{ $total }}</em></button>
                            @foreach($groups as $g => $n)
                                <button type="button" data-g="{{ $g }}"><span>{{ $g }}</span><em>{{ $n }}</em></button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($others->isNotEmpty())
                    <div class="hb-cp-box">
                        <p class="hb-cp-box-title">Autres catégories</p>
                        <div class="hb-cp-links">
                            @foreach($others as $o)
                                <a href="{{ route('solutions.category', [$solution, $o['slug']]) }}">
                                    {{ $o['title'] ?? $o['name'] }} <x-icon name="arrow-right" :size="13" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="hb-cp-box hb-cp-help">
                        <p class="hb-cp-box-title">Besoin d’aide ?</p>
                        <p>Notre équipe vous conseille et prépare votre devis.</p>
                        <a class="hb-button hb-button-gold" href="{{ route('contact', ['sujet' => 'quote']) }}">Demander une étude</a>
                    </div>
                </aside>

                {{-- Résultats --}}
                <div class="hb-cp-main">
                    <div class="hb-cp-bar">
                        <span id="hbMkCount">{{ $total }} produits</span>
                        <label class="hb-cp-sort">
                            <span>Trier par</span>
                            <select id="hbMkSort" aria-label="Trier les produits">
                                <option value="i">Pertinence</option>
                                <option value="az">Nom A → Z</option>
                                <option value="za">Nom Z → A</option>
                            </select>
                        </label>
                    </div>

                    <div class="hb-shop-grid hb-cp-grid" id="hbMkGrid">
                        @foreach($products as $k => $p)
                            @include('partials.hb-shop-card', ['p' => $p, 'idx' => $k])
                        @endforeach
                    </div>
                    <p class="hb-cp-empty" id="hbMkEmpty" hidden>Aucun produit trouvé.</p>
                </div>
            </div>
        </div>
    </section>

    @include('partials.hb-cta', ['heading' => 'Un projet '.$catTitle.' ?'])
</div>

<script>
(function () {
    var grid = document.getElementById('hbMkGrid');
    if (!grid) return;
    var q = document.getElementById('hbMkSearch'),
        sort = document.getElementById('hbMkSort'),
        cnt = document.getElementById('hbMkCount'),
        empty = document.getElementById('hbMkEmpty'),
        chips = document.querySelectorAll('#hbMkChips button'),
        cards = Array.prototype.slice.call(grid.querySelectorAll('.hb-shop-card')),
        group = '';

    function norm(s) { return (s || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ''); }

    function apply() {
        var v = norm(q.value.trim()), n = 0;
        cards.forEach(function (c) {
            var ok = (!v || norm(c.dataset.s).indexOf(v) > -1) && (!group || c.dataset.g === group);
            c.hidden = !ok;
            if (ok) n++;
        });
        cnt.textContent = n + (n > 1 ? ' produits' : ' produit');
        empty.hidden = n > 0;
    }

    function order() {
        var m = sort.value;
        var list = cards.slice().sort(function (a, b) {
            if (m === 'az') return a.dataset.n.localeCompare(b.dataset.n);
            if (m === 'za') return b.dataset.n.localeCompare(a.dataset.n);
            return (+a.dataset.i) - (+b.dataset.i);
        });
        list.forEach(function (c) { grid.appendChild(c); });
    }

    q.addEventListener('input', apply);
    sort.addEventListener('change', order);
    chips.forEach(function (b) {
        b.addEventListener('click', function () {
            group = b.dataset.g;
            chips.forEach(function (x) { x.classList.toggle('is-on', x === b); });
            apply();
        });
    });
})();
</script>

<style>
    .hb-cp-crumbs { margin:0 0 22px; color:#cfcabe; font-size:11px; letter-spacing:.3px; }
    .hb-cp-crumbs a { color:#cfcabe; text-decoration:none; }
    .hb-cp-crumbs a:hover { color:var(--gl); }
    .hb-cp-crumbs strong { color:var(--gl); font-weight:400; }

    .hb-cp-head { display:flex; align-items:flex-end; justify-content:space-between; gap:24px; flex-wrap:wrap;
        margin-bottom:28px; padding-bottom:24px; border-bottom:1px solid var(--border); }
    .hb-cp-eyebrow { display:flex; align-items:center; gap:14px; margin:0 0 12px; color:var(--gl); font-size:10px; font-weight:700; letter-spacing:4px; }
    .hb-cp-eyebrow::before { content:""; flex:0 0 38px; height:1px; background:var(--g); }
    .hb-cp-head h1 { margin:0; font-family:Georgia,serif; font-size:clamp(32px,4.2vw,52px); font-weight:400; line-height:1.1; color:#fffaf0; }
    .hb-cp-lead { max-width:620px; margin:14px 0 0; color:#d4cfc3; font-size:13px; line-height:1.85; }
    .hb-cp-total { color:var(--muted); font-size:12px; letter-spacing:1px; text-transform:uppercase; }
    .hb-cp-total b { color:var(--gl); font-family:Georgia,serif; font-size:26px; font-weight:400; margin-right:6px; }

    .hb-cp-layout { display:grid; grid-template-columns:260px minmax(0,1fr); gap:28px; align-items:start; }

    .hb-cp-side { position:sticky; top:96px; display:grid; gap:14px; }
    .hb-cp-box { padding:18px; border:1px solid var(--border); background:var(--card); }
    .hb-cp-box-title { margin:0 0 14px; color:var(--gl); font-size:10px; font-weight:700; letter-spacing:2.6px; text-transform:uppercase; }
    .hb-cp-box input { width:100%; height:44px; padding:0 14px; border:1px solid var(--border); background:rgba(0,0,0,.45); color:#f5f1e8; font-size:13px; outline:none; transition:border-color .25s, box-shadow .25s; }
    .hb-cp-box input:focus { border-color:var(--g); box-shadow:0 0 0 3px rgba(214,173,85,.15); }

    .hb-cp-filters { display:grid; gap:6px; }
    .hb-cp-filters button { display:flex; align-items:center; justify-content:space-between; gap:10px; width:100%; min-height:40px; padding:0 12px;
        border:1px solid transparent; background:transparent; color:#d9d4c8; font:inherit; font-size:12px; text-align:left; cursor:pointer; transition:background .25s, border-color .25s, color .25s; }
    .hb-cp-filters button em { font-style:normal; color:#8f8a7e; font-size:11px; }
    .hb-cp-filters button:hover { border-color:var(--border); color:var(--gl); }
    .hb-cp-filters button.is-on { background:var(--g); border-color:var(--g); color:#15130d; font-weight:700; }
    .hb-cp-filters button.is-on em { color:#15130d; opacity:.7; }

    .hb-cp-links { display:grid; gap:2px; }
    .hb-cp-links a { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 0; border-bottom:1px solid rgba(214,173,85,.14);
        color:#d9d4c8; font-size:12px; text-decoration:none; transition:color .25s, padding .25s; }
    .hb-cp-links a:last-child { border-bottom:0; }
    .hb-cp-links a:hover { color:var(--gl); padding-left:6px; }

    .hb-cp-help p:not(.hb-cp-box-title) { margin:0 0 14px; color:var(--muted); font-size:12px; line-height:1.7; }

    .hb-cp-bar { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:16px; padding:12px 16px; border:1px solid var(--border); background:var(--card); }
    .hb-cp-bar #hbMkCount { color:var(--muted); font-size:12px; letter-spacing:.6px; }
    .hb-cp-sort { display:flex; align-items:center; gap:10px; color:var(--muted); font-size:12px; }
    .hb-cp-sort select { height:36px; padding:0 12px; border:1px solid var(--border); background:#0c0b09; color:#f5f1e8; font:inherit; font-size:12px; outline:none; cursor:pointer; }
    .hb-cp-sort select:focus { border-color:var(--g); }

    .hb-cp-grid { grid-template-columns:repeat(3,minmax(0,1fr)); }
    .hb-cp-empty { padding:50px 0; text-align:center; color:var(--muted); font-size:14px; }
    .hb-cp-empty[hidden] { display:none; }

    @media (max-width:1100px){ .hb-cp-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:900px){
        .hb-cp-layout { grid-template-columns:1fr; }
        .hb-cp-side { position:static; }
        .hb-cp-help { display:none; }
        .hb-cp-filters { display:flex; overflow-x:auto; gap:8px; padding-bottom:4px; }
        .hb-cp-filters button { flex:none; width:auto; border-color:var(--border); }
        .hb-cp-links { display:flex; flex-wrap:wrap; gap:8px; }
        .hb-cp-links a { border:1px solid var(--border); padding:8px 12px; }
    }
    @media (max-width:760px){
        .hb-cp-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .hb-cp-sort span { display:none; }
    }
</style>
</x-hb-layout>