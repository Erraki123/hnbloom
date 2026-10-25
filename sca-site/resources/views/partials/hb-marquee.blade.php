{{-- =====================================================================
     HN BLOOM · Bandeau défilant (marquee) des marques : ZKTeco & SMART G4 CONTROL
     Fichier : resources/views/partials/hb-marquee.blade.php
     Logos (optionnels) : public/images/brands/zkteco.png  et  public/images/brands/g4.png
     ===================================================================== --}}
@php
    $mqFile = fn ($p) => ($p && file_exists(public_path($p))) ? asset($p) : null;
    $mqBrands = [
        ['name' => 'ZKTeco',            'logo' => $mqFile('images/brands/zkteco.png'), 'tag' => 'DISTRIBUTEUR'],              // À VALIDER
        ['name' => 'SMART G4 CONTROL',  'logo' => $mqFile('images/brands/g4.png'),     'tag' => 'REPRÉSENTANT EXCLUSIF AU MAROC'],
    ];
@endphp

<div class="hb-mq" aria-label="Marques distribuées par HN BLOOM">
    <div class="hb-mq-track">
        @for($k = 0; $k < 2; $k++)
            <div class="hb-mq-group" @if($k === 1) aria-hidden="true" @endif>
                @for($r = 0; $r < 4; $r++)
                    @foreach($mqBrands as $b)
                        <div class="hb-mq-item">
                            @if($b['logo'])<img src="{{ $b['logo'] }}" alt="{{ $k === 0 && $r === 0 ? $b['name'] : '' }}" loading="lazy">@else<span class="hb-mq-name">{{ $b['name'] }}</span>@endif
                            <small>{{ $b['tag'] }}</small>
                        </div>
                        <span class="hb-mq-sep" aria-hidden="true"></span>
                    @endforeach
                @endfor
            </div>
        @endfor
    </div>
</div>

<style>
    .hb-mq { position:relative; overflow:hidden; padding:22px 0; background:rgba(9,9,8,.78); border-top:1px solid rgba(216,180,94,.28); border-bottom:1px solid rgba(216,180,94,.28);
        -webkit-mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent); mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent); }
    .hb-mq-track { display:flex; width:max-content; animation:hbMq 38s linear infinite; will-change:transform; }
    .hb-mq:hover .hb-mq-track { animation-play-state:paused; }
    .hb-mq-group { display:flex; align-items:center; flex:none; }
    .hb-mq-item { display:flex; align-items:center; gap:16px; flex:none; padding:0 38px; }
    .hb-mq-item img { max-height:34px; max-width:150px; object-fit:contain; opacity:.92; filter:drop-shadow(0 0 12px rgba(216,180,94,.25)); }
    .hb-mq-name { font-family:Georgia,"Times New Roman",serif; font-size:24px; letter-spacing:.1em; color:#f0d69a; white-space:nowrap; }
    .hb-mq-item small { color:#aaa69c; font:700 9px/1 Arial,Helvetica,sans-serif; letter-spacing:2.4px; white-space:nowrap; }
    .hb-mq-sep { flex:none; width:9px; height:9px; border:1px solid #d8b45e; transform:rotate(45deg); opacity:.75; }
    @keyframes hbMq { from { transform:translateX(0); } to { transform:translateX(-50%); } }
    @media (max-width:700px) { .hb-mq { padding:16px 0; } .hb-mq-item { padding:0 24px; gap:12px; } .hb-mq-name { font-size:19px; } .hb-mq-track { animation-duration:30s; } }
    @media (prefers-reduced-motion:reduce) { .hb-mq-track { animation:none; } .hb-mq { overflow-x:auto; } }
</style>
