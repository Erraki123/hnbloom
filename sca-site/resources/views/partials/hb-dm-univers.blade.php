{{-- HN BLOOM — Bloc "Choisissez votre univers" (même design que la page Domotique)
     Utilisation : @include('partials.hb-dm-univers', ['current' => $market['slug']]) --}}
@php
    $dmx = config('hnbloom.domotique') ?? [];
    $dmxMarkets = $dmx['markets'] ?? [];
    $dmxCurrent = $current ?? null;
@endphp

<div class="hb-dm-quick-univers hb-dmx" id="nos-univers">
    <div class="hb-dm-quick-heading">
        <div>
            <p class="hb-dm-quick-eyebrow">NOS SOLUTIONS</p>
            <h2>Choisissez votre <span>univers.</span></h2>
        </div>
        <p class="hb-dm-quick-note">SMART G4 CONTROL <span>·</span> DISTRIBUTION EXCLUSIVE AU MAROC</p>
    </div>

    <div class="hb-dm-grid">
        @foreach($dmxMarkets as $i => $m)
            @php
                $local = !empty($m['image']) && file_exists(public_path($m['image'])) ? asset($m['image']) : null;
                $img   = $local ?: ($m['fallback'] ?? '');
                $url   = route('domotique.show', $m['slug']);
                $mv    = !empty($m['video']) && file_exists(public_path($m['video'])) ? asset($m['video']) : null;
                $isCur = $dmxCurrent === $m['slug'];
            @endphp
            <a href="{{ $url }}" class="hb-dm-card{{ $isCur ? ' is-current' : '' }}" aria-label="Découvrir {{ $m['title'] }}" @if($isCur) aria-current="page" @endif>
                <div class="hb-dm-card-image">
                    @if($mv)
                        <video src="{{ $mv }}" poster="{{ $img }}" muted loop playsinline preload="none" disablepictureinpicture></video>
                    @else
                        <img src="{{ $img }}" alt="{{ $m['title'] }} — HN BLOOM" loading="lazy" decoding="async">
                    @endif
                    <span class="hb-dm-card-shine" aria-hidden="true"></span>
                    <span class="hb-dm-index">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="hb-dm-card-glow" aria-hidden="true"></span>
                    <div class="hb-dm-card-overlay">
                        <span class="hb-dm-card-kicker">{{ $m['category'] ?? 'SMART BUILDING' }}</span>
                        <h3>{{ $m['title'] }}</h3>
                        @if(!empty($m['text']))<p>{{ $m['text'] }}</p>@endif
                        <span class="hb-dm-card-discover">
                            {{ $isCur ? 'Vous êtes ici' : 'Voir les solutions' }}
                            <span class="hb-dm-card-circle"><x-icon name="arrow-up-right" :size="18" /></span>
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>

<script>
(function () {
    var reduce = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    var saver  = navigator.connection && navigator.connection.saveData;
    var canHover = window.matchMedia('(hover:hover) and (pointer:fine)').matches;
    if (reduce || saver || !canHover) return;
    document.querySelectorAll('.hb-dmx .hb-dm-card').forEach(function (card) {
        var v = card.querySelector('video'); if (!v) return;
        v.muted = true;
        var start = function () { v.preload = 'auto'; var p = v.play(); if (p && p.catch) p.catch(function () {}); };
        var stop  = function () { v.pause(); };
        card.addEventListener('mouseenter', start); card.addEventListener('focus', start);
        card.addEventListener('mouseleave', stop);  card.addEventListener('blur', stop);
    });
})();
</script>

