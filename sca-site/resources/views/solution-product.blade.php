@php
    $nobg = function ($g) {
        $n = preg_replace('#^images/products/#', 'images/products_nobg/', $g);
        $n = preg_replace('#\.(jpe?g|png|webp)$#i', '.png', $n);
        return ($n !== $g && file_exists(public_path($n))) ? $n : $g;
    };

    $gallery = array_values(array_filter($p['gallery'] ?? [] ?: [$p['image'] ?? null]));
    $gallery = array_map($nobg, $gallery);
    $gallery = array_values(array_filter($gallery, fn ($g) => $g && file_exists(public_path($g))));

    $specs   = $p['specs'] ?? [];
    $eyebrow = !empty($p['group'])
        ? ($p['brand'] ?? 'ZKTeco') . ' · ' . $p['group']
        : ($cat['title'] ?? 'PRODUIT');

    $wa = 'https://wa.me/212661864602?text=' . urlencode('Bonjour, je souhaite un devis pour : ' . $p['title']);

    // Fond : celui de la catégorie de CE produit (sinon celui de la famille)
    $pick = fn (array $a) => collect($a)->first(fn ($x) => $x && file_exists(public_path($x)));
    $v = $pick([
        $cat['video'] ?? null,
        ($f && $cat) ? 'videos/solutions/'.$f['slug'].'-'.$cat['slug'].'.mp4' : null,
        ($f && $cat) ? 'videos/'.$f['slug'].'-'.$cat['slug'].'.mp4' : null,
        $f['video'] ?? null,
    ]);
    $i = $pick([$cat['image'] ?? null, $cat['bg_image'] ?? null, $f['image'] ?? null]);
    $scopeVideo = $v ? asset($v) : null;
    $scopeImage = $i ? asset($i) : null;
@endphp

