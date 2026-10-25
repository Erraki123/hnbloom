@php
    $features = [
        ['t' => "Contrôle d'accès\nbiométrique", 'i' => '<path d="M12 11v3m-3-2a3 3 0 016 0v3m-9-3a6 6 0 0112 0v4m-15-4a9 9 0 0118 0v2M9 20c0-1.5.3-3 .6-4m5.4 5c.3-1.4.5-2.6.5-4"/>'],
        ['t' => "Sécurité électronique\navancée",  'i' => '<path d="M12 3l8 3v6c0 4.5-3.2 8-8 9-4.8-1-8-4.5-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>'],
        ['t' => "Solutions sur mesure",            'i' => '<path d="M5 21V8l7-4 7 4v13M9 21v-4h6v4M9 10h2M13 10h2M9 13h2M13 13h2"/>'],
        ['t' => "Accompagnement\nlocal",           'i' => '<path d="M4 14v-2a8 8 0 0116 0v2"/><rect x="3" y="14" width="4" height="6" rx="1.5"/><rect x="17" y="14" width="4" height="6" rx="1.5"/><path d="M19 20c0 1.4-2 2-5 2"/>'],
    ];
@endphp

@once
<style>
    .hb-hf {
        position: absolute; z-index: 3; top: 50%; right: 3%;
        transform: translateY(-50%);
        width: clamp(190px, 17vw, 250px);
        display: flex; flex-direction: column;
        padding: 6px 18px;
        background: rgba(10,9,8,.55);
        border: 1px solid rgba(214,173,85,.28);
        -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px);
        list-style: none; margin: 0;
    }
    .hb-hf li {
        display: flex; align-items: center; gap: 14px; padding: 17px 0;
        border-bottom: 1px solid rgba(214,173,85,.18);
        font: 400 12px/1.45 Arial, Helvetica, sans-serif; color: #f1ece1; white-space: pre-line;
        transition: transform .4s ease, color .3s;
    }
    .hb-hf li:last-child { border-bottom: 0; }
    .hb-hf li:hover { transform: translateX(-4px); color: #f6dc9b; }
    .hb-hf svg { flex: none; width: 26px; height: 26px; fill: none; stroke: #d6ad55; stroke-width: 1.4; stroke-linecap: round; stroke-linejoin: round; }

    @media (max-width: 1100px) {
        .hb-hf { position: relative; top: auto; right: auto; transform: none; width: auto;
                 margin: 8px 5% 28px; display: grid; grid-template-columns: repeat(2, 1fr); column-gap: 24px; }
        .hb-hf li:nth-last-child(2) { border-bottom: 0; }
    }
    @media (max-width: 560px) {
        .hb-hf { grid-template-columns: 1fr; }
        .hb-hf li:nth-last-child(2) { border-bottom: 1px solid rgba(214,173,85,.18); }
    }
</style>
@endonce

<ul class="hb-hf" aria-label="Nos atouts">
    @foreach($features as $f)
        <li>
            <svg viewBox="0 0 24 24" aria-hidden="true">{!! $f['i'] !!}</svg>
            <span>{{ $f['t'] }}</span>
        </li>
    @endforeach
</ul>
