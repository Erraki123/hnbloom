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

    // Smart Home = page interactive ; Hotel / Hospital = grille produits + recherche + pagination
    $isSmart = ($pk === 'home');

    // ===== Smart Home : visite immersive 360 (images dans public/images/smart-home/) =====
    if ($isSmart) {
        $hlBase  = 'images/smart-home/';
        $hlFiles = [
            'normal' => 'living-room-normal', 'open' => 'living-room-curtains-open', 'warm' => 'living-room-warm-light',
            'night'  => 'living-room-night',  'closed' => 'living-room-curtains-closed', 'party' => 'living-room-party',
        ];
        $map = collect($hlFiles)->map(function ($f) use ($hlBase) {
            foreach (['webp', 'png'] as $e) {
                if (file_exists(public_path($hlBase.$f.'.'.$e))) return asset($hlBase.$f.'.'.$e);
            }
            return asset($hlBase.$f.'.png');
        })->all();
        $music = file_exists(public_path('audio/smart-home/lounge.mp3')) ? asset('audio/smart-home/lounge.mp3') : null; // optionnel : sinon musique générée par le navigateur
    }
@endphp

<x-hb-layout :title="$market['title'].' — Domotique'" :description="$market['text']">
<div class="hb-smart-page">
@if ($isSmart)
{{-- Smart Home : uniquement la démo, plein écran --}}
<section class="hl360" id="hl360" tabindex="0" data-mood="normal" data-images='@json($map)' data-music="{{ $music }}" aria-label="Visite immersive du salon connecté">
    <div class="hl360__stage" id="hl360Stage">
        <canvas class="hl360__canvas" id="hl360Canvas" aria-label="Vue panoramique. Glissez pour regarder autour de vous."></canvas>
        <div class="hl360__fx hl360__fx--vignette"></div>
        <div class="hl360__fx hl360__fx--party" id="hl360FxParty"></div>
        <div class="hl360__fx hl360__fx--flash" id="hl360Flash"></div>
        <div class="hl360__fx hl360__fx--cold" id="hl360Cold"></div>
        <div class="hl360__fx hl360__fx--heat" id="hl360Heat"></div>
        <div class="hl360__bar hl360__bar--top"></div>
        <div class="hl360__bar hl360__bar--bottom"></div>

        <div class="hl360__loading" id="hl360Loading"><span class="hl360__spinner"></span><span id="hl360LoadText">Préparation de la visite…</span><span class="hl360__progress"><i id="hl360LoadBar"></i></span></div>
        <div class="hl360__error" id="hl360Error" hidden><strong>Panorama indisponible</strong><span>Vérifiez les images dans <code>public/images/smart-home/</code>.</span></div>

        <header class="hl360__header">
            <div class="hl360__brand"><span class="hl360__brand-mark">H</span><span><b>HN BLOOM</b><small>Maison connectée</small></span></div>
        </header>

        <div class="hl360__caption" id="hl360Caption"><h2 id="hl360Title">Jour</h2><p id="hl360Sub">Lumière naturelle, voilages tirés</p></div>
        <div class="hl360__hint" id="hl360Hint">Glissez pour regarder autour de vous · Utilisez le téléphone pour piloter la maison</div>

        <nav class="hl360__toolbar" aria-label="Commandes">
            <a class="hl360__tool hl360__tool--back" href="{{ route('domotique') }}" title="Retour aux univers">←</a>
            <button class="hl360__tool is-active" id="hl360Auto" type="button" aria-pressed="true" title="Rotation automatique">⟳</button>
            <button class="hl360__tool" id="hl360Motion" type="button" aria-pressed="false" title="Mouvement du téléphone (gyroscope)">⌖</button>
            <button class="hl360__tool" id="hl360Reset" type="button" title="Recentrer">↺</button>
            <button class="hl360__tool" id="hl360Fullscreen" type="button" title="Plein écran">⛶</button>
            <button class="hl360__tool hl360__tool--phone" id="hl360PhoneToggle" type="button" title="Afficher / masquer le téléphone">▯ Téléphone</button>
        </nav>

        {{-- Téléphone avec l'application SMART-G4 --}}
        <div class="hl360__phone" id="hl360Phone">
            <div class="ph__body">
                <div class="ph__btn ph__btn--a"></div><div class="ph__btn ph__btn--b"></div>
                <div class="ph__screen" id="app">
                    <div class="ph__notch"></div>
                    <div class="ap-status"><span id="apTime">9:41</span><span>▂▄▆ 5G ▮</span></div>
                    <div class="ap-head">
                        <button class="ap-back" id="apBack" type="button" aria-label="Retour" hidden>‹</button>
                        <div class="ap-title"><b id="apTitle">SMART-G4</b></div>
                        <span class="ap-gear">⚙</span>
                    </div>
                    <div class="ap-content" id="apContent"></div>
                    <div class="ap-nav"><span class="on">⌂<small>Home</small></span><span>▦<small>Control</small></span><span>⚙<small>Settings</small></span><span>☰<small>Main Setting</small></span></div>
                    <div class="ap-notif" id="apNotif"><b id="apNotifT"></b><small id="apNotifS"></small></div>
                    <div class="ap-snack" id="apSnack"></div>
                </div>
            </div>
        </div>
    </div>
    @if ($music)<audio id="hl360Audio" src="{{ $music }}" loop preload="none"></audio>@endif
</section>
@else
    @include('partials.hb-market-bg')

    <section class="hb-mk">
        <div class="hb-wrap">

            <a class="hb-mk-back" href="{{ route('domotique') }}"><x-icon name="arrow-left" :size="14" /> Retour aux univers</a>

            <p class="hb-mk-eyebrow">{{ $market['category'] }}</p>
            <h1>{{ $market['title'] }}</h1>
            <p class="hb-mk-lead">{{ $market['text'] }}</p>



            @unless ($isSmart)
                {{-- ============ Hotel / Hospital : produits + recherche + pagination ============ --}}
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
            @endunless

                <div class="hb-mk-cta">
                    <h3>Un projet {{ $market['title'] }} ?</h3>
                    <a class="hb-button hb-button-gold" href="{{ route('contact', ['sujet' => 'quote']) }}">
                        Demander une étude <x-icon name="arrow-up-right" :size="16" />
                    </a>
                </div>

            @include('partials.hb-dm-univers', ['current' => $market['slug']])
        </div>
    </section>
@endif
</div>

@unless ($isSmart)
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
@endunless

@if ($isSmart)
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
(function () {
    'use strict';
    const $ = (id) => document.getElementById(id);
    const root = $('hl360');
    if (!root || !window.THREE) return;
    const stage = $('hl360Stage'), canvas = $('hl360Canvas'), loading = $('hl360Loading'), error = $('hl360Error');
    const IMG = JSON.parse(root.dataset.images);
    const NAMES = {
        normal: ['Jour', 'Lumière naturelle, voilages tirés'], open: ['Panorama', 'Voilages ouverts sur la ville'],
        warm: ['Détente', 'Ambiance chaude de fin de journée'], night: ['Nuit', 'Skyline et éclairage indirect'],
        closed: ['Cinéma', 'Rideaux fermés, écran prêt'], party: ['Soirée', 'LED couleur et ambiance festive']
    };

    /* ---------- État de la maison (piloté par l'application) ---------- */
    const S = { mood: 'normal', curtains: false, rgb: null, pulse: false, media: false, tv: false, fire: false, light: true, bright: 100, warm: 0, ac: true, temp: 22, fan: 0, music: false, vol: 70,
        timeT: 0, wx: 'clear', lock: true, alarm: false, img: 'normal' };
    const LED = { party: -1, red: 0.0, amber: 0.09, green: 0.33, cyan: 0.5, blue: 0.62, violet: 0.76, pink: 0.9, white: 2 };
    /* Écran TV mesuré dans les panoramas 2688x1152 : [x0, vBas, x1, vHaut] en UV */
    const RECT = { party: [0.7539, 0.5602, 0.8172, 0.6742], std: [0.7545, 0.5205, 0.8176, 0.6320] };
    const rectOf = (k) => (k === 'party' ? RECT.party : RECT.std);
    const hueOf = (k) => (k === 'party' && S.rgb ? LED[S.rgb] : -1);
    const target = () => (S.rgb ? 'party' : S.curtains ? 'closed' : S.mood);

    /* ---------- WebGL : fondu cinématique entre panoramas ---------- */
    let renderer;
    try { renderer = new THREE.WebGLRenderer({ canvas, antialias: false, powerPreference: 'high-performance' }); }
    catch (e) { loading.hidden = true; error.hidden = false; return; }
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));   /* PERF : 1.5 max (au lieu de 2) */
    const scene = new THREE.Scene(), camera = new THREE.PerspectiveCamera(72, 1, 0.1, 1100);
    const geo = new THREE.SphereGeometry(500, 64, 40); geo.scale(-1, 1, 1); /* PERF : sphère plus légère */
    const blank = new THREE.DataTexture(new Uint8Array([20, 18, 15, 255]), 1, 1, THREE.RGBAFormat); blank.needsUpdate = true;
    const tvCanvas = document.createElement('canvas'); tvCanvas.width = 680; tvCanvas.height = 500;
    const tvCtx = tvCanvas.getContext('2d'), tvTex = new THREE.CanvasTexture(tvCanvas); tvTex.minFilter = THREE.LinearFilter; tvTex.generateMipmaps = false;
    const U = {
        tA: { value: blank }, tB: { value: blank }, tTv: { value: tvTex }, uMix: { value: 0 }, uSoft: { value: 0 }, uBright: { value: 1 }, uWarm: { value: 0 }, uGrain: { value: 0.03 }, uTime: { value: 0 },
        uHueA: { value: -1 }, uHueB: { value: -1 }, uPulse: { value: 0 }, uBeat: { value: 0 }, uTvOn: { value: 0 }, uTvLum: { value: 0.5 }, uFire: { value: 0 }, uFan: { value: 0 },
        uClA: { value: 0 }, uClB: { value: 0 }, uCloud: { value: 0 }, uRain: { value: 0 }, uFog: { value: 0 },
        rA: { value: new THREE.Vector4(...RECT.std) }, rB: { value: new THREE.Vector4(...RECT.std) }
    };
    scene.add(new THREE.Mesh(geo, new THREE.ShaderMaterial({
        uniforms: U,
        vertexShader: 'varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0); }',
        fragmentShader: [
            'precision highp float;',
            'uniform sampler2D tA; uniform sampler2D tB; uniform sampler2D tTv;',
            'uniform float uMix, uSoft, uBright, uWarm, uGrain, uTime, uHueA, uHueB, uPulse, uBeat, uTvOn, uTvLum, uFire, uFan, uClA, uClB, uCloud, uRain, uFog; uniform vec4 rA, rB; varying vec2 vUv;',
            'float h(vec2 p){ return fract(sin(dot(p, vec2(12.9898,78.233))) * 43758.5453); }',
            'vec3 rgb2hsv(vec3 c){ vec4 K=vec4(0.,-1./3.,2./3.,-1.); vec4 p=mix(vec4(c.bg,K.wz),vec4(c.gb,K.xy),step(c.b,c.g)); vec4 q=mix(vec4(p.xyw,c.r),vec4(c.r,p.yzx),step(p.x,c.r)); float d=q.x-min(q.w,q.y); return vec3(abs(q.z+(q.w-q.y)/(6.*d+1e-10)), d/(q.x+1e-10), q.x); }',
            'vec3 hsv2rgb(vec3 c){ vec4 K=vec4(1.,2./3.,1./3.,3.); vec3 p=abs(fract(c.xxx+K.xyz)*6.-K.www); return c.z*mix(K.xxx,clamp(p-K.xxx,0.,1.),c.y); }',
            'float winMask(){ return clamp(step(vUv.x,.087) + step(.916,vUv.x), 0., 1.) * step(.43,vUv.y) * step(vUv.y,.82); }',
            'vec3 led(vec3 col, float hue){',
            '  if(hue < -0.5) return col;',
            '  vec3 hsv = rgb2hsv(col);',
            '  float rng = smoothstep(.47,.54,hsv.x) * (1. - smoothstep(.95,.99,hsv.x));',
            '  float m = rng * smoothstep(.18,.45,hsv.y) * (1. - winMask());',
            '  vec3 t = hsv;',
            '  if(hue > 1.5){ t.y *= .1; t.z = min(1., t.z * 1.3); } else { t.x = hue; t.y = min(1., t.y * 1.12); }',
            '  vec3 o = mix(col, hsv2rgb(t), m);',
            '  return o * (1. + m * (uPulse * .35 * sin(uTime * 2.4) + uBeat * .7));',
            '}',
            'void main(){',
            '  float wave = vUv.x * 0.35 + h(floor(vUv * 90.0)) * 0.18;',
            '  float m = mix(smoothstep(0.0, 1.0, clamp(uMix * 1.55 - wave, 0.0, 1.0)), smoothstep(0.0, 1.0, uMix), uSoft);',
            '  float cl = mix(uClA, uClB, m);',
            /* ventilateur : les voilages ondulent */
            '  float sway = 0.;',
            '  if(uFan > 0.001){',
            '    float cm = (smoothstep(.085,.095,vUv.x) * (1. - smoothstep(.128,.134,vUv.x)) + smoothstep(.858,.864,vUv.x) * (1. - smoothstep(.910,.918,vUv.x))) * step(.40,vUv.y) * step(vUv.y,.80);',
            '    float cm2 = cl * clamp((1. - smoothstep(.128,.134,vUv.x)) + smoothstep(.864,.87,vUv.x), 0., 1.) * step(.36,vUv.y) * step(vUv.y,.82);',
            '    sway = (sin(vUv.y * 55. + uTime * 2.4 + vUv.x * 30.) * .0045 + sin(vUv.y * 23. - uTime * 1.4) * .0025) * uFan * max(cm, cm2);',
            '  }',
            '  vec2 suv = vUv + vec2(sway, 0.);',
            /* PERF : une seule texture lue quand il n'y a pas de transition */
            '  vec3 c = led(texture2D(tA, suv).rgb, uHueA);',
            '  if(m > 0.001){ vec3 b = led(texture2D(tB, suv).rgb, uHueB); c = mix(c, b, m); }',
            '  c *= 1.0 + 0.10 * sin(uMix * 3.14159) * (1. - uSoft);',
            /* météo derrière les vitres (calculée seulement si active) */
            '  if(uCloud + uRain + uFog > 0.001){',
            '    float wm = winMask() * (1. - cl); float lum = dot(c, vec3(.3,.59,.11)); vec3 grey = vec3(lum) * vec3(.82,.88,.98);',
            '    c = mix(c, grey * .72, wm * uCloud * .7); c = mix(c, grey * .5, wm * uRain * .85); c = mix(c, vec3(.78,.8,.82) * (.45 + lum * .6), wm * uFog * .88);',
            '    float cx = floor(vUv.x * 420.); float spd = 24. + h(vec2(cx, 1.)) * 20.;',
            '    float st = step(.965, h(vec2(cx, floor(vUv.y * 70. + uTime * spd + h(vec2(cx, 3.)) * 40.))));',
            '    c += vec3(.75,.85,1.) * st * wm * uRain * .35;',
            '    float lg = pow(max(0., sin(uTime * .63 + sin(uTime * .27) * 3.)), 70.) * uRain; c += vec3(.6,.7,1.) * lg * (wm * .9 + .05);',
            '    c *= 1. - uRain * .05;',
            '  }',
            '  c.r *= 1.0 + uWarm * 0.26; c.b *= 1.0 - uWarm * 0.34; c.g *= 1.0 + uWarm * 0.03;',
            '  c *= uBright * (1. + uBeat * .09 + uPulse * .08 * sin(uTime * 2.4));',
            '  if(uFire > 0.001){',
            '    vec2 fd = vec2(vUv.x - .796, (vUv.y - .5) * 1.5);',
            '    float fl = .72 + .28 * sin(uTime * 11.0) * sin(uTime * 7.3 + 1.0);',
            '    c += vec3(1., .46, .13) * exp(-dot(fd, fd) * 260.) * fl * uFire * .5;',
            '  }',
            '  if(uTvOn > 0.001){',
            '    vec4 r = mix(rA, rB, m); vec2 tuv = (vUv - r.xy) / (r.zw - r.xy);',
            '    float ins = step(0., tuv.x) * step(tuv.x, 1.) * step(0., tuv.y) * step(tuv.y, 1.);',
            '    vec3 tv = texture2D(tTv, clamp(tuv, 0., 1.)).rgb;',
            '    vec2 gd = (vUv - (r.xy + r.zw) * .5) * vec2(1., 1.6);',
            '    c += vec3(.95, .8, .5) * exp(-dot(gd, gd) * 110.) * uTvOn * (.10 + .10 * uTvLum);',
            '    c = mix(c, tv * 1.12, ins * uTvOn);',
            '  }',
            '  c += (h(gl_FragCoord.xy + uTime * 60.0) - 0.5) * uGrain;',
            '  gl_FragColor = vec4(c, 1.0);',
            '}'
        ].join('\n')
    })));
    camera.fov = 72; camera.updateProjectionMatrix();
    function resize() {
        const w = Math.max(1, stage.clientWidth), h = Math.max(1, stage.clientHeight);
        renderer.setSize(w, h, false); camera.aspect = w / h; camera.updateProjectionMatrix();
    }
    if ('ResizeObserver' in window) new ResizeObserver(resize).observe(stage); else window.addEventListener('resize', resize);

    /* PERF : on charge d'abord SEULEMENT l'image du jour (la scène s'affiche vite) ; les autres arrivent en arrière-plan */
    const tex = {}, pending = {}, loader = new THREE.TextureLoader(), aniso = Math.min(4, renderer.capabilities.getMaxAnisotropy());
    function loadOne(k) {
        if (tex[k]) return Promise.resolve(tex[k]);
        if (pending[k]) return pending[k];
        pending[k] = new Promise((res) => {
            loader.load(IMG[k], (t) => {
                t.minFilter = THREE.LinearFilter; t.generateMipmaps = false; t.anisotropy = aniso; tex[k] = t;
                try { renderer.initTexture(t); } catch (_) {}   /* envoi au GPU maintenant, pas au moment de la transition */
                res(t);
            }, undefined, () => res(null));
        });
        return pending[k];
    }

    /* ---------- Transitions, légendes, effets ---------- */
    let trans = null, capTimer;
    function fx() {
        $('hl360FxParty').classList.toggle('is-on', S.img === 'party');
        root.classList.toggle('is-cinema', S.media);
        root.dataset.mood = S.img;
    }
    function snap() {
        if (!U.uSoft.value) return;
        const k = S.img; U.tA.value = tex[k] || U.tA.value; U.tB.value = U.tA.value; U.uMix.value = 0; U.uSoft.value = 0;
        U.uClA.value = U.uClB.value = k === 'closed' ? 1 : 0; U.uHueA.value = U.uHueB.value = hueOf(k); U.rA.value.set(...rectOf(k)); U.rB.value.copy(U.rA.value);
    }
    function setImage(key, instant) {
        if (!tex[key]) { loadOne(key).then((t) => { if (t && target() === key) setImage(key, instant); }); return; }
        snap();
        if (key === S.img && !trans) { U.uHueA.value = U.uHueB.value = hueOf(key); return; }
        if (trans) { U.tA.value = U.tB.value; U.uHueA.value = U.uHueB.value; U.uClA.value = U.uClB.value; U.rA.value.copy(U.rB.value); U.uMix.value = 0; trans = null; }
        S.img = key; fx();
        U.uHueB.value = hueOf(key); U.uClB.value = key === 'closed' ? 1 : 0; U.rB.value.set(...rectOf(key));
        if (instant) { U.tA.value = tex[key]; U.uHueA.value = U.uHueB.value; U.uClA.value = U.uClB.value; U.rA.value.copy(U.rB.value); return; }
        U.tB.value = tex[key]; trans = { t0: performance.now(), dur: 1700 };
        flash();
        const c = $('hl360Caption'); c.classList.add('is-swap'); window.clearTimeout(capTimer);
        capTimer = window.setTimeout(() => { $('hl360Title').textContent = NAMES[key][0]; $('hl360Sub').textContent = NAMES[key][1]; c.classList.remove('is-swap'); }, 380);
    }
    function flash() { const f = $('hl360Flash'); f.classList.remove('go'); void f.offsetWidth; f.classList.add('go'); }
    /* Curseur "heure du jour" : fondu continu jour -> coucher de soleil -> nuit */
    function scrub(f) {
        const seq = ['normal', 'warm', 'night'];
        if (!seq.every((k) => tex[k])) { Promise.all(seq.map(loadOne)).then(() => scrub(S.timeT)); return; }
        const x = f * 2, i = Math.min(1, Math.floor(x)), fr = x - i;
        trans = null; U.tA.value = tex[seq[i]]; U.tB.value = tex[seq[i + 1]]; U.uMix.value = fr; U.uSoft.value = 1;
        U.uClA.value = U.uClB.value = 0; U.uHueA.value = U.uHueB.value = -1; U.rA.value.set(...RECT.std); U.rB.value.set(...RECT.std);
        S.mood = fr < 0.5 ? seq[i] : seq[i + 1]; S.img = S.mood; S.curtains = false; S.rgb = null; S.media = false; fx();
    }
    const clockStr = (f) => { const m = Math.round((12 + f * 10) * 60); return Math.floor(m / 60) + ':' + String(m % 60).padStart(2, '0'); };
    const phaseOf = (f) => (f < 0.25 ? 'Daytime' : f < 0.75 ? 'Sunset' : 'Night');

    /* ---------- Logo HN BLOOM animé sur la TV (texte seul) ---------- */
    const ease = (x) => (x < 0 ? 0 : x > 1 ? 1 : x * x * (3 - 2 * x));
    const seg = (t, a, b) => ease((t - a) / (b - a));
    function drawLogo(time) {
        const W = 680, H = 500, ctx = tvCtx, t = time % 8, out = 1 - seg(t, 7.2, 7.9), txt = 'HN BLOOM', size = 92;
        const bg = ctx.createRadialGradient(W / 2, H / 2, 20, W / 2, H / 2, 420); bg.addColorStop(0, '#1c170f'); bg.addColorStop(1, '#030302');
        ctx.globalAlpha = 1; ctx.shadowBlur = 0; ctx.fillStyle = bg; ctx.fillRect(0, 0, W, H);
        ctx.font = '700 ' + size + 'px Georgia, "Times New Roman", serif'; ctx.textBaseline = 'alphabetic'; ctx.textAlign = 'center';
        const sp = 12, ws = Array.from(txt).map((ch) => ctx.measureText(ch).width), tot = ws.reduce((a, b) => a + b, 0) + sp * (txt.length - 1), y = H / 2 + size * 0.3, x0 = (W - tot) / 2;
        const hg = ctx.createRadialGradient(W / 2, H / 2, 10, W / 2, H / 2, 320); hg.addColorStop(0, 'rgba(217,189,131,0.30)'); hg.addColorStop(1, 'rgba(217,189,131,0)');
        ctx.globalAlpha = out * seg(t, 0.3, 2.2) * (0.75 + 0.25 * Math.sin(time * 1.6)); ctx.fillStyle = hg; ctx.fillRect(0, 0, W, H);
        const sweep = seg(t, 3.2, 5.6) * (tot + 260) - 130; let x = x0;
        for (let i = 0; i < txt.length; i++) {
            const a = seg(t, 0.2 + i * 0.17, 0.95 + i * 0.17), cxm = x + ws[i] / 2, k = Math.max(0, 1 - Math.abs(cxm - (x0 + sweep)) / 95);
            ctx.save(); ctx.globalAlpha = out * a; ctx.translate(cxm, y + (1 - a) * 30); const sc = 0.82 + 0.18 * a; ctx.scale(sc, sc);
            ctx.fillStyle = 'rgb(' + Math.round(222 + 33 * k) + ',' + Math.round(188 + 60 * k) + ',' + Math.round(125 + 90 * k) + ')';
            ctx.shadowColor = 'rgba(240,205,140,' + (0.45 * a + 0.45 * k) + ')'; ctx.shadowBlur = 16 + 24 * k; ctx.fillText(txt[i], 0, 0); ctx.restore();
            x += ws[i] + sp;
        }
        ctx.shadowBlur = 0; ctx.globalAlpha = out; ctx.strokeStyle = 'rgba(232,201,140,0.85)'; ctx.lineWidth = 2;
        const lw = tot * seg(t, 2.4, 3.6); ctx.beginPath(); ctx.moveTo(W / 2 - lw / 2, y + 36); ctx.lineTo(W / 2 + lw / 2, y + 36); ctx.stroke();
        ctx.globalAlpha = 1; ctx.fillStyle = 'rgba(0,0,0,0.14)'; for (let yy = 0; yy < H; yy += 4) ctx.fillRect(0, yy, W, 1);
        tvTex.needsUpdate = true;
    }

    /* ---------- Audio réel (WebAudio) : musique, ventilateur, pluie ---------- */
    let ac, master, synthOut, analyser, reverb, noiseBuf, synthTimer = null, nextT = 0, stepN = 0, beatData = null;
    const amb = {}, audEl = $('hl360Audio');
    const mtof = (m) => 440 * Math.pow(2, (m - 69) / 12);
    function audio() {
        if (ac) { if (ac.state === 'suspended') ac.resume(); return ac; }
        const C = window.AudioContext || window.webkitAudioContext; if (!C) return null;
        ac = new C(); master = ac.createGain(); master.gain.value = S.vol / 100 * 0.8;
        analyser = ac.createAnalyser(); analyser.fftSize = 256; beatData = new Uint8Array(analyser.frequencyBinCount); master.connect(analyser); analyser.connect(ac.destination);
        const len = Math.floor(ac.sampleRate * 2.6), ir = ac.createBuffer(2, len, ac.sampleRate);
        for (let c = 0; c < 2; c++) { const d = ir.getChannelData(c); for (let i = 0; i < len; i++) d[i] = (Math.random() * 2 - 1) * Math.pow(1 - i / len, 2.6); }
        reverb = ac.createConvolver(); reverb.buffer = ir; const rg = ac.createGain(); rg.gain.value = 0.55; synthOut = ac.createGain(); synthOut.connect(master); reverb.connect(rg); rg.connect(synthOut);
        noiseBuf = ac.createBuffer(1, ac.sampleRate * 2, ac.sampleRate); const nd = noiseBuf.getChannelData(0); for (let i = 0; i < nd.length; i++) nd[i] = Math.random() * 2 - 1;
        if (ac.state === 'suspended') ac.resume();
        return ac;
    }
    function tone(f, t, dur, type, vol, lp, att) {
        const o = ac.createOscillator(), g = ac.createGain(), fl = ac.createBiquadFilter();
        o.type = type; o.frequency.value = f; fl.type = 'lowpass'; fl.frequency.value = lp;
        g.gain.setValueAtTime(0.0001, t); g.gain.linearRampToValueAtTime(vol, t + att); g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
        o.connect(fl); fl.connect(g); g.connect(synthOut); g.connect(reverb); o.start(t); o.stop(t + dur + 0.1);
    }
    function kick(t) {
        const o = ac.createOscillator(), g = ac.createGain(); o.frequency.setValueAtTime(130, t); o.frequency.exponentialRampToValueAtTime(42, t + 0.16);
        g.gain.setValueAtTime(0.55, t); g.gain.exponentialRampToValueAtTime(0.0001, t + 0.28); o.connect(g); g.connect(synthOut); o.start(t); o.stop(t + 0.3);
    }
    function hat(t) {
        const s = ac.createBufferSource(), f = ac.createBiquadFilter(), g = ac.createGain(); s.buffer = noiseBuf; f.type = 'highpass'; f.frequency.value = 7500;
        g.gain.setValueAtTime(0.05, t); g.gain.exponentialRampToValueAtTime(0.0001, t + 0.06); s.connect(f); f.connect(g); g.connect(synthOut); s.start(t, Math.random()); s.stop(t + 0.08);
    }
    const CH = [{ pad: [57, 60, 64, 67, 71], bass: 45 }, { pad: [53, 57, 60, 64, 67], bass: 41 }, { pad: [60, 64, 67, 71, 74], bass: 48 }, { pad: [55, 59, 62, 64, 69], bass: 43 }];
    const PAT = [0, 2, 4, 2, 3, 1, 4, 3];
    function playStep(n, t) {
        const k = n % 16, ch = CH[Math.floor(n / 16) % 4];
        if (k === 0) ch.pad.slice(0, 4).forEach((m) => tone(mtof(m), t, 6.8, 'triangle', 0.045, 1200, 1.2));
        if (k % 8 === 0) { tone(mtof(ch.bass), t, 1.7, 'sine', 0.3, 500, 0.02); kick(t); }
        if (k === 14) tone(mtof(ch.bass + 7), t, 0.9, 'sine', 0.16, 500, 0.02);
        if (k % 2 === 0) tone(mtof(ch.pad[PAT[(k / 2) % 8]] + 12), t, 1.2, 'sine', 0.06, 3200, 0.01);
        if (k % 2 === 1) hat(t);
    }
    function startSynth() {
        if (synthTimer) return; nextT = ac.currentTime + 0.1; stepN = 0;
        synthOut.gain.cancelScheduledValues(ac.currentTime); synthOut.gain.setTargetAtTime(1, ac.currentTime, 0.03);
        synthTimer = window.setInterval(() => { while (nextT < ac.currentTime + 0.5) { playStep(stepN, nextT); nextT += 60 / 76 / 2; stepN++; } }, 120);
    }
    function setMusic(on) {
        if (on) {
            if (!audio()) return;
            if (audEl && audEl.dataset.bad !== '1') {
                if (!audEl.dataset.wired) { try { ac.createMediaElementSource(audEl).connect(master); audEl.dataset.wired = '1'; } catch (_) {} }
                const pr = audEl.play();
                if (pr && pr.catch) pr.catch((err) => { if (err && err.name === 'AbortError') return; audEl.dataset.bad = '1'; if (S.music) startSynth(); });
            } else startSynth();
        } else {
            if (synthTimer) { window.clearInterval(synthTimer); synthTimer = null; }
            if (synthOut && ac) synthOut.gain.setTargetAtTime(0, ac.currentTime, 0.06);   /* coupe net, même les notes longues */
            if (audEl) audEl.pause();
        }
    }
    if (audEl) audEl.addEventListener('error', () => { audEl.dataset.bad = '1'; if (S.music && ac) startSynth(); });
    function ambient(kind, level) {
        if (!audio()) return;
        if (!amb[kind]) {
            const src = ac.createBufferSource(), f = ac.createBiquadFilter(), g = ac.createGain(); src.buffer = noiseBuf; src.loop = true; g.gain.value = 0;
            if (kind === 'fan') { f.type = 'bandpass'; f.frequency.value = 420; f.Q.value = 0.5; } else { f.type = 'highpass'; f.frequency.value = 2500; }
            src.connect(f); f.connect(g); g.connect(ac.destination); src.start(); amb[kind] = g;
        }
        amb[kind].gain.setTargetAtTime(level, ac.currentTime, 0.4);
    }
    let musicWas = false;
    function syncAudio() {
        if (S.music !== musicWas) { musicWas = S.music; setMusic(S.music); }
        if (S.fan > 0 || amb.fan) ambient('fan', S.fan * 0.02);
        if (S.wx === 'rain' || amb.rain) ambient('rain', S.wx === 'rain' ? 0.05 : 0);
    }
    function chime() { if (!audio()) return; const t = ac.currentTime; tone(880, t, 0.5, 'sine', 0.25, 4000, 0.01); tone(660, t + 0.22, 0.7, 'sine', 0.25, 4000, 0.01); }

    /* ---------- Caméra : on regarde autour de soi, sans zoom ---------- */
    const HOME = 180, FOV = 72, d2r = Math.PI / 180;
    let lon = HOME, lat = -4, tLon = HOME, tLat = -4, auto = true, dragging = false, px = 0, py = 0, last = performance.now();
    const clampLat = (v) => Math.max(-65, Math.min(65, v));
    function look(l, a) { tLon = lon + ((((l - lon) % 360) + 540) % 360 - 180); tLat = a; $('hl360Auto').classList.remove('is-active'); auto = false; }
    const CAM = { curtain: [20, 6], media: [284, 8], mood: [180, 14], rgbw: [180, 22], light: [180, 22], music: [119, 4], tv: [284, 8], fire: [284, 0], time: [20, 6], weather: [20, 6], fan: [180, 20] };
    function setAuto(on) { auto = on; $('hl360Auto').classList.toggle('is-active', on); $('hl360Auto').setAttribute('aria-pressed', String(on)); }
    $('hl360Auto').addEventListener('click', () => setAuto(!auto));
    $('hl360Reset').addEventListener('click', () => { tLon = lon + ((((HOME - lon) % 360) + 540) % 360 - 180); tLat = -4; setAuto(true); });
    $('hl360Fullscreen').addEventListener('click', async () => { try { if (!document.fullscreenElement) await stage.requestFullscreen(); else await document.exitFullscreen(); } catch (_) {} });
    document.addEventListener('fullscreenchange', resize);
    $('hl360PhoneToggle').addEventListener('click', () => root.classList.toggle('phone-hidden'));
    canvas.addEventListener('pointerdown', (e) => { if (e.pointerType === 'mouse' && e.button !== 0) return; dragging = true; setAuto(false); px = e.clientX; py = e.clientY; canvas.setPointerCapture(e.pointerId); $('hl360Hint').classList.add('is-hidden'); });
    canvas.addEventListener('pointermove', (e) => { if (!dragging) return; tLon -= (e.clientX - px) * 0.075; tLat = clampLat(tLat + (e.clientY - py) * 0.075); px = e.clientX; py = e.clientY; });
    ['pointerup', 'pointercancel', 'lostpointercapture'].forEach((n) => canvas.addEventListener(n, () => { dragging = false; }));
    root.addEventListener('keydown', (e) => {
        if (['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(e.key) === false || e.target.closest('#app')) return;
        e.preventDefault(); setAuto(false);
        if (e.key === 'ArrowLeft') tLon -= 6; if (e.key === 'ArrowRight') tLon += 6;
        if (e.key === 'ArrowUp') tLat = clampLat(tLat + 6); if (e.key === 'ArrowDown') tLat = clampLat(tLat - 6);
    });
    let gyro = false, g0 = null; const gb = $('hl360Motion');
    gb.addEventListener('click', async () => {
        if (gyro) { gyro = false; g0 = null; gb.classList.remove('is-active'); return; }
        if (!('DeviceOrientationEvent' in window)) return;
        try {
            if (typeof DeviceOrientationEvent.requestPermission === 'function' && (await DeviceOrientationEvent.requestPermission()) !== 'granted') return;
            gyro = true; setAuto(false); gb.classList.add('is-active');
        } catch (_) {}
    });
    window.addEventListener('deviceorientation', (e) => {
        if (!gyro || e.alpha == null) return;
        if (!g0) g0 = { a: e.alpha, b: e.beta, lon: tLon, lat: tLat };
        let da = e.alpha - g0.a; if (da > 180) da -= 360; if (da < -180) da += 360;
        tLon = g0.lon - da; tLat = clampLat(g0.lat + (e.beta - g0.b));
    });

    /* ---------- Application SMART-G4 (dans le téléphone) ---------- */
    const content = $('apContent'), title = $('apTitle'), back = $('apBack');
    let stack = ['spaces'], snackTimer;
    const TILES = [['light', 'Light', '☀', '#f5b72e'], ['rgbw', 'RGBW', '◉', '#5b5f7a'], ['ac', 'AC', '❄', '#3d8bff'], ['music', 'Music', '♫', '#8a5cf6'],
                   ['curtain', 'Curtain', '▥', '#e0a52b'], ['mood', 'Mood', '◎', '#ee5a7c'], ['fan', 'Fan', '✢', '#16b6c7'], ['tv', 'TV', '▭', '#6c7bff'],
                   ['fire', 'Fireplace', '♨', '#ff7a2f'], ['media', 'Cinema', '▶', '#2dbb78'], ['time', 'Time', '◔', '#f08a24'], ['weather', 'Weather', '☁', '#4aa3e8'],
                   ['energy', 'Energy', '⚡', '#e0b000'], ['security', 'Security', '⚿', '#d9485a']];
    const TITLES = { spaces: 'SMART-G4', zone: 'Home', controls: 'Salon_Zone', light: 'Light', rgbw: 'RGBW', ac: 'AC', music: 'Music', curtain: 'Curtain', mood: 'Mood', fan: 'Fan', tv: 'TV', fire: 'Fireplace', media: 'Cinema', time: 'Time of day', weather: 'Weather', energy: 'Energy', security: 'Security' };
    const COLORS = [['party', 'Party', 'conic-gradient(#4f6bff,#c43dff,#ff5aa5,#4f6bff)'], ['red', 'Red', '#ff3b3b'], ['green', 'Green', '#2dff7a'], ['blue', 'Blue', '#3d6bff'], ['violet', 'Violet', '#9b5cff'], ['pink', 'Pink', '#ff4fa3'], ['amber', 'Amber', '#ffb14f'], ['cyan', 'Cyan', '#35e6ff'], ['white', 'White', '#ffffff']];
    const isOn = (k) => ({ light: S.light, rgbw: !!S.rgb, ac: S.ac, music: S.music, curtain: S.curtains, mood: true, fan: S.fan > 0, tv: S.tv || S.media, fire: S.fire, media: S.media, time: false, weather: S.wx !== 'clear', energy: false, security: S.alarm })[k];
    const sw = (on, act) => '<button class="ap-sw' + (on ? ' on' : '') + '" data-act="' + act + '" aria-label="Interrupteur"><i></i></button>';
    const loads = () => [['Lights', S.light ? Math.round(60 * S.bright / 100) : 0, '#f5b72e'], ['AC', S.ac ? Math.round(450 + Math.abs(S.temp - 22) * 120) : 0, '#3d8bff'], ['TV', (S.tv || S.media) ? 120 : 0, '#6c7bff'],
                         ['LED', S.rgb ? 35 : 0, '#c43dff'], ['Audio', S.music ? 30 : 0, '#8a5cf6'], ['Fan', [0, 20, 40, 65][S.fan], '#16b6c7']];

    function viewHTML(v) {
        if (v === 'spaces') return '<p class="ap-lead">Select Your Space</p>' +
            '<button class="ap-space" data-act="go:zone" style="--bg:url(' + IMG.night + ')"><b>DemoArea</b><small>6 devices</small></button>' +
            '<button class="ap-space" data-act="go:zone" style="--bg:url(' + IMG.normal + ')"><b>Home</b><small>6 devices</small></button>' +
            '<div class="ap-fab">+</div>';
        if (v === 'zone') return '<p class="ap-lead">Home</p><div class="ap-rooms">' +
            '<button class="ap-room" data-act="go:controls" style="--bg:url(' + IMG.normal + ')"><b>Salon</b></button>' +
            '<button class="ap-room" data-act="go:controls" style="--bg:url(' + IMG.warm + ')"><b>Lounge</b></button></div>';
        if (v === 'controls') return '<div class="ap-grid">' + TILES.map((t) =>
            '<button class="ap-tile' + (isOn(t[0]) ? ' on' : '') + '" data-act="go:' + t[0] + '"><span class="ap-ico" style="--c:' + t[3] + '">' + t[2] + '</span><b>' + t[1] + '</b></button>').join('') + '</div>';
        if (v === 'light') return '<div class="ap-card"><div class="ap-row"><b>Main lights</b>' + sw(S.light, 'light') + '</div>' +
            '<label>Brightness <output id="oBr">' + S.bright + '%</output></label><input type="range" class="ap-rng" data-rng="bright" min="40" max="120" value="' + S.bright + '">' +
            '<label>Colour temperature <output id="oWm">' + (S.warm > 12 ? 'Warm' : S.warm < -12 ? 'Cool' : 'Neutral') + '</output></label><input type="range" class="ap-rng ap-rng--t" data-rng="warm" min="-60" max="60" value="' + S.warm + '"></div>';
        if (v === 'rgbw') return '<div class="ap-card"><div class="ap-row"><b>LED strip</b>' + sw(!!S.rgb, S.rgb ? 'rgb:off' : 'rgb:party') + '</div><div class="ap-colors">' +
            COLORS.map((c) => '<button class="ap-col' + (S.rgb === c[0] ? ' on' : '') + '" data-act="rgb:' + c[0] + '"><i style="background:' + c[2] + '"></i><small>' + c[1] + '</small></button>').join('') + '</div>' +
            '<div class="ap-row ap-row--sp"><b>Breathing effect</b>' + sw(S.pulse, 'pulse') + '</div><small class="ap-note">RGBW = Red, Green, Blue + White LEDs. Pick one colour to light the room in it. With music on, the LEDs pulse to the beat.</small></div>';
        if (v === 'ac') return '<div class="ap-card ap-center"><div class="ap-temp">' + String(S.temp).replace('.', ',') + '<sup>°C</sup></div><div class="ap-mode">' + (!S.ac ? 'Off' : S.temp < 21 ? '❄ Cooling' : S.temp > 23 ? '☀ Heating' : 'Comfort') + '</div><div class="ap-step"><button data-act="t:-0.5">−</button><button data-act="t:0.5">+</button></div>' +
            '<div class="ap-row"><b>Power · Auto</b>' + sw(S.ac, 'ac') + '</div><small class="ap-note">The room colour follows the temperature: cooler = blue tone, warmer = amber tone.</small></div>';
        if (v === 'music') return '<div class="ap-card ap-center"><div class="ap-art' + (S.music ? ' play' : '') + '"><span></span><span></span><span></span><span></span><span></span></div><b>Lounge Evening</b><small>Multiroom · Salon</small>' +
            '<button class="ap-play" data-act="music">' + (S.music ? '❚❚' : '▶') + '</button>' +
            '<label class="ap-vol">Volume <output id="oVol">' + S.vol + '%</output></label><input type="range" class="ap-rng" data-rng="vol" min="0" max="100" value="' + S.vol + '"></div>';
        if (v === 'curtain') return '<div class="ap-card ap-center"><div class="ap-seg"><button class="' + (!S.curtains ? 'on' : '') + '" data-act="cur:open">Open</button><button class="' + (S.curtains ? 'on' : '') + '" data-act="cur:close">Close</button></div><small class="ap-note">Motorised curtains · the view updates live.</small></div>';
        if (v === 'mood') return '<div class="ap-moods">' + [['normal', 'Day'], ['open', 'View'], ['warm', 'Relax'], ['night', 'Night']].map((m) =>
            '<button class="ap-mood' + (!S.rgb && !S.curtains && S.mood === m[0] ? ' on' : '') + '" data-act="mood:' + m[0] + '" style="--bg:url(' + IMG[m[0]] + ')"><b>' + m[1] + '</b></button>').join('') + '</div>' +
            '<p class="ap-lead">Quick scenes</p><div class="ap-chips"><button data-act="scn:morning">Good morning</button><button data-act="scn:night">Good night</button><button data-act="scn:leave">Leave home</button></div>';
        if (v === 'fan') return '<div class="ap-card ap-center"><div class="ap-fan s' + S.fan + '">✢</div><div class="ap-seg">' + [0, 1, 2, 3].map((n) => '<button class="' + (S.fan === n ? 'on' : '') + '" data-act="fan:' + n + '">' + (n ? n : 'Off') + '</button>').join('') + '</div>' +
            '<small class="ap-note">Ceiling fan, speed 1 to 3. In the room the sheer curtains sway more with each speed, and you hear the airflow.</small></div>';
        if (v === 'tv') return '<div class="ap-card"><div class="ap-row"><b>TV screen</b>' + sw(S.tv || S.media, 'tv') + '</div><small class="ap-note">Shows the HN BLOOM logo animation on the living-room TV.</small></div>';
        if (v === 'fire') return '<div class="ap-card"><div class="ap-row"><b>Fireplace glow</b>' + sw(S.fire, 'fire') + '</div><small class="ap-note">Adds a warm flickering light around the fireplace.</small></div>';
        if (v === 'media') return '<div class="ap-card"><div class="ap-row"><b>Cinema mode</b>' + sw(S.media, 'media') + '</div><small class="ap-note">Closes the curtains, dims the room, adds cinema bars and starts the TV.</small></div>';
        if (v === 'time') return '<div class="ap-card ap-center"><div class="ap-clock" id="oClock">' + clockStr(S.timeT) + '</div><div class="ap-phase" id="oPhase">' + phaseOf(S.timeT) + '</div>' +
            '<input type="range" class="ap-rng ap-rng--day" data-rng="timeT" min="0" max="100" value="' + Math.round(S.timeT * 100) + '"><div class="ap-ends"><span>Day</span><span>Sunset</span><span>Night</span></div>' +
            '<small class="ap-note">Slide to move the sun. The room follows the time of day, live.</small></div>';
        if (v === 'weather') return '<div class="ap-card"><div class="ap-wx">' + [['clear', '☀', 'Clear'], ['cloudy', '☁', 'Cloudy'], ['rain', '☂', 'Rain'], ['fog', '≋', 'Fog']].map((w) =>
            '<button class="' + (S.wx === w[0] ? 'on' : '') + '" data-act="wx:' + w[0] + '"><span>' + w[1] + '</span>' + w[2] + '</button>').join('') + '</div><small class="ap-note">Simulated weather outside, visible through the windows when the curtains are open. Rain comes with sound and lightning.</small></div>';
        if (v === 'energy') { const L = loads(), tot = L.reduce((a, l) => a + l[1], 0);
            return '<div class="ap-card"><div class="ap-big">' + tot + '<small> W</small></div><small class="ap-note">Live consumption of the salon</small>' +
            L.map((l) => '<div class="ap-bar"><span>' + l[0] + '</span><i style="--w:' + Math.min(100, l[1] / 6) + '%;--c:' + l[2] + '"></i><em>' + l[1] + ' W</em></div>').join('') + '</div>'; }
        if (v === 'security') return '<div class="ap-cam"><span class="ap-rec">● REC</span><span class="ap-camname">Front door</span></div><div class="ap-card"><div class="ap-row"><b>Front door lock</b>' + sw(S.lock, 'lock') + '</div>' +
            '<div class="ap-row ap-row--sp"><b>Alarm armed</b>' + sw(S.alarm, 'alarm') + '</div><div class="ap-row ap-row--sp"><button class="ap-test" data-act="visitor">Simulate a visitor</button></div></div>';
        return '';
    }
    function render(anim) {
        const v = stack[stack.length - 1];
        title.textContent = TITLES[v]; back.hidden = stack.length < 2;
        content.className = 'ap-content' + (anim ? ' in' : '');
        content.innerHTML = viewHTML(v);
    }
    function climate() {
        const k = S.ac ? (S.temp - 22) / 5 : 0;
        $('hl360Cold').style.opacity = Math.max(0, -k) * 0.85; $('hl360Heat').style.opacity = Math.max(0, k) * 0.7;
    }
    function snack(msg) {
        if (!msg) return; const s = $('apSnack'); s.textContent = msg; s.classList.add('show');
        window.clearTimeout(snackTimer); snackTimer = window.setTimeout(() => s.classList.remove('show'), 1800);
    }
    function apply(msg, cam) {
        if (S.media) S.curtains = true;
        setImage(target()); fx();
        root.style.setProperty('--glow', S.rgb && S.img === 'party' ? $('app').dataset.glow : '');
        climate(); syncAudio();
        if (cam && CAM[cam]) look(CAM[cam][0], CAM[cam][1]);
        render(false); snack(msg);
    }
    function notif(t, sub) {
        $('apNotifT').textContent = t; $('apNotifS').textContent = sub; const n = $('apNotif'); n.classList.add('show');
        window.setTimeout(() => n.classList.remove('show'), 3800);
    }
    content.addEventListener('click', (e) => {
        const b = e.target.closest('[data-act]'); if (!b) return;
        b.classList.add('tap'); window.setTimeout(() => b.classList.remove('tap'), 300);
        const [a, val] = b.dataset.act.split(':');
        if (a === 'go') { stack.push(val); render(true); if (CAM[val]) look(CAM[val][0], CAM[val][1]); return; }
        if (a === 'light') { S.light = !S.light; apply('Lights ' + (S.light ? 'on' : 'off')); }
        else if (a === 'rgb') {
            S.rgb = val === 'off' ? null : val;
            if (S.rgb) { S.curtains = false; S.media = false; const c0 = COLORS.filter((c) => c[0] === val)[0]; $('app').dataset.glow = (val === 'party' || val === 'white') ? '#c43dffcc' : c0[2] + 'cc'; flash(); }
            apply(S.rgb ? 'LED · ' + (val === 'party' ? 'party colours' : val) : 'LED off', 'rgbw');
        }
        else if (a === 'pulse') { S.pulse = !S.pulse; apply('Breathing ' + (S.pulse ? 'on' : 'off')); }
        else if (a === 'ac') { S.ac = !S.ac; apply('AC ' + (S.ac ? 'on' : 'off')); }
        else if (a === 't') { S.temp = Math.max(17, Math.min(27, S.temp + Number(val))); S.ac = true; apply('AC · ' + S.temp + '°C'); }
        else if (a === 'music') { S.music = !S.music; apply(S.music ? 'Playing · Lounge Evening' : 'Music paused', 'music'); }
        else if (a === 'cur') { const hadRgb = !!S.rgb; S.curtains = val === 'close'; if (S.curtains) S.rgb = null; if (!S.curtains) S.media = false; apply((S.curtains && hadRgb ? 'LED off · ' : '') + 'Curtains ' + (S.curtains ? 'closing' : 'opening'), 'curtain'); }
        else if (a === 'mood') { S.mood = val; S.timeT = { normal: 0, open: 0.1, warm: 0.5, night: 1 }[val]; S.rgb = null; S.curtains = false; S.media = false; S.tv = false; S.light = true; S.warm = val === 'warm' ? 25 : val === 'night' ? 10 : 0; S.bright = val === 'open' ? 105 : 100; apply('Mood · ' + NAMES[val][0], 'mood'); }
        else if (a === 'fan') { S.fan = Number(val); apply(S.fan ? 'Fan speed ' + S.fan : 'Fan off', 'fan'); }
        else if (a === 'tv') { const on = !(S.tv || S.media); S.tv = on; if (!on) S.media = false; apply(on ? 'TV on · HN BLOOM' : 'TV off', 'tv'); }
        else if (a === 'fire') { S.fire = !S.fire; apply('Fireplace ' + (S.fire ? 'lit' : 'off'), 'fire'); }
        else if (a === 'media') { S.media = !S.media; if (S.media) S.rgb = null; if (!S.media) S.curtains = false; apply(S.media ? 'Cinema mode on' : 'Cinema mode off', 'media'); }
        else if (a === 'wx') { S.wx = val; if (S.curtains) { S.curtains = false; S.media = false; } apply('Weather · ' + val, 'weather'); }
        else if (a === 'lock') { S.lock = !S.lock; apply('Front door ' + (S.lock ? 'locked' : 'unlocked')); }
        else if (a === 'alarm') { S.alarm = !S.alarm; apply('Alarm ' + (S.alarm ? 'armed' : 'disarmed')); }
        else if (a === 'visitor') {
            chime(); notif('Someone is at the front door', 'Front door camera · now');
            if (S.alarm) { root.classList.add('is-alert'); window.setTimeout(() => root.classList.remove('is-alert'), 4000); }
        }
        else if (a === 'scn') {
            if (val === 'morning') Object.assign(S, { mood: 'open', timeT: 0.1, curtains: false, rgb: null, media: false, tv: false, light: true, bright: 105, warm: 0, ac: true, temp: 22, music: false, fire: false });
            if (val === 'night') Object.assign(S, { curtains: true, rgb: null, media: false, tv: false, light: true, bright: 60, warm: 20, ac: true, temp: 20, music: false, fire: true, lock: true, alarm: true });
            if (val === 'leave') Object.assign(S, { curtains: true, rgb: null, media: false, tv: false, light: false, ac: false, music: false, fire: false, fan: 0, lock: true, alarm: true });
            apply({ morning: 'Good morning', night: 'Good night', leave: 'Leave home · all off' }[val], 'mood');
        }
    });
    content.addEventListener('input', (e) => {
        const k = e.target.dataset.rng; if (!k) return;
        const v = Number(e.target.value);
        if (k === 'bright') { S.bright = v; $('oBr').value = v + '%'; }
        else if (k === 'warm') { S.warm = v; $('oWm').value = v > 12 ? 'Warm' : v < -12 ? 'Cool' : 'Neutral'; }
        else if (k === 'vol') { S.vol = v; $('oVol').value = v + '%'; if (master) master.gain.value = v / 100 * 0.8; }
        else if (k === 'timeT') { S.timeT = v / 100; scrub(S.timeT); $('oClock').textContent = clockStr(S.timeT); $('oPhase').textContent = phaseOf(S.timeT); }
    });
    content.addEventListener('change', (e) => { if (e.target.dataset.rng === 'timeT') snack('Time · ' + clockStr(S.timeT)); });
    back.addEventListener('click', () => { if (stack.length > 1) { stack.pop(); render(true); } });
    const navs = $('app').querySelectorAll('.ap-nav span');
    navs[0].addEventListener('click', () => { stack = ['spaces']; render(true); });
    navs[1].addEventListener('click', () => { stack = ['spaces', 'zone', 'controls']; render(true); });
    navs[1].style.cursor = navs[2].style.cursor = navs[3].style.cursor = 'pointer';
    navs[2].addEventListener('click', () => snack('Settings · disponible dans l’application complète'));
    navs[3].addEventListener('click', () => snack('Main Setting · disponible dans l’application complète'));
    const clock = () => { const d = new Date(); $('apTime').textContent = d.getHours() + ':' + String(d.getMinutes()).padStart(2, '0'); };
    clock(); window.setInterval(clock, 20000);

    /* ---------- Boucle de rendu ---------- */
    const cur = { b: 1, w: 0 }, lerp = (a, b, t) => a + (b - a) * t;
    let visible = true, tvLast = 0;
    /* PERF : on ne dessine rien quand la scène est hors de l'écran ou l'onglet est caché */
    if ('IntersectionObserver' in window) new IntersectionObserver((es) => { visible = es[0].isIntersecting; }, { threshold: 0 }).observe(stage);
    function animate(now) {
        requestAnimationFrame(animate);
        if (!visible || document.hidden) { last = now; return; }
        const dt = Math.min(0.05, (now - last) / 1000); last = now;
        if (auto && !dragging && !gyro) tLon += dt * 2.1;
        lon = lerp(lon, tLon, 0.07); lat = lerp(lat, tLat, 0.07);
        if (trans) {
            const p = Math.min(1, (now - trans.t0) / trans.dur);
            U.uMix.value = p < 0.5 ? 2 * p * p : 1 - Math.pow(-2 * p + 2, 2) / 2;
            if (p >= 1) { U.tA.value = U.tB.value; U.uHueA.value = U.uHueB.value; U.uClA.value = U.uClB.value; U.rA.value.copy(U.rB.value); U.uMix.value = 0; trans = null; }
        }
        cur.b = lerp(cur.b, (S.light ? S.bright / 100 : 0.3) * (S.media ? 0.85 : 1), 0.06);
        cur.w = lerp(cur.w, S.warm / 100 + (S.ac ? (S.temp - 22) / 12 : 0), 0.06);
        U.uBright.value = cur.b; U.uWarm.value = cur.w; U.uTime.value = (now / 1000) % 100;
        U.uPulse.value = lerp(U.uPulse.value, S.pulse ? 1 : 0, 0.08); U.uFire.value = lerp(U.uFire.value, S.fire ? 1 : 0, 0.05);
        U.uFan.value = lerp(U.uFan.value, S.fan / 3, 0.05);
        U.uCloud.value = lerp(U.uCloud.value, S.wx === 'cloudy' ? 1 : 0, 0.04); U.uRain.value = lerp(U.uRain.value, S.wx === 'rain' ? 1 : 0, 0.04); U.uFog.value = lerp(U.uFog.value, S.wx === 'fog' ? 1 : 0, 0.04);
        let beat = 0;
        if (analyser && S.music) { analyser.getByteFrequencyData(beatData); beat = (beatData[1] + beatData[2] + beatData[3] + beatData[4]) / 1020; beat = Math.max(0, (beat - 0.35) * 1.8); }
        U.uBeat.value = lerp(U.uBeat.value, beat, 0.3);
        U.uTvOn.value = lerp(U.uTvOn.value, (S.tv || S.media) ? 1 : 0, 0.07);
        if (U.uTvOn.value > 0.01) {
            if (now - tvLast > 40) { tvLast = now; drawLogo(now / 1000); }   /* PERF : logo TV à 25 images/s max */
            U.uTvLum.value = 0.5 + 0.5 * Math.sin(now / 700);
        }
        const phi = (90 - lat) * d2r, th = lon * d2r;
        camera.lookAt(Math.sin(phi) * Math.cos(th), Math.cos(phi), Math.sin(phi) * Math.sin(th));
        renderer.render(scene, camera);
    }
    $('hl360LoadBar').style.width = '60%';
    loadOne('normal').then((t) => {
        if (!t) { loading.hidden = true; error.hidden = false; return; }
        $('hl360LoadBar').style.width = '100%';
        U.tA.value = t; U.tB.value = t; S.img = 'normal'; fx();
        loading.classList.add('is-done'); window.setTimeout(() => { loading.hidden = true; }, 600);
        window.setTimeout(() => root.classList.add('phone-in'), 400);
        /* les 5 autres panoramas arrivent ensuite, un par un, sans bloquer la page */
        window.setTimeout(() => {
            ['open', 'warm', 'night', 'closed', 'party'].reduce((p, k) => p.then(() => loadOne(k)), Promise.resolve());
        }, 1500);
    });
    render(false); climate(); resize(); requestAnimationFrame(animate);
})();
</script>
<style>
    .hl360{--gold:#d9bd83;--glow:#f3e2b8aa;margin:28px 0;font-family:Inter,"Helvetica Neue",Arial,sans-serif;color:#f4f1e9;outline:none}
    .hl360 *{box-sizing:border-box}
    .hl360[data-mood=open]{--glow:#9fd0ffaa}.hl360[data-mood=warm]{--glow:#ffb15faa}.hl360[data-mood=night]{--glow:#ffb86a77}.hl360[data-mood=closed]{--glow:#ffc98a77}.hl360[data-mood=party]{--glow:#c43dffcc}
    .hl360__stage{position:relative;isolation:isolate;width:100%;height:clamp(700px,86vh,920px);overflow:hidden;background:#171612;border:1px solid rgba(217,189,131,.32);border-radius:20px}
    .hl360__stage:fullscreen{width:100vw;height:100vh;border:0;border-radius:0}
    .hl360__canvas{position:absolute;inset:0;width:100%;height:100%;display:block;cursor:grab;touch-action:none}.hl360__canvas:active{cursor:grabbing}

    .hl360__fx{position:absolute;inset:0;z-index:1;pointer-events:none}
    .hl360__fx--vignette{background:radial-gradient(ellipse at center,transparent 52%,rgba(5,4,3,.55) 100%),linear-gradient(180deg,rgba(0,0,0,.4),transparent 22%,transparent 70%,rgba(0,0,0,.45))}
    /* PERF : plus d'animation hue-rotate ni de mix-blend-mode sur toute la scène (très coûteux) */
    .hl360__fx--party{opacity:0;transition:opacity 1.2s;background:conic-gradient(from 0deg at 50% 60%,#4f6bff,#c43dff,#ff5aa5,#ffb14f,#4f6bff)}
    .hl360__fx--party.is-on{opacity:.22}
    .hl360__fx--flash{opacity:0;background:radial-gradient(circle at 50% 45%,rgba(255,226,170,.5),transparent 60%)}
    .hl360__fx--flash.go{animation:hl360flash 1.7s ease-out}
    @keyframes hl360flash{0%{opacity:0}18%{opacity:.8}100%{opacity:0}}
    .hl360__fx--cold{opacity:0;transition:opacity 1.2s;background:radial-gradient(ellipse at center,transparent 38%,rgba(150,205,255,.5) 100%)}
    .hl360__fx--heat{opacity:0;transition:opacity 1.2s;background:linear-gradient(0deg,rgba(255,150,60,.45),transparent 55%)}
    .hl360__bar{position:absolute;z-index:2;left:0;right:0;height:0;background:#000;pointer-events:none;transition:height .9s cubic-bezier(.65,0,.35,1)}
    .hl360__bar--top{top:0}.hl360__bar--bottom{bottom:0}.hl360.is-cinema .hl360__bar{height:clamp(30px,7%,70px)}

    .hl360__header{position:absolute;z-index:6;top:24px;left:26px;pointer-events:none}
    .hl360__brand{display:flex;align-items:center;gap:11px;text-shadow:0 2px 10px #0008}
    .hl360__brand-mark{display:grid;place-items:center;width:36px;height:36px;border:1px solid #d9bd83a6;border-radius:50%;font:italic 20px Georgia,serif;color:var(--gold)}
    .hl360__brand b{display:block;font:600 10px Inter,Arial,sans-serif;letter-spacing:2px}.hl360__brand small{display:block;margin-top:4px;color:#d8cba9;font-size:10px}
    .hl360__caption{position:absolute;z-index:4;left:30px;bottom:90px;text-shadow:0 3px 22px #000a;pointer-events:none;transition:opacity .38s,transform .38s}
    .hl360__caption.is-swap{opacity:0;transform:translateY(8px)}
    .hl360__caption h2{margin:0 0 4px;font:400 clamp(34px,4.4vw,56px)/1.05 Georgia,serif;letter-spacing:-.8px}.hl360__caption p{margin:0;font-size:13px;color:#eee9df}
    /* PERF : backdrop-filter supprimé (flou en temps réel sur un canvas WebGL = très lent) */
    .hl360__hint{position:absolute;z-index:5;left:30px;bottom:34px;padding:9px 16px;border:1px solid #ffffff28;border-radius:40px;background:#161511d9;font-size:11px;pointer-events:none;transition:opacity .35s;max-width:calc(100% - 340px)}
    .hl360__hint.is-hidden{opacity:0}

    .hl360__loading{position:absolute;inset:0;z-index:20;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;background:#171612;color:#e5dbc4;font-size:12px;transition:opacity .6s}
    .hl360__loading.is-done{opacity:0}.hl360__loading[hidden]{display:none}
    .hl360__spinner{width:32px;height:32px;border:1px solid #74694f;border-top-color:#ead4a0;border-radius:50%;animation:hl360spin .8s linear infinite}@keyframes hl360spin{to{transform:rotate(360deg)}}
    .hl360__progress{width:180px;height:2px;background:#ffffff1c;border-radius:2px;overflow:hidden}.hl360__progress i{display:block;height:100%;width:0;background:var(--gold);transition:width .3s}
    .hl360__error{position:absolute;z-index:21;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:9px;padding:30px;background:#171612;text-align:center}.hl360__error[hidden]{display:none}.hl360__error strong{font:24px Georgia,serif;color:var(--gold)}.hl360__error span{font-size:12px;color:#d6d0c1}

    .hl360__toolbar{position:absolute;z-index:8;top:22px;right:26px;display:flex;gap:4px;padding:5px;border:1px solid #ffffff25;border-radius:14px;background:#11110fe6}
    .hl360__tool{min-width:36px;height:36px;padding:0 10px;border:1px solid transparent;border-radius:10px;background:transparent;color:#e6d7b4;font:500 16px Inter,Arial,sans-serif;cursor:pointer;transition:.2s}
    .hl360__tool:hover,.hl360__tool.is-active{border-color:#d9bd8350;background:#d9bd831e;color:#f3dba7}.hl360__tool--phone{font-size:11px}

    /* téléphone */
    .hl360__phone{position:absolute;z-index:7;right:42px;top:50%;width:286px;height:590px;margin-top:-295px;transform:translateX(130%) rotate(4deg);opacity:0;transition:transform 1.1s cubic-bezier(.2,.8,.2,1),opacity .8s;will-change:transform}
    .hl360.phone-in .hl360__phone{transform:none;opacity:1}
    .hl360.phone-hidden .hl360__phone{transform:translateX(130%) rotate(4deg);opacity:0;pointer-events:none}
    .ph__body{position:relative;width:100%;height:100%;padding:9px;border-radius:44px;background:linear-gradient(145deg,#3a3a3f,#0b0b0d 40%,#2a2a2e);border:1px solid #55555c;box-shadow:0 0 50px var(--glow),0 24px 50px #000a,inset 0 0 0 2px #0008;transition:box-shadow 1.2s}
    .ph__btn{position:absolute;width:3px;background:#2d2d31;border-radius:2px}.ph__btn--a{left:-3px;top:110px;height:44px}.ph__btn--b{right:-3px;top:150px;height:64px}
    .ph__screen{position:relative;width:100%;height:100%;overflow:hidden;border-radius:36px;background:linear-gradient(180deg,#e7ebff,#f1eeff 45%,#e6f5fa);color:#1b2142;display:flex;flex-direction:column;font:12px/1.35 Inter,Arial,sans-serif}
    .ph__screen:after{content:"";position:absolute;inset:0;pointer-events:none;background:linear-gradient(115deg,rgba(255,255,255,.28),transparent 28%);border-radius:inherit}
    .ph__notch{position:absolute;z-index:5;top:8px;left:50%;width:78px;height:20px;margin-left:-39px;border-radius:14px;background:#050506}
    .ap-status{display:flex;justify-content:space-between;padding:10px 22px 0;font-size:10px;font-weight:600}
    .ap-head{display:flex;align-items:center;gap:8px;padding:14px 16px 6px}.ap-title{flex:1;font-size:16px}.ap-gear{color:#6b7194}
    .ap-back{width:30px;height:30px;border:0;border-radius:50%;background:#fff;box-shadow:0 2px 8px #4b55a022;font-size:20px;color:#1b2142;cursor:pointer}.ap-back[hidden]{display:none}
    .ap-content{flex:1;overflow-y:auto;padding:6px 14px 10px;scrollbar-width:none}.ap-content::-webkit-scrollbar{display:none}
    .ap-content.in{animation:apin .35s ease}@keyframes apin{from{opacity:0;transform:translateX(18px)}}
    .ap-lead{margin:6px 2px 10px;font-weight:600;color:#3b4270}
    .ap-space,.ap-room,.ap-mood{position:relative;display:block;width:100%;height:96px;margin-bottom:10px;padding:10px 12px;border:0;border-radius:14px;background:linear-gradient(0deg,#000a,#0000 70%),var(--bg) center/cover;color:#fff;text-align:left;cursor:pointer;box-shadow:0 6px 16px #3a438033;transition:transform .2s}
    .ap-space b,.ap-room b,.ap-mood b{position:absolute;left:12px;bottom:22px;font-size:13px}.ap-space small{position:absolute;left:12px;bottom:8px;font-size:10px;opacity:.85}
    .ap-room b,.ap-mood b{bottom:10px}
    .ap-rooms{display:grid;grid-template-columns:1fr 1fr;gap:10px}.ap-room{height:130px;margin:0}
    .ap-moods{display:grid;grid-template-columns:1fr 1fr;gap:10px}.ap-mood{height:104px;margin:0}.ap-mood.on{outline:3px solid #12b5cb;outline-offset:-3px}
    .ap-fab{position:absolute;right:18px;bottom:62px;width:40px;height:40px;border-radius:50%;background:#12b5cb;color:#fff;display:grid;place-items:center;font-size:22px;box-shadow:0 6px 16px #12b5cb66}
    .ap-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .ap-tile{display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 6px;border:1px solid transparent;border-radius:16px;background:#fff;box-shadow:0 4px 14px #4b55a01c;cursor:pointer;transition:.2s;color:#1b2142}
    .ap-tile.on{border-color:#12b5cb55;background:#f2fdff}.ap-tile b{font-weight:600;font-size:12px}
    .ap-ico{display:grid;place-items:center;width:42px;height:42px;border-radius:13px;background:color-mix(in srgb,var(--c) 18%,#fff);color:var(--c);font-size:21px}
    .ap-card{padding:14px;border-radius:16px;background:#fff;box-shadow:0 4px 14px #4b55a01c}.ap-center{display:flex;flex-direction:column;align-items:center;gap:12px;text-align:center}
    .ap-row{display:flex;align-items:center;justify-content:space-between;width:100%;font-size:13px}
    .ap-card label{display:flex;justify-content:space-between;margin:16px 0 6px;color:#5a6188;font-size:11px}.ap-card output{color:#0d93a6;font-weight:600}
    .ap-note{display:block;margin-top:10px;color:#6b7194;font-size:10.5px;line-height:1.45}
    .ap-sw{position:relative;width:42px;height:24px;border:0;border-radius:14px;background:#c3c8e0;cursor:pointer;transition:.25s}
    .ap-sw i{position:absolute;top:3px;left:3px;width:18px;height:18px;border-radius:50%;background:#fff;box-shadow:0 1px 4px #0003;transition:.25s}.ap-sw.on{background:#12b5cb}.ap-sw.on i{left:21px}
    .ap-rng{width:100%;height:5px;border-radius:5px;appearance:none;-webkit-appearance:none;background:linear-gradient(90deg,#9be3ee,#12b5cb);cursor:pointer}
    .ap-rng::-webkit-slider-thumb{width:18px;height:18px;border:3px solid #fff;border-radius:50%;background:#12b5cb;box-shadow:0 2px 8px #0003;-webkit-appearance:none}
    .ap-rng::-moz-range-thumb{width:12px;height:12px;border:3px solid #fff;border-radius:50%;background:#12b5cb}
    .ap-rng--t{background:linear-gradient(90deg,#bcd7ff,#fff2cf,#ffb066)}
    .ap-colors{display:grid;grid-template-columns:repeat(3,1fr);gap:10px 6px;margin:14px 0}.ap-col{display:flex;flex-direction:column;align-items:center;gap:4px;padding:0;border:0;background:none;cursor:pointer;color:#5a6188;font-size:9.5px}
    .ap-col i{width:38px;height:38px;border:3px solid #fff;border-radius:50%;box-shadow:0 3px 10px #0003;transition:.2s}.ap-col.on i{transform:scale(1.15);outline:2px solid #12b5cb}.ap-col.on small{color:#0d93a6;font-weight:600}
    .ap-row--sp{margin-top:6px;padding-top:12px;border-top:1px solid #e8ebf7}
    .ap-mode{font-size:11px;font-weight:600;color:#0d93a6}
    .ap-chips{display:flex;flex-direction:column;gap:8px}.ap-chips button{padding:11px 12px;border:0;border-radius:12px;background:#fff;box-shadow:0 3px 10px #4b55a01c;text-align:left;font-weight:600;color:#1b2142;cursor:pointer}
    .ap-temp{font-size:50px;font-weight:300;line-height:1}.ap-temp sup{font-size:16px}
    .ap-step{display:flex;gap:14px}.ap-step button{width:46px;height:46px;border:0;border-radius:50%;background:#eef1ff;font-size:22px;color:#1b2142;cursor:pointer}
    .ap-seg{display:flex;width:100%;padding:3px;border-radius:12px;background:#eef1ff}.ap-seg button{flex:1;height:34px;border:0;border-radius:10px;background:transparent;font-weight:600;color:#5a6188;cursor:pointer;transition:.2s}.ap-seg button.on{background:#12b5cb;color:#fff;box-shadow:0 3px 10px #12b5cb55}
    .ap-art{display:flex;align-items:flex-end;justify-content:center;gap:5px;width:120px;height:120px;padding:24px;border-radius:20px;background:linear-gradient(135deg,#8a5cf6,#12b5cb)}
    .ap-art span{width:8px;height:16%;border-radius:4px;background:#fff}.ap-art.play span{animation:apeq .9s ease-in-out infinite alternate}
    .ap-art span:nth-child(2){animation-delay:.15s}.ap-art span:nth-child(3){animation-delay:.3s}.ap-art span:nth-child(4){animation-delay:.1s}.ap-art span:nth-child(5){animation-delay:.25s}
    @keyframes apeq{to{height:92%}}
    .ap-play{width:54px;height:54px;border:0;border-radius:50%;background:#12b5cb;color:#fff;font-size:18px;box-shadow:0 6px 16px #12b5cb66;cursor:pointer}
    .ap-fan{font-size:56px;color:#16b6c7}.ap-fan.s1{animation:hl360spin 2s linear infinite}.ap-fan.s2{animation:hl360spin 1s linear infinite}.ap-fan.s3{animation:hl360spin .5s linear infinite}
    .tap{transform:scale(.95)}
    .ap-nav{display:flex;justify-content:space-around;padding:8px 6px 14px;background:#fffc;border-top:1px solid #e1e4f5;color:#8a90b3;text-align:center;font-size:15px}
    .ap-nav span{display:flex;flex-direction:column;cursor:default}.ap-nav small{font-size:8px}.ap-nav .on{color:#12b5cb;cursor:pointer}
    .ap-snack{position:absolute;left:14px;right:14px;bottom:64px;padding:10px 12px;border-radius:12px;background:#1b2142;color:#fff;font-size:11px;opacity:0;transform:translateY(10px);transition:.3s;pointer-events:none}.ap-snack.show{opacity:1;transform:none}
    .hl360 :focus-visible{outline:2px solid #e4ca91;outline-offset:3px}

    @media(max-width:900px){
        .hl360__stage{height:clamp(700px,92vh,880px)}
        .hl360__phone{right:50%;top:auto;bottom:14px;margin:0;transform:translate(50%,120%) scale(.74);transform-origin:bottom center}
        .hl360.phone-in .hl360__phone{transform:translateX(50%) scale(.74)}
        .hl360.phone-hidden .hl360__phone{transform:translate(50%,120%) scale(.74)}
        .hl360__caption{top:84px;bottom:auto;left:18px}.hl360__hint{display:none}.hl360__header{top:16px;left:16px}.hl360__toolbar{top:14px;right:14px}
    }
    .ap-notif{position:absolute;z-index:6;left:10px;right:10px;top:34px;padding:9px 12px;border-radius:14px;background:#1b2142f2;color:#fff;transform:translateY(-130%);transition:transform .45s cubic-bezier(.2,.8,.2,1);box-shadow:0 8px 24px #0006}
    .ap-notif.show{transform:none}.ap-notif b{display:block;font-size:11.5px}.ap-notif small{font-size:9.5px;opacity:.75}
    .hl360.is-alert .hl360__stage:after{content:"";position:absolute;inset:0;z-index:3;pointer-events:none;box-shadow:inset 0 0 130px 40px rgba(255,40,40,.75);animation:hl360alert .45s ease-in-out infinite alternate}
    @keyframes hl360alert{from{opacity:.25}to{opacity:1}}
    @keyframes hl360blink{50%{opacity:.3}}
    .ap-cam{position:relative;height:110px;margin-bottom:10px;border-radius:14px;overflow:hidden;background:linear-gradient(135deg,#1c2430,#0b0f14);box-shadow:0 6px 16px #3a438033}
    .ap-cam:before{content:"";position:absolute;inset:0;background:repeating-linear-gradient(0deg,#ffffff0d 0 1px,transparent 1px 3px);animation:apscan 6s linear infinite}
    .ap-cam:after{content:"";position:absolute;left:-30%;top:0;width:30%;height:100%;background:linear-gradient(90deg,transparent,#ffffff1a,transparent);animation:apsweep 4s ease-in-out infinite}
    @keyframes apscan{to{background-position:0 60px}}@keyframes apsweep{to{left:110%}}
    .ap-rec{position:absolute;left:10px;top:8px;color:#ff5a5a;font-size:9.5px;font-weight:700;animation:hl360blink 1.4s infinite}.ap-camname{position:absolute;left:10px;bottom:8px;color:#cfd6e6;font-size:10px}
    .ap-test{width:100%;padding:10px;border:0;border-radius:11px;background:#eef1ff;color:#1b2142;font-weight:600;cursor:pointer}
    .ap-clock{font-size:42px;font-weight:300;line-height:1}.ap-phase{font-size:11px;font-weight:600;color:#0d93a6}
    .ap-rng--day{background:linear-gradient(90deg,#8ad0ff,#ffc36b,#ff8a5c,#2b3470)}.ap-ends{display:flex;justify-content:space-between;width:100%;font-size:9.5px;color:#6b7194}
    .ap-wx{display:grid;grid-template-columns:1fr 1fr;gap:8px}.ap-wx button{display:flex;flex-direction:column;align-items:center;gap:4px;padding:12px 4px;border:0;border-radius:12px;background:#eef1ff;color:#1b2142;font-weight:600;font-size:11px;cursor:pointer;transition:.2s}
    .ap-wx button span{font-size:22px}.ap-wx button.on{background:#12b5cb;color:#fff;box-shadow:0 4px 12px #12b5cb55}
    .ap-big{font-size:36px;font-weight:300}.ap-big small{font-size:14px}
    .ap-bar{display:grid;grid-template-columns:46px 1fr 44px;align-items:center;gap:8px;margin-top:10px;font-size:10.5px}.ap-bar i{display:block;height:7px;border-radius:5px;background:#eef1ff;position:relative;overflow:hidden}
    .ap-bar i:after{content:"";position:absolute;left:0;top:0;bottom:0;width:var(--w);background:var(--c);border-radius:5px;transition:width .5s}.ap-bar em{font-style:normal;text-align:right;color:#5a6188}
    .ap-vol{display:flex;justify-content:space-between;width:100%;margin-top:6px;color:#5a6188;font-size:11px}.ap-vol output{color:#0d93a6;font-weight:600}
    @media(prefers-reduced-motion:reduce){.hl360 *,.hl360 *:before,.hl360 *:after{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important}}
    /* ===== Plein écran (Smart Home) ===== */
    .hl360{--hl-top:96px;margin:0}
    .hl360__stage{height:100vh;height:100svh;min-height:560px;border:0;border-radius:0}
    .hl360__header{top:var(--hl-top)}
    .hl360__toolbar{top:var(--hl-top)}
    .hl360__tool--back{display:grid;place-items:center;text-decoration:none}
    .hl360__phone{top:calc(50% + 36px)}
    .hl360__caption{bottom:96px}
    @media (min-width:901px) and (max-height:780px){
        .hl360__phone{transform-origin:right center}
        .hl360.phone-in .hl360__phone{transform:scale(.84)}
    }
    @media (max-width:900px){
        .hl360__stage{height:100vh;height:100svh}
        .hl360__header{top:calc(var(--hl-top) - 8px)}.hl360__toolbar{top:calc(var(--hl-top) - 8px)}
        .hl360__caption{top:calc(var(--hl-top) + 56px)}
    }
</style>
@endif

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
    .hb-mk-sub { margin:56px 0 20px; font-family:Georgia,serif; font-size:clamp(24px,3vw,32px); font-weight:400; color:#f5f1e8; }
    @media (max-width:900px){ .hb-mk-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:620px){ .hb-mk-grid { grid-template-columns:1fr; } .hb-mk { padding:55px 0; } }

</style>
</x-hb-layout>