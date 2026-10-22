@php
    $d = config('hnbloom.domotique');
    $others = collect($d['markets'])->where('slug', '!=', $market['slug']);
    $file = fn ($p) => ($p && file_exists(public_path($p))) ? asset($p) : null;

    // Produits SMART G4 (config/hnbloom_products.php) : home / hotel / hospital
    $s  = strtolower($market['slug']);
    $pk = str_contains($s, 'hotel') ? 'hotel'
        : ((str_contains($s, 'hospital') || str_contains($s, 'hopital') || str_contains($s, 'clinic')) ? 'hospital'
        : 'home');
    $items = config('hnbloom_products.'.$pk) ?: $market['items'];
@endphp

<x-hb-layout :title="$market['title'].' — Domotique'" :description="$market['text']">
<div class="hb-smart-page">
    @include('partials.hb-market-bg')

    <section class="hb-mk">
        <div class="hb-wrap">

            <a class="hb-mk-back" href="{{ route('domotique') }}"><x-icon name="arrow-left" :size="14" /> Retour aux univers</a>

            <p class="hb-mk-eyebrow">{{ $market['category'] }}</p>
            <h1>{{ $market['title'] }}</h1>
            <p class="hb-mk-lead">{{ $market['text'] }}</p>

            <div class="hb-mk-search">
                <input type="search" id="hbMkSearch" placeholder="Rechercher un produit ({{ count($items) }})…" autocomplete="off">
                <span id="hbMkCount">{{ count($items) }} produits</span>
            </div>

            <div class="hb-mk-grid" id="hbMkGrid">
                @foreach($items as $i => $item)
                    @php
                        $name = is_array($item) ? $item['name'] : $item;
                        $text = is_array($item) ? ($item['text'] ?? null) : null;
                        $pic  = is_array($item) ? $file($item['image'] ?? null) : null;
                    @endphp
                    <article class="hb-mk-card" data-s="{{ strtolower($name.' '.$text) }}" @if($i >= 12) hidden @endif>
                        @if($pic)<div class="hb-mk-pic"><img src="{{ $pic }}" alt="{{ $name }}" loading="lazy" decoding="async"></div>@endif
                        <div class="hb-mk-body">
                            <span class="hb-mk-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $name }}</h3>
                            @if($text)<p>{{ $text }}</p>@endif
                        </div>
                    </article>
                @endforeach
            </div>
            <p class="hb-mk-empty" id="hbMkEmpty" hidden>Aucun produit trouvé.</p>
            <nav class="hb-mk-pager" id="hbMkPager" aria-label="Pagination"></nav>

            <div class="hb-mk-cta">
                <h3>Un projet {{ $market['title'] }} ?</h3>
                <a class="hb-button hb-button-gold" href="{{ route('contact', ['sujet' => 'quote']) }}">
                    Demander une étude <x-icon name="arrow-up-right" :size="16" />
                </a>
            </div>

            <div class="hb-mk-others">
                <p>Découvrir aussi</p>
                @foreach($others as $o)
                    <a href="{{ route('domotique.show', $o['slug']) }}">{{ $o['title'] }} <x-icon name="arrow-right" :size="14" /></a>
                @endforeach
            </div>
        </div>
    </section>
</div>

