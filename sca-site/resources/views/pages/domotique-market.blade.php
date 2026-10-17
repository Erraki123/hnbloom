@php
    $d = config('hnbloom.domotique');
    $others = collect($d['markets'])->where('slug', '!=', $market['slug']);
    $file = fn ($p) => ($p && file_exists(public_path($p))) ? asset($p) : null;
@endphp

<x-hb-layout :title="$market['title'].' — Domotique'" :description="$market['text']">
<div class="hb-smart-page">
    @include('partials.hb-smart-bg')

    <section class="hb-mk">
        <div class="hb-wrap">

            <a class="hb-mk-back" href="{{ route('domotique') }}"><x-icon name="arrow-left" :size="14" /> Retour aux univers</a>

            <p class="hb-mk-eyebrow">{{ $market['category'] }}</p>
            <h1>{{ $market['title'] }}</h1>
            <p class="hb-mk-lead">{{ $market['text'] }}</p>

            <div class="hb-mk-grid">
                @foreach($market['items'] as $i => $item)
                    @php
                        $name = is_array($item) ? $item['name'] : $item;
                        $text = is_array($item) ? ($item['text'] ?? null) : null;
                        $pic  = is_array($item) ? $file($item['image'] ?? null) : null;
                    @endphp
                    <article class="hb-mk-card">
                        @if($pic)<div class="hb-mk-pic"><img src="{{ $pic }}" alt="{{ $name }}" loading="lazy"></div>@endif
                        <div class="hb-mk-body">
                            <span class="hb-mk-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $name }}</h3>
                            @if($text)<p>{{ $text }}</p>@endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="hb-mk-cta">
                <h3>Un projet {{ $market['title'] }} ?</h3>
                <a class="hb-button hb-button-gold" href="{{ route('contact', ['sujet' => 'quote']) }}">
                    Demander une étude <x-icon name="arrow-up-right" :size="16" />
                </a>
            </div>

            <div class="hb-mk-others">
                <p>Découvrir aussi</p>
                @foreach($others as $o)
                    <a href="{{ route('domotique.show', $o['slug']) }}">{{ $o['title'] }} <x-icon name="arrow-right" :size="14" /></a>
                @endforeach
            </div>
        </div>
    </section>
</div>

<style>
    .hb-mk { --g:#d8b45e; --gl:#f0d69a; --border:rgba(216,180,94,.25); padding:80px 0; background:rgba(9,9,8,.35); }
    .hb-mk-back { display:inline-flex; gap:8px; align-items:center; color:#bdb8ad; font-size:11px; text-decoration:none; margin-bottom:34px; }
    .hb-mk-back:hover { color:var(--gl); }
    .hb-mk-eyebrow { margin:0 0 14px; color:var(--g); font-size:10px; letter-spacing:2.4px; font-weight:600; }
    .hb-mk h1 { margin:0; font-family:Georgia,serif; font-size:clamp(40px,5.5vw,64px); font-weight:400; color:var(--gl); }
    .hb-mk-lead { max-width:620px; margin:18px 0 44px; color:#c0bcb2; font-size:13px; line-height:1.9; }
    .hb-mk-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
    .hb-mk-card { border:1px solid var(--border); background:rgba(16,16,14,.82); backdrop-filter:blur(6px); overflow:hidden; transition:border-color .25s, transform .25s; }
    .hb-mk-card:hover { border-color:var(--g); transform:translateY(-3px); }
    .hb-mk-pic { height:170px; overflow:hidden; }
    .hb-mk-pic img { width:100%; height:100%; object-fit:cover; display:block; }
    .hb-mk-body { padding:22px; }
    .hb-mk-num { color:var(--g); font-size:10px; letter-spacing:1.5px; }
    .hb-mk-body h3 { margin:10px 0 0; font-family:Georgia,serif; font-size:20px; font-weight:400; line-height:1.3; }
    .hb-mk-body p { margin:10px 0 0; color:#aaa69c; font-size:11px; line-height:1.8; }
    .hb-mk-cta { display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap;
        margin-top:40px; padding:26px; border:1px solid var(--border); background:rgba(17,17,14,.85); }
    .hb-mk-cta h3 { margin:0; font-family:Georgia,serif; font-size:24px; font-weight:400; }
    .hb-mk-others { display:flex; align-items:center; gap:22px; flex-wrap:wrap; margin-top:30px; font-size:12px; }
    .hb-mk-others p { margin:0; color:#8f8a7e; }
    .hb-mk-others a { display:inline-flex; gap:8px; align-items:center; color:var(--gl); text-decoration:none; }
    @media (max-width:900px){ .hb-mk-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:620px){ .hb-mk-grid { grid-template-columns:1fr; } .hb-mk { padding:55px 0; } }
</style>
</x-hb-layout>
