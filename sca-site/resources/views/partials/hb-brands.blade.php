{{-- =====================================================================
     HN BLOOM · Bloc "Distributeur ZKTeco & SMART G4 CONTROL" (hero accueil)
     Fichier : resources/views/partials/hb-brands.blade.php
     Logos (optionnels) : public/images/brands/zkteco.png  et  public/images/brands/g4.png
     Si un logo est absent, un nom de marque stylisé s'affiche à la place.
     ===================================================================== --}}
@php
    $brandFile = fn ($p) => ($p && file_exists(public_path($p))) ? asset($p) : null;
    $brands = [
        [
            'id'    => 'zkteco',
            'name'  => 'ZKTeco',
            'logo'  => $brandFile('images/brands/zkteco.png'),
            'role'  => 'DISTRIBUTEUR',   // À VALIDER : statut exact de HN BLOOM chez ZKTeco
            'title' => 'Contrôle d’accès & biométrie',
            'text'  => 'Terminaux de reconnaissance faciale assistée par IA, badge et empreinte.',
            'points'=> ['Reconnaissance faciale par IA', 'Badge, empreinte, TCP/IP et Wi-Fi', 'Intégration avec nos obstacles physiques'],
            'cta'   => 'Sécurité électronique',
            'href'  => url('solutions/securite-electronique'),
        ],
        [
            'id'    => 'g4',
            'name'  => 'SMART G4 CONTROL',
            'logo'  => $brandFile('images/brands/g4.png'),
            'role'  => 'REPRÉSENTANT EXCLUSIF AU MAROC',
            'title' => 'Domotique & Smart Building',
            'text'  => 'Smart Home, Smart Hotel et Smart Hospital : confort, sécurité et efficacité énergétique.',
            'points'=> ['Smart Home · Smart Hotel · Smart Hospital', '205 + 59 + 24 produits au catalogue', 'Étude et installation sur site'],
            'cta'   => 'Découvrir la domotique',
            'href'  => url('domotique'),
        ],
    ];
@endphp

<div class="hb-br" x-data="hbBrands()" x-cloak>
    {{-- cubes 3D décoratifs --}}
    <div class="hb-br-cubes" aria-hidden="true">
        @foreach([['s'=>46,'x'=>'-26px','y'=>'-30px','d'=>20],['s'=>30,'x'=>'calc(100% - 10px)','y'=>'18%','d'=>15],['s'=>64,'x'=>'calc(100% - 40px)','y'=>'calc(100% - 20px)','d'=>26]] as $cb)
            <div class="hb-br-float" style="left:{{ $cb['x'] }};top:{{ $cb['y'] }}">
                <div class="hb-br-cube" style="--s:{{ $cb['s'] }}px;--dur:{{ $cb['d'] }}s"><i></i><i></i><i></i><i></i><i></i><i></i></div>
            </div>
        @endforeach
    </div>

    <p class="hb-br-eyebrow">DISTRIBUTEUR DE</p>
    <p class="hb-br-head">Deux marques de référence,<br><em>une seule expertise.</em></p>

    {{-- CHOIX --}}
    <div class="hb-br-tabs" role="tablist">
        @foreach($brands as $i => $b)
            <button type="button" role="tab" class="hb-br-tab" :class="{ 'is-on': cur === {{ $i }} }"
                    :aria-selected="cur === {{ $i }}" @click="pick({{ $i }})">
                <span class="hb-br-tab-n">0{{ $i + 1 }}</span>
                @if($b['logo'])<img src="{{ $b['logo'] }}" alt="{{ $b['name'] }}" loading="lazy">@else<span class="hb-br-tab-t">{{ $b['name'] }}</span>@endif
            </button>
        @endforeach
        <span class="hb-br-tab-bar" :style="'transform:translateX(' + (cur * 100) + '%)'"></span>
    </div>

    {{-- CARTE 3D (tourne quand on choisit) --}}
    <div class="hb-br-stage" x-ref="stage" @pointermove="tilt($event)" @pointerleave="untilt()">
        <div class="hb-br-tilt" x-ref="tilt">
            <div class="hb-br-flip" :class="{ 'is-back': cur === 1 }">
                @foreach($brands as $i => $b)
                    <article class="hb-br-face {{ $i === 1 ? 'is-b' : '' }}" aria-hidden="{{ $i === 0 ? 'false' : 'true' }}" :aria-hidden="cur !== {{ $i }}">
                        <span class="hb-br-line" aria-hidden="true"></span>
                        <div class="hb-br-logo">
                            @if($b['logo'])<img src="{{ $b['logo'] }}" alt="{{ $b['name'] }}">@else<span>{{ $b['name'] }}</span>@endif
                        </div>
                        <p class="hb-br-role">{{ $b['role'] }}</p>
                        <h3>{{ $b['title'] }}</h3>
                        <p class="hb-br-text">{{ $b['text'] }}</p>
                        <ul>
                            @foreach($b['points'] as $pt)
                                <li><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>{{ $pt }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ $b['href'] }}" class="hb-br-btn" :tabindex="cur === {{ $i }} ? 0 : -1">{{ $b['cta'] }} <x-icon name="arrow-right" :size="15" /></a>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function hbBrands() {
    return {
        cur: 0, timer: null, reduce: window.matchMedia('(prefers-reduced-motion:reduce)').matches,
        init() {
            // alternance automatique douce (stoppe dès que le visiteur choisit)
            if (!this.reduce) this.timer = setInterval(() => { this.cur = this.cur ? 0 : 1; }, 7000);
        },
        pick(i) { this.cur = i; if (this.timer) { clearInterval(this.timer); this.timer = null; } },
        tilt(e) {
            if (this.reduce || !window.matchMedia('(hover:hover) and (pointer:fine)').matches) return;
            var r = this.$refs.stage.getBoundingClientRect(),
                x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
            this.$refs.tilt.style.transform = 'rotateX(' + (-y * 9) + 'deg) rotateY(' + (x * 11) + 'deg)';
        },
        untilt() { this.$refs.tilt.style.transform = ''; },
    };
}
</script>