<script>
    (function () {
        var PER = 12, page = 1;
        var q = document.getElementById('hbMkSearch'),
            grid = document.getElementById('hbMkGrid'),
            cards = Array.prototype.slice.call(grid.querySelectorAll('.hb-mk-card')),
            cnt = document.getElementById('hbMkCount'),
            empty = document.getElementById('hbMkEmpty'),
            pager = document.getElementById('hbMkPager'),
            matched = cards.slice();

        function render() {
            var total = matched.length,
                pages = Math.max(1, Math.ceil(total / PER));
            if (page > pages) page = pages;
            var start = (page - 1) * PER, end = start + PER;

            cards.forEach(function (c) { c.hidden = true; });
            matched.forEach(function (c, i) { c.hidden = !(i >= start && i < end); });

            cnt.textContent = total + ' produits';
            empty.hidden = total > 0;

            pager.innerHTML = '';
            if (pages <= 1) return;
            for (var p = 1; p <= pages; p++) {
                var b = document.createElement('button');
                b.type = 'button';
                b.textContent = p;
                if (p === page) b.className = 'is-active';
                b.dataset.p = p;
                pager.appendChild(b);
            }
        }

        pager.addEventListener('click', function (e) {
            var p = e.target.dataset && e.target.dataset.p;
            if (!p) return;
            page = parseInt(p, 10);
            render();
            grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        q.addEventListener('input', function () {
            var v = q.value.trim().toLowerCase();
            matched = cards.filter(function (c) { return !v || c.dataset.s.indexOf(v) > -1; });
            page = 1;
            render();
        });

        render();
    })();
</script>

<style>
    .hb-mk { --g:#d8b45e; --gl:#f0d69a; --border:rgba(216,180,94,.25); padding:80px 0; background:rgba(9,9,8,.35); }
    .hb-mk-back { display:inline-flex; gap:8px; align-items:center; color:#bdb8ad; font-size:11px; text-decoration:none; margin-bottom:34px; }
    .hb-mk-back:hover { color:var(--gl); }
    .hb-mk-eyebrow { margin:0 0 14px; color:var(--g); font-size:10px; letter-spacing:2.4px; font-weight:600; }
    .hb-mk h1 { margin:0; font-family:Georgia,serif; font-size:clamp(40px,5.5vw,64px); font-weight:400; color:var(--gl); }
    .hb-mk-lead { max-width:620px; margin:18px 0 30px; color:#c0bcb2; font-size:13px; line-height:1.9; }
    .hb-mk-search { display:flex; align-items:center; gap:16px; margin-bottom:22px; }
    .hb-mk-search input { flex:1; max-width:420px; height:46px; padding:0 16px; border:1px solid var(--border); background:rgba(16,16,14,.85); color:#f5f1e8; font-size:13px; outline:none; }
    .hb-mk-search input:focus { border-color:var(--g); }
    .hb-mk-search span { color:#8f8a7e; font-size:11px; letter-spacing:1px; }
    .hb-mk-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
    .hb-mk-card[hidden], .hb-mk-empty[hidden] { display:none; }
    .hb-mk-empty { color:#aaa69c; font-size:13px; }

    /* backdrop-filter retiré (très coûteux) + content-visibility pour accélérer le rendu */
    .hb-mk-card { border:1px solid var(--border); background:rgba(16,16,14,.92); overflow:hidden; transition:border-color .25s, transform .25s;
        content-visibility:auto; contain-intrinsic-size:auto 360px; }
    .hb-mk-card:hover { border-color:var(--g); transform:translateY(-3px); }

    /* photos produit : affichées entières sur fond clair */
    .hb-mk-pic { height:200px; padding:14px; display:grid; place-items:center; background:#f3efe6; overflow:hidden; }
    .hb-mk-pic img { max-width:100%; max-height:100%; width:auto; height:auto; object-fit:contain; display:block; }
    .hb-mk-body { padding:22px; }
    .hb-mk-num { color:var(--g); font-size:10px; letter-spacing:1.5px; }
    .hb-mk-body h3 { margin:10px 0 0; font-family:Georgia,serif; font-size:18px; font-weight:400; line-height:1.3; }
    .hb-mk-body p { margin:10px 0 0; color:#aaa69c; font-size:11px; line-height:1.8; }

    /* pagination */
    .hb-mk-pager { display:flex; gap:8px; flex-wrap:wrap; justify-content:center; margin-top:30px; }
    .hb-mk-pager button { min-width:40px; height:40px; border:1px solid var(--border); background:rgba(16,16,14,.85); color:#c0bcb2; font-size:12px; cursor:pointer; transition:.2s; }
    .hb-mk-pager button:hover { border-color:var(--g); color:var(--gl); }
    .hb-mk-pager button.is-active { background:var(--g); border-color:var(--g); color:#111; }

    .hb-mk-cta { display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap;
        margin-top:40px; padding:26px; border:1px solid var(--border); background:rgba(17,17,14,.85); }
    .hb-mk-cta h3 { margin:0; font-family:Georgia,serif; font-size:24px; font-weight:400; }
    .hb-mk-others { display:flex; align-items:center; gap:22px; flex-wrap:wrap; margin-top:30px; font-size:12px; }
    .hb-mk-others p { margin:0; color:#8f8a7e; }
    .hb-mk-others a { display:inline-flex; gap:8px; align-items:center; color:var(--gl); text-decoration:none; }
    @media (max-width:900px){ .hb-mk-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:620px){ .hb-mk-grid { grid-template-columns:1fr; } .hb-mk { padding:55px 0; } }
</style>
</x-hb-layout>
