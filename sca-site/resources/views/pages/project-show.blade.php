@php
    use Illuminate\Support\Js;
    use Illuminate\Support\Str;

    $heading    = $project['heading'] ?? $project['title'];
    $paragraphs = $project['paragraphs'] ?? [];
    $images     = array_map('asset', array_values(array_filter(array_merge([$project['image'] ?? null], $project['gallery'] ?? []))));
    $cover      = $images[0] ?? (!empty($project['thumb']) ? asset($project['thumb']) : null);

    // Champs optionnels : s'affichent seulement s'ils existent dans la config
    $meta = array_filter([
        'Client'    => $project['name'] ?? null,
        'Secteur'   => $project['category'] ?? null,
        'Ville'     => $project['city'] ?? null,
        'Année'     => $project['year'] ?? null,
        'Mission'   => $project['role'] ?? null,
    ]);
@endphp

<x-hb-layout :title="$heading" :description="Str::limit($paragraphs[0] ?? 'Projet réalisé par HN BLOOM.', 150)">

    {{-- HERO --}}
    <section class="hb-pd-hero">
        @if($cover)<div class="hb-pd-hero-bg" style="background-image:url('{{ $cover }}')"></div>@endif
        <div class="hb-wrap hb-pd-hero-inner">
            <nav class="hb-pd-crumbs" aria-label="Fil d’Ariane">
                <a href="{{ route('home') }}">Accueil</a><span>/</span>
                <a href="{{ route('projects') }}">Projets</a><span>/</span>
                <b>{{ $project['title'] }}</b>
            </nav>
            <p class="hb-eyebrow">{{ strtoupper($project['category'] ?? 'NOS PROJETS') }}</p>
            <h1>{{ $heading }}</h1>
        </div>
    </section>

    {{-- CONTENU --}}
    <section class="hb-pd-section" x-data="hbLightbox({{ Js::from($images) }})"
             @keydown.escape.window="close()" @keydown.arrow-right.window="open && next()" @keydown.arrow-left.window="open && prev()">
        <div class="hb-wrap">

            @if(count($paragraphs) || count($meta) || !empty($project['website']))
                <div class="hb-pd-layout">
                    <div class="hb-pd-text">
                        <p class="hb-pd-kicker">À PROPOS DU PROJET</p>
                        @foreach($paragraphs as $k => $para)
                            <p class="{{ $k === 0 ? 'is-lead' : '' }}">{{ $para }}</p>
                        @endforeach
                    </div>

                    <aside class="hb-pd-side">
                        <p class="hb-pd-kicker">FICHE PROJET</p>
                        <dl>
                            @foreach($meta as $label => $value)
                                <div><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>
                            @endforeach
                        </dl>
                        @if(!empty($project['website']))
                            <a href="{{ $project['website'] }}" target="_blank" rel="noopener noreferrer" class="hb-pd-btn">
                                Visiter le site <x-icon name="arrow-up-right" :size="15" />
                            </a>
                        @endif
                        <a href="{{ route('contact', ['sujet' => 'quote']) }}" class="hb-pd-btn is-outline">
                            Un projet similaire ? <x-icon name="arrow-right" :size="15" />
                        </a>
                    </aside>
                </div>
            @endif

            {{-- GALERIE --}}
            @if(count($images))
                <div class="hb-pd-gallery-head">
                    <p class="hb-pd-kicker">GALERIE</p>
                    <span>{{ str_pad(count($images), 2, '0', STR_PAD_LEFT) }} visuel{{ count($images) > 1 ? 's' : '' }}</span>
                </div>
                <div class="hb-pd-gallery {{ count($images) === 1 ? 'is-single' : '' }}">
                    @foreach($images as $k => $img)
                        <button type="button" class="hb-pd-shot" @click="show({{ $k }})" aria-label="Agrandir l’image {{ $k + 1 }}">
                            <img src="{{ $img }}" alt="{{ $heading }} — image {{ $k + 1 }}" loading="lazy" decoding="async">
                            <span><x-icon name="maximize-2" :size="18" /></span>
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- PRÉCÉDENT / SUIVANT --}}
            <nav class="hb-pd-nav" aria-label="Navigation entre projets">
                @if($prev)
                    <a href="{{ route('projects.show', $prev['slug']) }}" class="is-prev">
                        @if(!empty($prev['thumb']))<img src="{{ asset($prev['thumb']) }}" alt="" loading="lazy">@endif
                        <span><small>← Projet précédent</small><strong>{{ $prev['title'] }}</strong></span>
                    </a>
                @else <span></span> @endif
                @if($next)
                    <a href="{{ route('projects.show', $next['slug']) }}" class="is-next">
                        <span><small>Projet suivant →</small><strong>{{ $next['title'] }}</strong></span>
                        @if(!empty($next['thumb']))<img src="{{ asset($next['thumb']) }}" alt="" loading="lazy">@endif
                    </a>
                @endif
            </nav>
        </div>

        {{-- LIGHTBOX --}}
        <div class="hb-lb" x-show="open" x-cloak x-transition.opacity.duration.250ms @click.self="close()">
            <button type="button" class="hb-lb-close" @click="close()" aria-label="Fermer"><x-icon name="x" :size="22" /></button>
            <button type="button" class="hb-lb-nav is-l" x-show="imgs.length > 1" @click="prev()" aria-label="Précédent"><x-icon name="arrow-left" :size="20" /></button>
            <img :src="imgs[i]" alt="">
            <button type="button" class="hb-lb-nav is-r" x-show="imgs.length > 1" @click="next()" aria-label="Suivant"><x-icon name="arrow-right" :size="20" /></button>
            <p class="hb-lb-count" x-text="(i + 1) + ' / ' + imgs.length"></p>
        </div>
    </section>

    @include('partials.hb-cta', ['heading' => 'Engagez-vous dans l’Excellence avec HN BLOOM'])

    <script>
        function hbLightbox(imgs) {
            return {
                imgs, i: 0, open: false,
                show(n) { this.i = n; this.open = true; document.documentElement.style.overflow = 'hidden'; },
                close() { this.open = false; document.documentElement.style.overflow = ''; },
                next() { this.i = (this.i + 1) % this.imgs.length; },
                prev() { this.i = (this.i - 1 + this.imgs.length) % this.imgs.length; },
            };
        }
    </script>

    <style>
        [x-cloak] { display:none !important; }
        .hb-pd-hero, .hb-pd-section { --g:#d8b45e; --gl:#f0d69a; --muted:#aaa69c; --border:rgba(216,180,94,.25); color:#f6f1e7; }

        /* HERO */
        .hb-pd-hero { position:relative; overflow:hidden; min-height:380px; display:flex; align-items:flex-end; background:#0b0b09; border-bottom:1px solid var(--border); }
        .hb-pd-hero-bg { position:absolute; inset:0; background-size:cover; background-position:center; opacity:.45; }
        .hb-pd-hero::after { content:""; position:absolute; inset:0; background:linear-gradient(180deg,rgba(11,11,9,.55),rgba(11,11,9,.35) 40%,#0b0b09 100%); }
        .hb-pd-hero-inner { position:relative; z-index:1; width:100%; padding-top:110px; padding-bottom:55px; }
        .hb-pd-crumbs { display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-bottom:28px; font-size:11px; color:#bdb8ad; }
        .hb-pd-crumbs a { color:#bdb8ad; text-decoration:none; } .hb-pd-crumbs a:hover { color:var(--gl); }
        .hb-pd-crumbs b { color:var(--gl); font-weight:400; } .hb-pd-crumbs span { opacity:.4; }
        .hb-pd-hero .hb-eyebrow { margin:0 0 16px; color:var(--g); font-size:10px; font-weight:600; letter-spacing:2.4px; }
        .hb-pd-hero h1 { max-width:880px; margin:0; font-family:Georgia,"Times New Roman",serif; font-size:clamp(34px,5vw,60px); font-weight:400; line-height:1.1; letter-spacing:-1px; }

        /* CONTENU */
        .hb-pd-section { padding:70px 0 85px; background:#090908; }
        .hb-pd-kicker { margin:0 0 22px; color:var(--g); font-size:10px; font-weight:600; letter-spacing:2.2px; }
        .hb-pd-layout { display:grid; grid-template-columns:minmax(0,1.7fr) minmax(280px,1fr); gap:60px; align-items:start; margin-bottom:75px; }
        .hb-pd-text p:not(.hb-pd-kicker) { margin:0 0 20px; color:#c0bcb2; font-size:14px; line-height:2; }
        .hb-pd-text p.is-lead { color:#f1ece0; font-family:Georgia,serif; font-size:22px; line-height:1.65; }
        .hb-pd-side { position:sticky; top:100px; padding:28px; border:1px solid var(--border); background:#10100e; }
        .hb-pd-side dl { margin:0 0 26px; }
        .hb-pd-side dl div { padding:14px 0; border-bottom:1px solid var(--border); }
        .hb-pd-side dt { color:var(--muted); font-size:9px; letter-spacing:1.6px; text-transform:uppercase; }
        .hb-pd-side dd { margin:6px 0 0; font-family:Georgia,serif; font-size:17px; }
        .hb-pd-btn { display:flex; align-items:center; justify-content:space-between; gap:14px; min-height:46px; margin-top:12px; padding:0 18px; border:1px solid var(--g); background:var(--g); color:#15130d; font-size:11px; font-weight:600; text-decoration:none; transition:.25s; }
        .hb-pd-btn:hover { background:transparent; color:var(--gl); }
        .hb-pd-btn.is-outline { background:transparent; color:var(--gl); font-weight:500; }
        .hb-pd-btn.is-outline:hover { background:var(--g); color:#15130d; }

        /* GALERIE */
        .hb-pd-gallery-head { display:flex; align-items:baseline; justify-content:space-between; padding-bottom:16px; margin-bottom:22px; border-bottom:1px solid var(--border); }
        .hb-pd-gallery-head .hb-pd-kicker { margin:0; } .hb-pd-gallery-head span { color:var(--muted); font-size:11px; }
        .hb-pd-gallery { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
        .hb-pd-gallery.is-single { grid-template-columns:1fr; }
        .hb-pd-shot { position:relative; display:block; padding:0; border:1px solid var(--border); background:#1b1a16; overflow:hidden; cursor:zoom-in; aspect-ratio:16/10; }
        .hb-pd-gallery.is-single .hb-pd-shot { aspect-ratio:21/10; }
        .hb-pd-shot img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .7s; }
        .hb-pd-shot:hover img { transform:scale(1.05); }
        .hb-pd-shot span { position:absolute; right:14px; bottom:14px; display:grid; width:38px; height:38px; place-items:center; border:1px solid rgba(255,255,255,.6); border-radius:50%; color:#fff; background:rgba(0,0,0,.35); opacity:0; transition:.3s; }
        .hb-pd-shot:hover span { opacity:1; background:var(--g); border-color:var(--g); color:#111; }

        /* NAV */
        .hb-pd-nav { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:75px; }
        .hb-pd-nav a { display:flex; align-items:center; gap:18px; padding:14px; border:1px solid var(--border); background:#10100e; color:inherit; text-decoration:none; transition:.25s; }
        .hb-pd-nav a:hover { border-color:var(--g); }
        .hb-pd-nav a.is-next { justify-content:flex-end; text-align:right; }
        .hb-pd-nav img { width:90px; height:64px; object-fit:cover; flex:0 0 90px; }
        .hb-pd-nav small { display:block; margin-bottom:6px; color:var(--g); font-size:10px; letter-spacing:1px; }
        .hb-pd-nav strong { font-family:Georgia,serif; font-size:16px; font-weight:400; }

        /* LIGHTBOX */
        .hb-lb { position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; padding:50px 70px; background:rgba(5,5,4,.94); }
        .hb-lb img { max-width:100%; max-height:100%; object-fit:contain; }
        .hb-lb-close, .hb-lb-nav { position:absolute; display:grid; width:46px; height:46px; place-items:center; border:1px solid rgba(255,255,255,.4); border-radius:50%; background:transparent; color:#fff; cursor:pointer; transition:.25s; }
        .hb-lb-close:hover, .hb-lb-nav:hover { background:var(--g); border-color:var(--g); color:#111; }
        .hb-lb-close { top:20px; right:20px; } .hb-lb-nav { top:50%; transform:translateY(-50%); }
        .hb-lb-nav.is-l { left:16px; } .hb-lb-nav.is-r { right:16px; }
        .hb-lb-count { position:absolute; bottom:18px; left:0; right:0; margin:0; text-align:center; color:#bdb8ad; font-size:11px; letter-spacing:1.5px; }

        @media (max-width:900px){
            .hb-pd-layout { grid-template-columns:1fr; gap:36px; margin-bottom:55px; }
            .hb-pd-side { position:static; }
        }
        @media (max-width:620px){
            .hb-pd-hero-inner { padding-top:90px; padding-bottom:40px; }
            .hb-pd-gallery { grid-template-columns:1fr; }
            .hb-pd-nav { grid-template-columns:1fr; }
            .hb-pd-text p.is-lead { font-size:19px; }
            .hb-lb { padding:60px 12px; }
            .hb-pd-shot span { opacity:1; }
        }
        @media (prefers-reduced-motion:reduce){ .hb-pd-shot img,.hb-pd-btn,.hb-pd-nav a { transition:none; } }
    </style>
</x-hb-layout>
