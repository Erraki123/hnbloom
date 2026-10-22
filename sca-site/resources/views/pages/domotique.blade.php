@php
    $d = config('hnbloom.domotique');
    $c = config('hnbloom');

    // Premier fichier existant dans la liste (public/...)
    $first = function (array $paths) {
        foreach ($paths as $p) {
            if ($p && file_exists(public_path($p))) return asset($p);
        }
        return null;
    };

    $dmVideo = $first([
        $d['bg_video'] ?? 'videos/domotique.mp4',
        $c['solutions_video'] ?? 'videos/solutions.mp4',
        $c['hero_video'] ?? null,
    ]);
    $dmPoster = $first([$d['bg_poster'] ?? null, $c['hero_poster'] ?? null]);
    $dmBg     = $first([$d['bg_image'] ?? null, $d['image'] ?? null, $c['images']['solutions'] ?? null]) ?: $dmPoster;
@endphp

<x-hb-layout
    title="Domotique & Smart Building"
    description="HN BLOOM vous accompagne dans vos projets Smart Home, Smart Hotel et Smart Hospital avec les solutions SMART G4 CONTROL au Maroc."
>
<div class="hb-dm-page">

    @if($dmBg)
        <div class="hb-dm-bg" style="background-image:url('{{ $dmBg }}')" aria-hidden="true"></div>
    @endif

    {{-- HERO : vidéo --}}
    <section class="hb-dm-hero">
        <div class="hb-dm-hero-media" aria-hidden="true"
             @if(!$dmVideo && $dmBg) style="background-image:url('{{ $dmBg }}')" @endif>
            @if($dmVideo)
                <video class="hb-dm-video" autoplay muted loop playsinline preload="auto" disablepictureinpicture
                       @if($dmPoster ?: $dmBg) poster="{{ $dmPoster ?: $dmBg }}" @endif>
                    <source src="{{ $dmVideo }}" type="video/mp4">
                </video>
            @endif
        </div>

        <div class="hb-dm-hero-inner">
            <div class="hb-dm-hero-content">
                <p class="hb-eyebrow">DOMOTIQUE · SMART BUILDING</p>
                <h1>Le bâtiment intelligent,<span>pensé pour demain.</span></h1>
                <p class="hb-dm-lead">{{ $d['lead'] }}</p>
                <a href="#nos-univers" class="hb-dm-button">
                    Choisir mon univers <x-icon name="arrow-down" :size="17" />
                </a>
            </div>

            {{-- PANNEAU DE CHOIX --}}
            <div class="hb-dm-hero-side">
                <span class="hb-dm-side-number">Choisissez votre univers</span>
                <span class="hb-dm-side-line"></span>

                @foreach($d['markets'] as $i => $m)
                    <a href="{{ route('domotique.show', $m['slug']) }}" class="hb-dm-pick">
                        <span class="hb-dm-pick-n">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="hb-dm-pick-t">{{ $m['title'] }}<small>{{ $m['category'] }}</small></span>
                        <x-icon name="arrow-right" :size="16" />
                    </a>
                @endforeach

                <span class="hb-dm-side-location">SMART G4 CONTROL · DISTRIBUTION EXCLUSIVE AU MAROC</span>
            </div>
        </div>
    </section>

    {{-- NOS UNIVERS --}}
    <section class="hb-dm-univers" id="nos-univers">
        <div class="hb-dm-wrap">

            <div class="hb-dm-heading">
                <div>
                    <p class="hb-eyebrow">VOTRE ENVIRONNEMENT</p>
                    <h2>Quel est votre projet ?<span>Cliquez pour voir les produits.</span></h2>
                </div>
                <p class="hb-dm-heading-text">
                    Sélectionnez l’univers qui correspond à votre bâtiment : vous accédez
                    directement à la liste complète des produits et fonctions disponibles.
                </p>
            </div>

            <div class="hb-dm-grid">
                @foreach($d['markets'] as $i => $m)
                    @php
                        $local = !empty($m['image']) && file_exists(public_path($m['image'])) ? asset($m['image']) : null;
                        $img   = $local ?: $m['fallback'];
                        $url   = route('domotique.show', $m['slug']);
                    @endphp
                    <a href="{{ $url }}" class="hb-dm-card reveal" style="--d:{{ $i * 0.08 }}s">
                        <div class="hb-dm-card-image">
                        @php
    $mv = !empty($m['video']) && file_exists(public_path($m['video'])) ? asset($m['video']) : null;