<style>
    .hb-br {
        --g:#d8b45e; --gl:#f0d69a; --bd:rgba(216,180,94,.32);
        position: absolute; z-index: 4; right: clamp(24px, 5vw, 80px); bottom: clamp(70px, 11vh, 130px);
        width: min(400px, 40vw); color: #f6f1e7; font-family: Arial, Helvetica, sans-serif;
        animation: hbBrIn 1.1s .5s cubic-bezier(.2,.7,.2,1) backwards;
    }
    @keyframes hbBrIn { from { opacity:0; transform: translateY(40px); } to { opacity:1; transform:none; } }
    .hb-br-eyebrow { margin:0 0 8px; color:var(--g); font-size:10px; font-weight:700; letter-spacing:3.5px; }
    .hb-br-head { margin:0 0 16px; font-family:Georgia,"Times New Roman",serif; font-size:clamp(20px,1.9vw,26px); line-height:1.2; color:#f6f1e7; }
    .hb-br-head em { font-style:normal; color:var(--gl); }

    /* choix */
    .hb-br-tabs { position:relative; display:grid; grid-template-columns:1fr 1fr; margin-bottom:14px; border:1px solid var(--bd); background:rgba(9,9,8,.7); backdrop-filter:blur(10px); }
    .hb-br-tab { position:relative; z-index:1; display:flex; align-items:center; justify-content:center; gap:10px; min-height:46px; padding:0 10px; border:0; background:none; color:#b9b4a8; cursor:pointer; font:inherit; transition:color .3s; }
    .hb-br-tab.is-on { color:#15130d; }
    .hb-br-tab-n { font-size:9px; letter-spacing:1.5px; opacity:.7; }
    .hb-br-tab-t { font-size:11px; font-weight:700; letter-spacing:1.6px; text-transform:uppercase; white-space:nowrap; }
    .hb-br-tab img { max-height:20px; max-width:100px; object-fit:contain; }
    .hb-br-tab-bar { position:absolute; inset:0 50% 0 0; background:linear-gradient(110deg,#c7983e,#f0d58e); transition:transform .55s cubic-bezier(.7,0,.2,1); }

    /* carte 3D */
    .hb-br-stage { perspective:1300px; }
    .hb-br-tilt { transform-style:preserve-3d; transition:transform .25s ease-out; }
    .hb-br-flip { position:relative; height:330px; transform-style:preserve-3d; transition:transform .9s cubic-bezier(.7,0,.2,1); }
    .hb-br-flip.is-back { transform:rotateY(180deg); }
    .hb-br-face {
        position:absolute; inset:0; display:flex; flex-direction:column; padding:26px 26px 22px;
        border:1px solid var(--bd); background:linear-gradient(160deg, rgba(240,214,154,.08), transparent 40%), rgba(14,14,12,.88);
        backdrop-filter:blur(12px); backface-visibility:hidden; -webkit-backface-visibility:hidden;
        box-shadow:0 40px 80px rgba(0,0,0,.55), 0 0 60px rgba(216,180,94,.1);
    }
    .hb-br-face.is-b { transform:rotateY(180deg); }
    .hb-br-line { position:absolute; top:-1px; left:12%; right:12%; height:2px; background:linear-gradient(90deg,transparent,var(--g),transparent); }
    .hb-br-logo { display:flex; align-items:center; min-height:34px; margin-bottom:12px; }
    .hb-br-logo img { max-height:34px; max-width:170px; object-fit:contain; }
    .hb-br-logo span { font-family:Georgia,serif; font-size:23px; letter-spacing:.08em; color:var(--gl); }
    .hb-br-role { margin:0; color:var(--g); font-size:9px; font-weight:700; letter-spacing:2.2px; }
    .hb-br-face h3 { margin:8px 0 0; font-family:Georgia,serif; font-size:20px; font-weight:400; color:#f6f1e7; }
    .hb-br-text { margin:8px 0 0; color:#b9b4a8; font-size:11.5px; line-height:1.75; }
    .hb-br-face ul { display:grid; gap:7px; margin:14px 0 0; padding:12px 0 0; list-style:none; border-top:1px solid var(--bd); }
    .hb-br-face li { display:flex; align-items:center; gap:9px; color:#e4ded0; font-size:11px; }
    .hb-br-face li svg { flex:none; color:var(--g); }
    /* bouton 3D (comme la page de connexion) */
    .hb-br-btn {
        display:inline-flex; align-items:center; justify-content:space-between; gap:14px; margin-top:auto; min-height:46px; padding:0 20px;
        background:linear-gradient(110deg,#c7983e,#f0d58e); color:#15130d; text-decoration:none;
        font-size:11px; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
        box-shadow:0 5px 0 #8a6a25, 0 16px 26px rgba(0,0,0,.5); transition:transform .15s, box-shadow .15s, filter .2s;
    }
    .hb-br-btn:hover { filter:brightness(1.07); transform:translateY(-2px); box-shadow:0 7px 0 #8a6a25, 0 20px 32px rgba(0,0,0,.55); }
    .hb-br-btn:active { transform:translateY(4px); box-shadow:0 1px 0 #8a6a25, 0 6px 12px rgba(0,0,0,.5); }

    /* cubes dorés */
    .hb-br-cubes { position:absolute; inset:0; perspective:700px; pointer-events:none; z-index:-1; }
    .hb-br-float { position:absolute; animation:hbBrFloat 8s ease-in-out infinite alternate; }
    .hb-br-cube { position:relative; width:var(--s); height:var(--s); transform-style:preserve-3d; animation:hbBrSpin var(--dur) linear infinite; }
    .hb-br-cube i { position:absolute; inset:0; border:1px solid rgba(216,180,94,.45); background:linear-gradient(135deg,rgba(216,180,94,.14),rgba(216,180,94,.02)); }
    .hb-br-cube i:nth-child(1){transform:translateZ(calc(var(--s)/2))} .hb-br-cube i:nth-child(2){transform:rotateY(180deg) translateZ(calc(var(--s)/2))}
    .hb-br-cube i:nth-child(3){transform:rotateY(90deg) translateZ(calc(var(--s)/2))} .hb-br-cube i:nth-child(4){transform:rotateY(-90deg) translateZ(calc(var(--s)/2))}
    .hb-br-cube i:nth-child(5){transform:rotateX(90deg) translateZ(calc(var(--s)/2))} .hb-br-cube i:nth-child(6){transform:rotateX(-90deg) translateZ(calc(var(--s)/2))}
    @keyframes hbBrSpin { to { transform:rotateX(360deg) rotateY(360deg); } }
    @keyframes hbBrFloat { from { transform:translateY(-12px); } to { transform:translateY(12px); } }

    /* tablette / mobile : le bloc passe sous le texte du hero */
    @media (max-width: 1100px) {
        .hb-br { position:relative; right:auto; bottom:auto; width:min(440px, calc(100% - 12%)); margin:30px 6% 90px; }
    }
    @media (max-width: 480px) { .hb-br-flip { height:350px; } .hb-br-face { padding:22px 20px 18px; } }
    @media (prefers-reduced-motion: reduce) {
        .hb-br, .hb-br-float, .hb-br-cube { animation:none !important; }
        .hb-br-flip, .hb-br-tilt, .hb-br-tab-bar { transition:none; }
    }
</style>
