@php
    $pos = $position ?? 'top';
    $brands = [
        ['name' => 'ZKTeco', 'img' => 'images/brands/zkteco.png', 'type' => 'zk'],
        ['name' => 'G4',     'img' => 'images/brands/g4.png',     'type' => 'g4'],
        ['name' => 'TRX',    'img' => 'images/brands/trx.png',    'type' => 'trx'],
    ];
    $brandClass = fn ($type) => ['g4' => 'is-g4', 'trx' => 'is-trx'][$type] ?? '';
@endphp

@once
<style>
    .hb-bb {
        position: absolute; left: 0; right: 0; z-index: 5;
        display: flex; align-items: center; gap: 28px;
        height: 68px; padding: 0 28px;
        background: linear-gradient(180deg, rgba(12,11,9,.55), rgba(8,8,7,.55));
        -webkit-backdrop-filter: blur(5px); backdrop-filter: blur(5px);
        border-top: 1px solid rgba(214,173,85,.55);
        border-bottom: 1px solid rgba(214,173,85,.55);
        box-shadow: 0 0 30px -8px rgba(214,173,85,.35);
        overflow: hidden;
    }
    .hb-bb-top    { top: 0; }
    .hb-bb-bottom { bottom: 0; }

    .hb-bb-label {
        flex: none; display: flex; align-items: center; gap: 16px;
        font: 700 10.5px/1 Arial, Helvetica, sans-serif;
        letter-spacing: 4px; text-transform: uppercase; color: #e9c46f; white-space: nowrap;
    }
    .hb-bb-label::before { content: ""; width: 28px; height: 1px; background: #d6ad55; }
    .hb-bb-label-txt { display: flex; flex-direction: column; gap: 6px; }
    .hb-bb-label-main {
        font: 800 clamp(14px, 1.35vw, 18px)/1 Arial, Helvetica, sans-serif;
        letter-spacing: 3px; color: #e9c46f;
    }
    .hb-bb-label-sub {
        font: 700 clamp(9px, .75vw, 11px)/1 Arial, Helvetica, sans-serif;
        letter-spacing: 1.6px; color: #fff;
    }

    .hb-bb-logo { display: inline-flex; align-items: center; justify-content: center; height: 34px; flex: none; }
    .hb-bb-logo img { height: 100%; width: auto; max-width: 150px; object-fit: contain; display: block; }

    /* Logo G4 : boule blanche (cercle) avec bordure dorée */
    .hb-bb-logo.is-g4 {
        width: 56px; height: 56px; min-width: 56px;
        box-sizing: border-box;
        padding: 5px;
        background: #fff;
        border: 2px solid #d6ad55;
        border-radius: 50%;
        box-shadow: 0 4px 14px rgba(0,0,0,.45), 0 0 0 3px rgba(233,196,111,.2);
    }
    .hb-bb-logo.is-g4 img {
        width: 100%; height: 100%; max-width: none;
        object-fit: contain; border-radius: 50%;
        filter: none;
    }
    .hb-bb-logo.is-g4 .hb-bb-txt-g4 { color: #111; font-size: 22px; letter-spacing: -.5px; }

    /* Logo TRX : pastille blanche (le logo a un fond blanc) avec bordure dorée */
    .hb-bb-logo.is-trx {
        height: 44px; min-width: 84px;
        box-sizing: border-box;
        padding: 5px 12px;
        background: #fff;
        border: 2px solid #d6ad55;
        border-radius: 8px;
        box-shadow: 0 4px 14px rgba(0,0,0,.45), 0 0 0 3px rgba(233,196,111,.2);
    }
    .hb-bb-logo.is-trx img { height: 100%; width: auto; max-width: 110px; object-fit: contain; }

    .hb-bb-txt-zk { font: italic 900 30px/1 Arial, Helvetica, sans-serif; color: #fff; letter-spacing: -1px; }
    .hb-bb-txt-zk b { color: #6cc24a; font-weight: 900; }
    .hb-bb-txt-zk small { font-size: 16px; color: #6cc24a; font-style: italic; font-weight: 800; letter-spacing: 0; }
    .hb-bb-txt-g4 { font: 700 32px/1 Arial, Helvetica, sans-serif; color: #fff; letter-spacing: -1px; }
    .hb-bb-txt-trx { font: 900 22px/1 Arial, Helvetica, sans-serif; color: #4a4d52; letter-spacing: 1px; }
    .hb-bb-txt-trx b { color: #7ac143; font-weight: 900; }
    .hb-bb-sep { flex: none; width: 1px; height: 28px; background: rgba(214,173,85,.55); }

    /* Barre du haut : défilement continu */
    .hb-bb-marquee { flex: 1; min-width: 0; overflow: hidden;
        -webkit-mask-image: linear-gradient(90deg, transparent, #000 6%, #000 94%, transparent);
                mask-image: linear-gradient(90deg, transparent, #000 6%, #000 94%, transparent); }
    .hb-bb-track { display: flex; align-items: center; gap: 56px; width: max-content; animation: hbBbScroll 28s linear infinite; }
    .hb-bb-marquee:hover .hb-bb-track { animation-play-state: paused; }
    .hb-bb-group { display: flex; align-items: center; gap: 56px; flex: none; }
    @keyframes hbBbScroll { from { transform: translateX(0); } to { transform: translateX(-50%); } }

    /* Barre du bas : logos + flèches */
    .hb-bb-arrow {
        flex: none; width: 34px; height: 34px; display: grid; place-items: center;
        background: transparent; border: 0; cursor: pointer; color: #d6ad55;
        transition: color .3s, transform .3s;
    }
    .hb-bb-arrow:hover { color: #f6dc9b; transform: scale(1.2); }
    .hb-bb-arrow svg { width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .hb-bb-viewport { flex: 1; min-width: 0; display: flex; align-items: center; justify-content: center; gap: 22px; overflow-x: auto; scroll-behavior: smooth; scrollbar-width: none; }
    .hb-bb-viewport::-webkit-scrollbar { display: none; }
    .hb-bb-viewport .hb-bb-logo { height: 40px; }
    .hb-bb-viewport .hb-bb-logo.is-g4 { width: 56px; height: 56px; }
    .hb-bb-viewport .hb-bb-logo.is-trx { height: 46px; }
    .hb-bb-viewport .hb-bb-txt-zk { font-size: 36px; }
    .hb-bb-viewport .hb-bb-txt-g4 { font-size: 38px; }
    .hb-bb-viewport .hb-bb-logo.is-g4 .hb-bb-txt-g4 { font-size: 22px; }
    .hb-bb-viewport .hb-bb-sep { height: 36px; margin: 0 14px; }

    @media (max-width: 760px) {
        .hb-bb { height: 58px; padding: 0 14px; gap: 14px; }
        .hb-bb-label::before { display: none; }
        .hb-bb-label-main { font-size: 11px; letter-spacing: 2px; }
        .hb-bb-label-sub { font-size: 7.5px; letter-spacing: .8px; }
        .hb-bb-track, .hb-bb-group { gap: 34px; }
        .hb-bb-logo.is-g4,
        .hb-bb-viewport .hb-bb-logo.is-g4 { width: 46px; height: 46px; min-width: 46px; padding: 4px; }
        .hb-bb-logo.is-trx,
        .hb-bb-viewport .hb-bb-logo.is-trx { height: 38px; min-width: 70px; padding: 4px 9px; }
    }
    @media (prefers-reduced-motion: reduce) { .hb-bb-track { animation: none; } }
</style>
@endonce

@if($pos === 'top')
    <div class="hb-bb hb-bb-top" aria-label="Distributeur officiel">
        <div class="hb-bb-label">
            <div class="hb-bb-label-txt">
                <span class="hb-bb-label-main">Distributeur officiel</span>
                <span class="hb-bb-label-sub">Représentant exclusif de la marque SMART G4</span>
            </div>
        </div>
        <div class="hb-bb-marquee">
            <div class="hb-bb-track">
                @for($copy = 0; $copy < 2; $copy++)
                    <div class="hb-bb-group" @if($copy) aria-hidden="true" @endif>
                        @for($r = 0; $r < 3; $r++)
                            @foreach($brands as $b)
                                <span class="hb-bb-logo {{ $brandClass($b['type']) }}">
                                    @if(file_exists(public_path($b['img'])))
                                        <img src="{{ asset($b['img']) }}" alt="{{ $b['name'] }}" loading="lazy">
                                    @elseif($b['type'] === 'zk')
                                        <span class="hb-bb-txt-zk">ZK<b>T</b><small>eco</small></span>
                                    @elseif($b['type'] === 'trx')
                                        <span class="hb-bb-txt-trx"><b>T</b>RX</span>
                                    @else
                                        <span class="hb-bb-txt-g4">G4</span>
                                    @endif
                                </span>
                                <span class="hb-bb-sep" aria-hidden="true"></span>
                            @endforeach
                        @endfor
                    </div>
                @endfor
            </div>
        </div>
        <button type="button" class="hb-bb-arrow" aria-hidden="true" tabindex="-1">
            <svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg>
        </button>
    </div>
@else
    <div class="hb-bb hb-bb-bottom" aria-label="Marques partenaires">
        <div class="hb-bb-label">
            <div class="hb-bb-label-txt">
                <span class="hb-bb-label-main">Distributeur officiel</span>
                <span class="hb-bb-label-sub">Représentant exclusif de la marque SMART G4</span>
            </div>
        </div>
        <button type="button" class="hb-bb-arrow" data-bb-prev aria-label="Précédent">
            <svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></svg>
        </button>
        <div class="hb-bb-viewport" data-bb-view>
            @foreach($brands as $b)
                <span class="hb-bb-logo {{ $brandClass($b['type']) }}">
                    @if(file_exists(public_path($b['img'])))
                        <img src="{{ asset($b['img']) }}" alt="{{ $b['name'] }}" loading="lazy">
                    @elseif($b['type'] === 'zk')
                        <span class="hb-bb-txt-zk">ZK<b>T</b><small>eco</small></span>
                    @elseif($b['type'] === 'trx')
                        <span class="hb-bb-txt-trx"><b>T</b>RX</span>
                    @else
                        <span class="hb-bb-txt-g4">G4</span>
                    @endif
                </span>
                @if(!$loop->last)<span class="hb-bb-sep" aria-hidden="true"></span>@endif
            @endforeach
        </div>
        <button type="button" class="hb-bb-arrow" data-bb-next aria-label="Suivant">
            <svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg>
        </button>
    </div>

    @once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.hb-bb-bottom').forEach(function (bar) {
                var v = bar.querySelector('[data-bb-view]');
                var step = function () { return Math.max(v.clientWidth * .6, 140); };
                bar.querySelector('[data-bb-prev]').addEventListener('click', function () { v.scrollBy({ left: -step() }); });
                bar.querySelector('[data-bb-next]').addEventListener('click', function () { v.scrollBy({ left:  step() }); });
            });
        });
    </script>
    @endonce
@endif