@endphp
@if($mv)
    <video src="{{ $mv }}" poster="{{ $img }}" autoplay muted loop playsinline preload="metadata" disablepictureinpicture></video>
@else
    <img src="{{ $img }}" alt="{{ $m['title'] }} — HN BLOOM" loading="{{ $i === 0 ? 'eager' : 'lazy' }}" decoding="async">
@endif
                            <span class="hb-dm-index">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="hb-dm-count">{{ count($m['items']) }} produits</span>
                            <span class="hb-dm-image-arrow"><x-icon name="arrow-up-right" :size="20" /></span>
                        </div>

                        <div class="hb-dm-card-content">
                            <div class="hb-dm-meta">
                                <span class="hb-dm-icon"><x-icon :name="$m['icon']" :size="20" :stroke="1.5" /></span>
                                <span class="hb-dm-category">{{ $m['category'] }}</span>
                            </div>

                            <h3>{{ $m['title'] }}</h3>
                            <p class="hb-dm-desc">{{ $m['text'] }}</p>

                            <div class="hb-dm-features">
                                @foreach(array_slice($m['items'], 0, 3) as $item)
                                    <span><x-icon name="check" :size="13" :stroke="1.8" />{{ is_array($item) ? $item['name'] : $item }}</span>
                                @endforeach
                                <span class="hb-dm-more">+ {{ max(count($m['items']) - 3, 0) }} autres fonctions</span>
                            </div>

                            <span class="hb-dm-cta">
                                Voir les produits {{ $m['title'] }}
                                <x-icon name="arrow-right" :size="16" />
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</div>

<script>
    // Force la lecture (certains navigateurs bloquent l'autoplay au premier chargement)
    document.querySelectorAll('.hb-dm-video').forEach(function (v) {
        v.muted = true;
        var p = v.play && v.play();
        if (p && p.catch) p.catch(function () {});
    });
</script>