<x-hb-layout :title="$p['title']" :description="($p['subtitle'] ?? '') ?: $p['title']">
<div class="hb-shop-page">
    @include('partials.hb-scope-bg', ['scopeVideo' => $scopeVideo, 'scopeImage' => $scopeImage])

    <section class="hb-shop-sec">
        <div class="hb-wrap">
            <p class="hb-pp-crumbs">
                <a href="{{ route('solutions.index') }}">Solutions</a> /
                @if($f)<a href="{{ route('solutions.show', $f['slug']) }}">{{ $f['title'] }}</a> /@endif
                @if($f && $cat)<a href="{{ route('solutions.category', [$f['slug'], $cat['slug']]) }}">{{ $cat['title'] }}</a> /@endif
                <strong>{{ $p['title'] }}</strong>
            </p>

            <div class="hb-pp">
                {{-- Galerie --}}
                <div class="hb-pp-gallery">
                    <div class="hb-pp-main">
                        @if(count($gallery))
                            <img id="pMain" src="{{ asset($gallery[0]) }}" alt="{{ $p['title'] }}">
                        @else
                            <span class="hb-shop-noimg">{{ $p['title'] }}</span>
                        @endif
                    </div>
                    @if(count($gallery) > 1)
                        <div class="hb-pp-thumbs">
                            @foreach($gallery as $g)
                                <button type="button" data-src="{{ asset($g) }}" class="{{ $loop->first ? 'is-on' : '' }}" aria-label="Image {{ $loop->iteration }}">
                                    <img src="{{ asset($g) }}" alt="" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Infos --}}
                <div class="hb-pp-info">
                    <p class="hb-pp-eyebrow">{{ strtoupper($eyebrow) }}</p>
                    <h1>{{ $p['title'] }}</h1>
                    @if(!empty($p['subtitle']))<p class="hb-pp-sub">{{ $p['subtitle'] }}</p>@endif

                    @if(!empty($p['features']))
                        <h3>Description</h3>
                        <ul class="hb-pp-list">
                            @foreach($p['features'] as $feat)
                                <li><x-icon name="check" :size="16" :stroke="2" /><span>{{ $feat }}</span></li>
                            @endforeach
                        </ul>
                    @endif

                    @foreach(($p['paragraphs'] ?? []) as $para)<p class="hb-pp-para">{{ $para }}</p>@endforeach

                    <div class="hb-pp-actions">
                        <a class="hb-button hb-button-gold" href="{{ route('contact', ['sujet' => 'quote', 'produit' => $p['title']]) }}">Demande devis</a>
                        <a class="hb-button hb-button-outline" href="{{ $wa }}" target="_blank" rel="noopener">Devis via WhatsApp</a>
                        @foreach(($p['pdfs'] ?? []) as $pdf)
                            <a class="hb-button hb-button-outline" href="{{ asset($pdf['file']) }}" target="_blank" rel="noopener">{{ $pdf['title'] }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            @if(count($specs))
                <div class="hb-specs">
                    <p class="hb-specs-kicker">CARACTÉRISTIQUES TECHNIQUES</p>
                    <dl>
                        @foreach($specs as $s)
                            <div><dt>{{ $s['label'] }}</dt><dd>{{ $s['value'] }}</dd></div>
                        @endforeach
                    </dl>
                </div>
            @endif

            @if(count($related))
                <div class="hb-pp-related">
                    <p class="hb-pp-eyebrow">À DÉCOUVRIR AUSSI</p>
                    <h2>Produits similaires</h2>
                    <div class="hb-shop-grid">
                        @foreach($related as $r)
                            @include('partials.hb-shop-card', ['p' => $r])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    @include('partials.hb-cta', ['heading' => 'Un devis pour : '.$p['title']])
</div>

<script>
    (function () {
        var main = document.getElementById('pMain');
        var btns = document.querySelectorAll('.hb-pp-thumbs button');
        if (!main) return;
        btns.forEach(function (b) {
            b.addEventListener('click', function () {
                main.src = b.dataset.src;
                btns.forEach(function (x) { x.classList.toggle('is-on', x === b); });
            });
        });
    })();
</script>

<style>
    .hb-pp-crumbs { margin:0 0 26px; color:#cfcabe; font-size:11px; letter-spacing:.3px; }
    .hb-pp-crumbs a { color:#cfcabe; text-decoration:none; }
    .hb-pp-crumbs a:hover { color:var(--gl); }
    .hb-pp-crumbs strong { color:var(--gl); font-weight:400; }

    .hb-pp { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); gap:clamp(24px,4vw,56px); align-items:start; }

    .hb-pp-gallery { position:sticky; top:100px; }
    .hb-pp-main { display:flex; align-items:center; justify-content:center; aspect-ratio:1/1; padding:28px;
        border:1px solid var(--border); background:radial-gradient(ellipse at 50% 40%, #fbfaf6 0%, #ece8dd 100%); }
    .hb-pp-main img { display:block; width:100%; height:100%; object-fit:contain; }
    .hb-pp-thumbs { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:10px; margin-top:12px; }
    .hb-pp-thumbs button { aspect-ratio:1/1; padding:6px; border:1px solid var(--border); cursor:pointer;
        background:radial-gradient(ellipse at 50% 40%, #fbfaf6 0%, #ece8dd 100%); transition:border-color .25s, transform .25s; }
    .hb-pp-thumbs button:hover { transform:translateY(-2px); }
    .hb-pp-thumbs button.is-on { border-color:var(--g); box-shadow:0 0 0 2px rgba(216,180,94,.35); }
    .hb-pp-thumbs img { display:block; width:100%; height:100%; object-fit:contain; }

    .hb-pp-info { padding:6px 0; }
    .hb-pp-eyebrow { margin:0 0 12px; color:var(--g); font-size:10px; font-weight:700; letter-spacing:3px; }
    .hb-pp-info h1 { margin:0; font-family:Georgia,serif; font-size:clamp(28px,3.4vw,42px); font-weight:400; line-height:1.15; color:#fffaf0; }
    .hb-pp-sub { margin:14px 0 0; color:#d8d3c7; font-size:14px; line-height:1.8; }
    .hb-pp-info h3 { margin:28px 0 12px; font-family:Georgia,serif; font-size:18px; font-weight:400; color:var(--gl); }
    .hb-pp-list { list-style:none; margin:0; padding:0; display:grid; gap:10px; }
    .hb-pp-list li { display:flex; gap:10px; align-items:flex-start; color:#e4dfd3; font-size:13px; line-height:1.7; }
    .hb-pp-list svg { flex:none; margin-top:3px; color:var(--g); }
    .hb-pp-para { margin:14px 0 0; color:#cfcabe; font-size:13px; line-height:1.9; }
    .hb-pp-actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:30px; }

    .hb-specs { margin-top:56px; padding:30px clamp(18px,3vw,38px); border:1px solid var(--border); background:rgba(14,14,12,.85); }
    .hb-specs-kicker { margin:0 0 20px; color:var(--g); font-size:10px; font-weight:700; letter-spacing:2.6px; }
    .hb-specs dl { margin:0; display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); column-gap:48px; }
    .hb-specs dl div { display:grid; grid-template-columns:minmax(120px,.8fr) 1.4fr; gap:18px; padding:13px 0; border-bottom:1px solid rgba(216,180,94,.15); }
    .hb-specs dt { color:#aaa69c; font-size:11px; letter-spacing:.8px; text-transform:uppercase; line-height:1.6; }
    .hb-specs dd { margin:0; color:#f1ece0; font-size:13px; line-height:1.7; overflow-wrap:anywhere; }

    .hb-pp-related { margin-top:64px; }
    .hb-pp-related h2 { margin:0 0 24px; font-family:Georgia,serif; font-size:clamp(26px,3vw,36px); font-weight:400; color:var(--gl); }

    @media (max-width:900px){
        .hb-pp { grid-template-columns:1fr; }
        .hb-pp-gallery { position:static; }
    }
    @media (max-width:860px){
        .hb-specs dl { grid-template-columns:1fr; }
        .hb-specs dl div { grid-template-columns:1fr; gap:4px; }
    }
</style>
</x-hb-layout>