@php
    $projects = $projects ?? config('portfolio.projects');

    // Photo de fond : public/images/projects.jpg (sinon hero.jpg, sinon about.jpg)
    $bgPath = collect([
        config('hnbloom.images.projects', 'images/projects.jpg'),
        config('hnbloom.images.hero'),
        config('hnbloom.images.about'),
    ])->first(fn ($p) => $p && file_exists(public_path($p)));
    $cover = $bgPath ? asset($bgPath) : null;

    // Vidéo du hero (optionnelle)
    $videoPath = config('hnbloom.projects_video');
    $heroVideo = ($videoPath && file_exists(public_path($videoPath))) ? asset($videoPath) : null;
@endphp

<x-hb-layout title="Nos projets" description="Les projets réalisés par HN BLOOM : industrie, événementiel, sport, tertiaire et institutions.">

<div class="hb-pl-page" @if($cover) style="--pl-bg:url('{{ $cover }}')" @endif>

    {{-- HERO (photo toujours visible, vidéo par-dessus si elle démarre) --}}
    <section class="hb-pl-hero">
        @if($cover || $heroVideo)
            <div class="hb-pl-hero-media" aria-hidden="true">
                @if($cover)
                    <div class="hb-pl-hero-bg" style="background-image:url('{{ $cover }}')"></div>
                @endif

                @if($heroVideo)
                    <video class="hb-pl-hero-video" autoplay muted loop playsinline preload="auto" disablepictureinpicture
                           onplaying="this.classList.add('is-on')" onerror="this.remove()">
                        <source src="{{ $heroVideo }}" type="video/mp4" onerror="this.parentNode.remove()">
                    </video>
                @endif
            </div>
        @endif

        <div class="hb-wrap hb-pl-hero-inner">
            <nav class="hb-pl-crumbs" aria-label="Fil d’Ariane">
                <a href="{{ route('home') }}">Accueil</a><span>/</span>
                <b>Projets</b>
            </nav>
            <p class="hb-eyebrow">NOS PROJETS</p>
            <h1>Réalisations<br>et références</h1>
            <p class="hb-pl-lead">Industries, institutions, événements, sport et tertiaire : des sites très différents, un même niveau d’exigence.</p>
            <p class="hb-pl-count">{{ str_pad(count($projects), 2, '0', STR_PAD_LEFT) }} projets</p>
        </div>
    </section>

    {{-- GRILLE --}}
    <section class="hb-pl-section">
        <div class="hb-wrap">
            <div class="hb-pl-grid">
                @foreach($projects as $i => $p)
                    <a href="{{ route('projects.show', $p['slug']) }}"
                       class="hb-pl-card"
                       style="animation-delay:{{ ($i % 3) * 0.07 }}s">
                        <span class="hb-pl-thumb">
                            <img src="{{ asset($p['thumb']) }}" alt="{{ $p['title'] }}" loading="lazy" decoding="async">
                        </span>
                        <span class="hb-pl-info">
                            <small>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</small>
                            <strong>{{ $p['title'] }}</strong>
                            <x-icon name="arrow-up-right" :size="16" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