<style>
    .hb-dm-page { position:relative; isolation:isolate; background:#090908; }
    .hb-dm-bg { position:fixed; inset:0; z-index:-1; background-position:center; background-size:cover; background-repeat:no-repeat; }
    .hb-dm-bg::after { content:""; position:absolute; inset:0; background:linear-gradient(180deg,rgba(9,9,8,.78),rgba(9,9,8,.9)); }

    .hb-dm-hero, .hb-dm-univers {
        --g:#d6ad55; --gl:#f1d58e; --panel:#10100e; --border:rgba(214,173,85,.25); --muted:#b1b0a8;
        color:#f5f1e8;
    }

    /* HERO */
    .hb-dm-hero { position:relative; overflow:hidden; padding:clamp(85px,11vw,150px) 6%; background:#10100e; border-bottom:1px solid var(--border); }
    .hb-dm-hero-media { position:absolute; inset:0; z-index:0; background-size:cover; background-position:center; }
    .hb-dm-video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center; }
    .hb-dm-hero-media::after { content:""; position:absolute; inset:0;
        background:
            linear-gradient(90deg,rgba(9,9,8,.88) 0%,rgba(9,9,8,.6) 55%,rgba(9,9,8,.35) 100%),
            linear-gradient(180deg,rgba(9,9,8,0) 60%,rgba(9,9,8,.85) 100%); }
    .hb-dm-hero-inner { position:relative; z-index:1; display:flex; align-items:flex-end; justify-content:space-between; gap:45px; width:100%; max-width:1250px; margin:0 auto; }
    .hb-dm-hero-content { max-width:820px; }
    .hb-dm-hero .hb-eyebrow, .hb-dm-heading .hb-eyebrow { margin:0 0 24px; color:var(--g); font-size:10px; font-weight:700; letter-spacing:3px; }
    .hb-dm-hero h1 { margin:0; color:#f8f4eb; font-family:Georgia,"Times New Roman",serif; font-size:clamp(42px,6vw,76px); font-weight:400; line-height:1.04; letter-spacing:-1.7px; }
    .hb-dm-hero h1 span, .hb-dm-heading h2 span { display:block; color:var(--gl); }
    .hb-dm-lead { max-width:590px; margin:26px 0 0; color:#cfcdc5; font-size:14px; line-height:1.95; }
    .hb-dm-button { display:inline-flex; align-items:center; justify-content:center; gap:22px; min-height:48px; margin-top:32px; padding:0 20px; border:1px solid var(--g); background:var(--g); color:#17130a; font-size:12px; font-weight:700; text-decoration:none; transition:background .25s,color .25s,transform .25s; }
    .hb-dm-button:hover { transform:translateY(-2px); background:transparent; color:var(--gl); }

    /* PANNEAU DE CHOIX (hero) */
    .hb-dm-hero-side { display:flex; flex:0 0 300px; flex-direction:column; align-items:flex-start; padding-bottom:7px; }
    .hb-dm-side-number { color:var(--gl); font-family:Georgia,serif; font-size:20px; }
    .hb-dm-side-line { width:100%; height:1px; margin:17px 0; background:var(--border); }
    .hb-dm-side-location { margin-top:14px; color:#a09e95; font-size:9px; letter-spacing:1.4px; line-height:1.8; }

    .hb-dm-pick { display:flex; align-items:center; gap:14px; width:100%; padding:15px 16px; margin-bottom:10px;
        border:1px solid var(--border); background:rgba(9,9,8,.62); backdrop-filter:blur(8px);
        color:#f5f1e8; text-decoration:none; transition:background .25s,border-color .25s,transform .25s,color .25s; }
    .hb-dm-pick:hover { background:var(--g); border-color:var(--g); color:#17130a; transform:translateX(5px); }
    .hb-dm-pick-n { color:var(--g); font-size:11px; letter-spacing:1.4px; }
    .hb-dm-pick:hover .hb-dm-pick-n { color:#17130a; }
    .hb-dm-pick-t { flex:1; font-family:Georgia,serif; font-size:18px; line-height:1.2; }
    .hb-dm-pick-t small { display:block; margin-top:4px; font-family:inherit; font-size:9px; letter-spacing:1.6px; opacity:.7; }
    .hb-dm-pick svg { flex:0 0 16px; }

    /* NOS UNIVERS */
    .hb-dm-univers { padding:80px 6% 95px; background:rgba(9,9,8,.45); }
    .hb-dm-wrap { max-width:1250px; margin:0 auto; }
    .hb-dm-heading { display:flex; align-items:flex-end; justify-content:space-between; gap:40px; margin-bottom:38px; }
    .hb-dm-heading .hb-eyebrow { margin-bottom:18px; }
    .hb-dm-heading h2 { margin:0; color:#f8f4eb; font-family:Georgia,serif; font-size:clamp(34px,4vw,49px); font-weight:400; line-height:1.12; letter-spacing:-.7px; }
    .hb-dm-heading-text { max-width:360px; margin:0 0 4px; color:var(--muted); font-size:13px; line-height:1.95; }

    .hb-dm-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:22px; }
    .hb-dm-card { display:flex; flex-direction:column; min-width:0; overflow:hidden; border:1px solid var(--border); background:var(--panel); color:inherit; text-decoration:none; transition:border-color .3s,transform .3s,background .3s; }
    .hb-dm-card:hover { transform:translateY(-5px); border-color:rgba(241,213,142,.8); background:#13130f; }
    .hb-dm-card-image { position:relative; height:clamp(200px,20vw,270px); overflow:hidden; background:#1a1915; }
    .hb-dm-card-image img { display:block; width:100%; height:100%; object-fit:cover; transition:transform .7s,filter .4s; }
.hb-dm-card-image video { display:block; width:100%; height:100%; object-fit:cover; transition:transform .7s,filter .4s; }
.hb-dm-card:hover .hb-dm-card-image video { transform:scale(1.045); filter:brightness(1.05); }
    .hb-dm-card:hover .hb-dm-card-image img { transform:scale(1.045); filter:brightness(1.05); }
    .hb-dm-index { position:absolute; top:17px; left:17px; display:grid; width:37px; height:37px; place-items:center; border:1px solid rgba(241,213,142,.65); background:rgba(9,9,8,.82); color:var(--gl); font-size:11px; backdrop-filter:blur(8px); }
    .hb-dm-image-arrow { position:absolute; right:17px; bottom:17px; display:grid; width:42px; height:42px; place-items:center; border:1px solid rgba(241,213,142,.75); background:rgba(9,9,8,.75); color:var(--gl); transition:background .25s,color .25s; }
    .hb-dm-card:hover .hb-dm-image-arrow { background:var(--g); color:#11100c; }
    .hb-dm-count { position:absolute; bottom:17px; left:17px; padding:7px 11px; border:1px solid rgba(241,213,142,.65); background:rgba(9,9,8,.82); color:var(--gl); font-size:10px; letter-spacing:1.2px; backdrop-filter:blur(8px); }

    .hb-dm-card-content { display:flex; flex:1; flex-direction:column; align-items:flex-start; padding:26px 26px 24px; }
    .hb-dm-meta { display:flex; align-items:center; gap:13px; margin-bottom:20px; }
    .hb-dm-icon { display:grid; flex:0 0 40px; width:40px; height:40px; place-items:center; border:1px solid var(--border); border-radius:50%; color:var(--gl); }
    .hb-dm-category { color:var(--g); font-size:9px; font-weight:700; letter-spacing:1.8px; }
    .hb-dm-card-content h3 { margin:0; color:#f5e4bd; font-family:Georgia,serif; font-size:clamp(24px,2.4vw,30px); font-weight:400; line-height:1.25; }
    .hb-dm-desc { min-height:46px; margin:13px 0 18px; color:var(--muted); font-size:12px; line-height:1.9; }
    .hb-dm-features { display:grid; gap:11px; width:100%; padding:17px 0; border-top:1px solid var(--border); border-bottom:1px solid var(--border); }
    .hb-dm-features span { display:flex; align-items:flex-start; gap:9px; color:#ded9ce; font-size:11px; line-height:1.6; }
    .hb-dm-features svg { flex:0 0 13px; margin-top:2px; color:var(--g); }
    .hb-dm-more { color:var(--g) !important; font-size:10px !important; letter-spacing:.6px; }

    /* Bouton clair sur chaque carte */
    .hb-dm-cta { display:flex; align-items:center; justify-content:space-between; gap:14px; width:100%;
        min-height:48px; margin-top:auto; padding:0 18px; border:1px solid var(--g); background:var(--g);
        color:#17130a; font-size:12px; font-weight:700; transition:background .25s,color .25s; }
    .hb-dm-features + .hb-dm-cta { margin-top:22px; }
    .hb-dm-card:hover .hb-dm-cta { background:transparent; color:var(--gl); }
    .hb-dm-cta svg { transition:transform .25s; }
    .hb-dm-card:hover .hb-dm-cta svg { transform:translateX(5px); }

    @media (max-width:1000px){ .hb-dm-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:900px){
        .hb-dm-hero-inner { align-items:flex-start; flex-direction:column; }
        .hb-dm-hero-side { flex-basis:auto; width:100%; max-width:none; margin-top:15px; }
        .hb-dm-heading { flex-direction:column; align-items:flex-start; gap:17px; }
    }
    @media (max-width:620px){
        .hb-dm-hero { padding:70px 6%; }
        .hb-dm-hero h1 { font-size:clamp(38px,10vw,52px); letter-spacing:-1px; }
        .hb-dm-univers { padding:60px 6% 60px; }
        .hb-dm-grid { grid-template-columns:1fr; gap:20px; }
        .hb-dm-card-image { height:235px; }
        .hb-dm-desc { min-height:0; }
    }
    @media (prefers-reduced-motion:reduce){
        .hb-dm-card,.hb-dm-card-image img,.hb-dm-image-arrow,.hb-dm-button,.hb-dm-pick,.hb-dm-cta,.hb-dm-cta svg { transition:none; }
    }
</style>
</x-hb-layout>
