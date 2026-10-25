{{-- resources/views/partials/hb-floating-contact.blade.php
     Boutons flottants à droite : WhatsApp, Appel + réseaux sociaux (Facebook, Instagram, etc.)
     À inclure une fois dans chaque layout : @include('partials.hb-floating-contact') --}}
@php
    $fc  = config('hnbloom');
    $fct = $fc['contact'] ?? [];

    $fcPhone = $fct['phones'][0] ?? null;
    $fcWa    = $fct['whatsapp'] ?? null;
    $fcWaTxt = urlencode('Bonjour HN BLOOM, je souhaite des informations sur vos solutions.');

    // Icônes (tracés type « Lucide », 24x24, trait)
    $fcIcons = [
        'facebook'  => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
        'instagram' => '<rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>',
        'linkedin'  => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/>',
        'youtube'   => '<path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/>',
        'twitter'   => '<path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>',
        'link'      => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
    ];

    // Détection du réseau d'après le libellé ou l'URL
    $fcKind = function (array $s) {
        $t = strtolower(($s['label'] ?? '') . ' ' . ($s['href'] ?? ''));
        foreach (['facebook' => 'facebook', 'instagram' => 'instagram', 'linkedin' => 'linkedin', 'youtube' => 'youtube', 'twitter' => 'twitter', 'x.com' => 'twitter'] as $needle => $kind) {
            if (str_contains($t, $needle)) return $kind;
        }
        return 'link';
    };
    $fcSocials = collect($fct['socials'] ?? [])->filter(fn ($s) => !empty($s['href']))->values();
@endphp

<div class="hb-fc" role="complementary" aria-label="Contact rapide">

    @if($fcWa)
        <a href="https://wa.me/{{ $fcWa }}?text={{ $fcWaTxt }}" target="_blank" rel="noopener noreferrer"
           class="hb-fc-btn is-wa" style="--n:0" aria-label="Écrire sur WhatsApp">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
                <g transform="translate(7.4 7.2) scale(.42)"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke-width="2.6"/></g>
            </svg>
            <span class="hb-fc-tip">WhatsApp</span>
        </a>
    @endif

    @if($fcPhone)
        <a href="tel:{{ preg_replace('/\s+/', '', $fcPhone) }}" class="hb-fc-btn is-call" style="--n:1" aria-label="Appeler {{ $fcPhone }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            <span class="hb-fc-tip">Appeler · {{ $fcPhone }}</span>
        </a>
    @endif

    @foreach($fcSocials as $k => $s)
        @php $kind = $fcKind($s); @endphp
        <a href="{{ $s['href'] }}" target="_blank" rel="noopener noreferrer"
           class="hb-fc-btn is-{{ $kind }}" style="--n:{{ $k + 2 }}" aria-label="{{ $s['label'] ?? ucfirst($kind) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $fcIcons[$kind] !!}</svg>
            <span class="hb-fc-tip">{{ $s['label'] ?? ucfirst($kind) }}</span>
        </a>
    @endforeach
</div>

<style>
    .hb-fc { position:fixed; z-index:900; right:18px; top:50%; transform:translateY(-50%); display:flex; flex-direction:column; gap:10px; }

    .hb-fc-btn {
        position:relative; display:grid; place-items:center; width:48px; height:48px;
        border:1px solid rgba(216,180,94,.55); border-radius:50%;
        background:rgba(12,12,10,.88); color:#f0d69a; text-decoration:none;
        -webkit-backdrop-filter:blur(6px); backdrop-filter:blur(6px);
        box-shadow:0 10px 26px rgba(0,0,0,.45);
        transition:transform .3s cubic-bezier(.2,.7,.2,1), background .3s, border-color .3s, color .3s, box-shadow .3s;
        animation:hbFcIn .7s calc(.6s + var(--n,0) * .09s) cubic-bezier(.2,.7,.2,1) backwards;
    }
    .hb-fc-btn svg { display:block; width:22px; height:22px; }
    .hb-fc-btn:hover, .hb-fc-btn:focus-visible { transform:translateX(-5px) scale(1.06); color:#fff; outline:none; }
    .hb-fc-btn:focus-visible { box-shadow:0 0 0 3px rgba(216,180,94,.4), 0 10px 26px rgba(0,0,0,.45); }

    /* Couleurs de marque au survol */
    .hb-fc-btn.is-wa        { background:#25d366; border-color:#25d366; color:#fff; animation:hbFcIn .7s .6s cubic-bezier(.2,.7,.2,1) backwards, hbFcPulse 2.8s 2s infinite; }
    .hb-fc-btn.is-wa:hover  { background:#1ebe5a; border-color:#1ebe5a; }
    .hb-fc-btn.is-call      { background:linear-gradient(135deg,#f0d38b,#c99b40); border-color:#d8b45e; color:#15130d; }
    .hb-fc-btn.is-call:hover { color:#15130d; filter:brightness(1.08); }
    .hb-fc-btn.is-facebook:hover  { background:#1877f2; border-color:#1877f2; }
    .hb-fc-btn.is-instagram:hover { background:linear-gradient(45deg,#f9a03f,#e1306c 55%,#833ab4); border-color:#e1306c; }
    .hb-fc-btn.is-linkedin:hover  { background:#0a66c2; border-color:#0a66c2; }
    .hb-fc-btn.is-youtube:hover   { background:#ff0000; border-color:#ff0000; }
    .hb-fc-btn.is-twitter:hover   { background:#111; border-color:#fff; }
    .hb-fc-btn.is-link:hover      { background:#d8b45e; border-color:#d8b45e; color:#15130d; }

    /* Info-bulle à gauche */
    .hb-fc-tip {
        position:absolute; right:calc(100% + 12px); top:50%; padding:8px 14px; white-space:nowrap;
        border:1px solid rgba(216,180,94,.4); background:rgba(11,11,9,.95); color:#f6f1e7;
        font:600 11px/1 Arial,Helvetica,sans-serif; letter-spacing:.5px;
        opacity:0; transform:translate(8px,-50%); pointer-events:none; transition:opacity .25s, transform .25s;
    }
    .hb-fc-btn:hover .hb-fc-tip, .hb-fc-btn:focus-visible .hb-fc-tip { opacity:1; transform:translate(0,-50%); }

    @keyframes hbFcIn    { from { opacity:0; transform:translateX(30px); } to { opacity:1; transform:none; } }
    @keyframes hbFcPulse { 0% { box-shadow:0 10px 26px rgba(0,0,0,.45), 0 0 0 0 rgba(37,211,102,.55); } 70%,100% { box-shadow:0 10px 26px rgba(0,0,0,.45), 0 0 0 16px rgba(37,211,102,0); } }

    @media (max-width:760px){
        .hb-fc { right:10px; top:auto; bottom:18px; transform:none; gap:8px; }
        .hb-fc-btn { width:42px; height:42px; }
        .hb-fc-btn svg { width:19px; height:19px; }
        .hb-fc-tip { display:none; }
    }
    @media (prefers-reduced-motion:reduce){
        .hb-fc-btn, .hb-fc-btn.is-wa { animation:none !important; transition:none; }
    }
    @media print { .hb-fc { display:none; } }
</style>