</div>{{-- /hb-pl-page --}}

    @include('partials.hb-cta', ['heading' => 'Engagez-vous dans l’Excellence avec HN BLOOM'])

    <style>
        /* ====== FOND DE LA PAGE (projects.jpg) ====== */
        .hb-pl-page {
            background:
                linear-gradient(180deg, rgba(9, 9, 8, .74) 0%, rgba(9, 9, 8, .9) 100%),
                var(--pl-bg, none) center / cover no-repeat fixed,
                #090908;
        }

        .hb-pl-hero, .hb-pl-section { --g:#d8b45e; --gl:#f0d69a; --muted:#b1ada3; --border:rgba(216,180,94,.25); color:#f6f1e7; }

        /* HERO */
        .hb-pl-hero { position:relative; overflow:hidden; min-height:440px; display:flex; align-items:flex-end; background:transparent; border-bottom:1px solid var(--border); }
        .hb-pl-hero-media { position:absolute; inset:0; }
        .hb-pl-hero-bg { position:absolute; inset:0; background-size:cover; background-position:center; }
        .hb-pl-hero-video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center; display:block; opacity:0; transition:opacity .8s ease; }
        .hb-pl-hero-video.is-on { opacity:1; }
        /* Voile du hero : plus léger que celui de la page pour bien montrer la photo */
        .hb-pl-hero::after { content:""; position:absolute; inset:0; background:linear-gradient(180deg,rgba(9,9,8,.55),rgba(9,9,8,.38) 45%,rgba(9,9,8,.8) 100%); }
        .hb-pl-hero-inner { position:relative; z-index:1; width:100%; padding-top:120px; padding-bottom:60px; }
        .hb-pl-crumbs { display:flex; gap:10px; align-items:center; margin-bottom:28px; font-size:11px; color:#d0ccc1; }
        .hb-pl-crumbs a { color:#d0ccc1; text-decoration:none; } .hb-pl-crumbs a:hover { color:var(--gl); }
        .hb-pl-crumbs b { color:var(--gl); font-weight:400; } .hb-pl-crumbs span { opacity:.5; }
        .hb-pl-hero .hb-eyebrow { margin:0 0 16px; color:var(--g); font-size:10px; font-weight:600; letter-spacing:2.4px; }
        .hb-pl-hero h1 { max-width:880px; margin:0; font-family:Georgia,"Times New Roman",serif; font-size:clamp(36px,5.4vw,66px); font-weight:400; line-height:1.08; letter-spacing:-1px; color:#f6f1e7; }
        .hb-pl-lead { max-width:560px; margin:22px 0 0; color:#d6d2c8; font-size:14px; line-height:1.9; }
        .hb-pl-count { display:inline-block; margin:26px 0 0; padding-top:14px; border-top:1px solid var(--border); color:var(--gl); font-family:Georgia,serif; font-size:15px; letter-spacing:1px; }

        /* GRILLE */
        .hb-pl-section { padding:70px 0 85px; background:rgba(9,9,8,.35); }
        .hb-pl-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:20px; }
        .hb-pl-card { display:flex; flex-direction:column; border:1px solid var(--border); background:rgba(16,16,14,.92); color:inherit; text-decoration:none; transition:border-color .3s, transform .3s; animation:hb-pl-in .7s cubic-bezier(.2,.7,.2,1) both; }
        .hb-pl-card:hover { border-color:var(--g); transform:translateY(-4px); }
        .hb-pl-thumb { display:block; aspect-ratio:4/3; overflow:hidden; background:#fff; }
        .hb-pl-thumb img { display:block; width:100%; height:100%; padding:22px; object-fit:contain; transition:transform .5s; }
        .hb-pl-card:hover .hb-pl-thumb img { transform:scale(1.05); }
        .hb-pl-info { display:flex; align-items:center; gap:14px; padding:16px 18px; border-top:1px solid var(--border); }
        .hb-pl-info small { color:var(--g); font-family:Georgia,serif; font-size:14px; }
        .hb-pl-info strong { flex:1; font-family:Georgia,serif; font-size:16px; font-weight:400; line-height:1.35; color:#f1ece0; }
        .hb-pl-info svg { flex:none; color:var(--g); transition:transform .3s; }
        .hb-pl-card:hover .hb-pl-info svg { transform:translate(3px,-3px); }

        @keyframes hb-pl-in { from { opacity:0; transform:translateY(22px); } to { opacity:1; transform:none; } }

        @media (max-width:980px){ .hb-pl-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (max-width:600px){
            .hb-pl-hero-inner { padding-top:95px; padding-bottom:42px; }
            .hb-pl-grid { grid-template-columns:1fr; gap:16px; }
        }
        @media (prefers-reduced-motion:reduce){
            .hb-pl-card, .hb-pl-thumb img, .hb-pl-info svg { animation:none; transition:none; }
            .hb-pl-hero-video { display:none; }
        }
    </style>
</x-hb-layout>