<style>
    .hb-dmx { width:100%; margin-top:56px; }
    .hb-dmx, .hb-dmx * { box-sizing:border-box; }
    .hb-dmx .hb-dm-quick-heading { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; margin-bottom:14px; }
    .hb-dmx .hb-dm-quick-eyebrow { margin:0 0 5px; color:#e7c16e; font:700 8px/1.5 Arial,sans-serif; letter-spacing:2.5px; }
    .hb-dmx .hb-dm-quick-heading h2 { margin:0; color:#fffaf0; font:400 clamp(23px,2.4vw,32px)/1.12 Georgia,'Times New Roman',serif; }
    .hb-dmx .hb-dm-quick-heading h2 span { color:#e7c16e; font-style:italic; }
    .hb-dmx .hb-dm-quick-note { margin:0 0 3px; color:#ded3b9; font:700 8px/1.7 Arial,sans-serif; letter-spacing:1.4px; text-align:right; }
    .hb-dmx .hb-dm-quick-note span { color:#e7c16e; padding:0 3px; }

    .hb-dmx .hb-dm-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:13px; }
    .hb-dmx .hb-dm-card { position:relative; display:flex; flex-direction:column; min-width:0; height:clamp(220px,27vh,270px); overflow:hidden; isolation:isolate;
        border:1px solid rgba(226,190,105,.68); background:#090907; color:inherit; text-decoration:none; box-shadow:0 16px 42px rgba(0,0,0,.36);
        transition:transform .4s ease, border-color .4s ease, box-shadow .4s ease; }
    .hb-dmx .hb-dm-card:hover, .hb-dmx .hb-dm-card:focus-visible { transform:translateY(-5px); border-color:rgba(244,215,151,.98);
        box-shadow:0 26px 55px -18px rgba(0,0,0,.7), 0 0 30px -10px rgba(214,173,85,.35); }
    .hb-dmx .hb-dm-card.is-current { border-color:#f1d58e; box-shadow:0 0 0 1px #f1d58e, 0 16px 42px rgba(0,0,0,.36); }

    .hb-dmx .hb-dm-card-image { position:absolute; inset:0; background:#161510; }
    .hb-dmx .hb-dm-card-image img, .hb-dmx .hb-dm-card-image video { position:absolute; inset:0; display:block; width:100%; height:100%; object-fit:cover; transition:transform .9s cubic-bezier(.2,.7,.2,1); }
    .hb-dmx .hb-dm-card:hover .hb-dm-card-image img, .hb-dmx .hb-dm-card:hover .hb-dm-card-image video { transform:scale(1.06); }
    .hb-dmx .hb-dm-card-image::before { content:""; position:absolute; z-index:1; inset:0; pointer-events:none;
        background:linear-gradient(180deg,rgba(5,5,4,.22) 0%,rgba(5,5,4,.1) 22%,rgba(5,5,4,.3) 42%,rgba(5,5,4,.78) 66%,rgba(5,5,4,.97) 100%); }
    .hb-dmx .hb-dm-card-shine { position:absolute; z-index:2; top:0; bottom:0; left:0; width:45%; pointer-events:none;
        background:linear-gradient(100deg,transparent,rgba(255,244,214,.26),transparent); transform:translateX(-170%) skewX(-18deg); }
    .hb-dmx .hb-dm-card:hover .hb-dm-card-shine { transform:translateX(330%) skewX(-18deg); transition:transform 1s ease; }
    .hb-dmx .hb-dm-card-glow { position:absolute; z-index:2; inset:0; pointer-events:none; opacity:0;
        background:radial-gradient(ellipse at 50% 80%,rgba(226,184,91,.22),transparent 55%); transition:opacity .5s; }
    .hb-dmx .hb-dm-card:hover .hb-dm-card-glow { opacity:1; }
    .hb-dmx .hb-dm-card::before { content:""; position:absolute; z-index:4; top:0; left:0; right:0; height:2px; pointer-events:none;
        background:linear-gradient(90deg,transparent,#f1d58e,transparent); transform:scaleX(0); transition:transform .6s cubic-bezier(.2,.7,.2,1); }
    .hb-dmx .hb-dm-card:hover::before, .hb-dmx .hb-dm-card.is-current::before { transform:scaleX(1); }

    .hb-dmx .hb-dm-index { position:absolute; z-index:3; top:11px; left:11px; display:grid; width:31px; height:31px; place-items:center;
        border:1px solid rgba(245,218,155,.9); background:rgba(8,8,7,.8); color:#f5d991; font-size:10px; box-shadow:0 4px 14px rgba(0,0,0,.35); transition:transform .4s ease, background .3s, color .3s; }
    .hb-dmx .hb-dm-card:hover .hb-dm-index, .hb-dmx .hb-dm-card.is-current .hb-dm-index { background:#d6ad55; color:#11100c; }
    .hb-dmx .hb-dm-card:hover .hb-dm-index { transform:translateY(-3px); }

    .hb-dmx .hb-dm-card-overlay { position:absolute; z-index:3; right:0; bottom:0; left:0; padding:15px 17px 13px; color:#fffaf0;
        background:linear-gradient(180deg,rgba(9,9,7,0) 0%,rgba(9,9,7,.48) 12%,rgba(9,9,7,.88) 45%,rgba(9,9,7,.96) 100%);
        border-top:1px solid rgba(231,193,110,.22); text-shadow:0 2px 12px rgba(0,0,0,.65); }
    .hb-dmx .hb-dm-card-kicker { display:block; margin:0 0 4px; color:#f1d38b; font:700 8px/1.4 Arial,sans-serif; letter-spacing:1.65px; text-transform:uppercase; }
    .hb-dmx .hb-dm-card-overlay h3 { margin:0; color:#fffaf0; font:400 clamp(21px,1.8vw,27px)/1.08 Georgia,"Times New Roman",serif; letter-spacing:-.35px; }
    .hb-dmx .hb-dm-card-overlay p { margin:6px 0 0; color:#f0ece2; font:500 10px/1.45 Arial,sans-serif; }
    .hb-dmx .hb-dm-card-discover { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:9px; padding-top:8px; border-top:1px solid rgba(231,193,110,.38);
        color:#f4d58e; font:700 8px/1.5 Arial,sans-serif; letter-spacing:1.25px; text-transform:uppercase; text-shadow:none; }
    .hb-dmx .hb-dm-card-circle { display:grid; flex:0 0 27px; width:27px; height:27px; place-items:center; border:1px solid rgba(242,211,138,.8); border-radius:50%;
        color:#f5d991; background:rgba(8,8,7,.68); transition:background .35s, color .35s, transform .35s; }
    .hb-dmx .hb-dm-card:hover .hb-dm-card-circle { background:#e2bb62; color:#11100b; transform:rotate(45deg); }

    @media (max-width:900px){ .hb-dmx .hb-dm-grid { gap:9px; } .hb-dmx .hb-dm-card { height:230px; } .hb-dmx .hb-dm-quick-note { max-width:210px; } }
    @media (max-width:620px){
        .hb-dmx { margin-top:40px; }
        .hb-dmx .hb-dm-quick-heading { flex-direction:column; align-items:flex-start; gap:7px; }
        .hb-dmx .hb-dm-quick-note { max-width:none; text-align:left; }
        .hb-dmx .hb-dm-grid { grid-template-columns:1fr; gap:10px; }
        .hb-dmx .hb-dm-card { height:190px; }
    }
</style>