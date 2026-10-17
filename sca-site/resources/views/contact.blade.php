@php
    $c = config('hnbloom');
    $ct = $c['contact'];

    // Photo de fond : public/images/contact.jpg (sinon hero.jpg, sinon about.jpg)
    $bgPath = collect([$c['images']['contact'] ?? 'images/contact.jpg', $c['images']['hero'] ?? null, $c['images']['about'] ?? null])
        ->first(fn ($p) => $p && file_exists(public_path($p)));
    $cover = $bgPath ? asset($bgPath) : null;

    // Vidéo du hero (optionnelle)
    $videoPath = $c['contact_video'] ?? null;
    $heroVideo = ($videoPath && file_exists(public_path($videoPath))) ? asset($videoPath) : null;

    // Carte
    $mapQuery = $ct['map_query'] ?? $ct['address'];
    $mapSrc   = 'https://www.google.com/maps?q=' . urlencode($mapQuery) . '&output=embed&hl=fr';
    $mapLink  = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($mapQuery);
    $dirLink  = 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($mapQuery);

    $waText = urlencode('Bonjour HN BLOOM, je souhaite des informations sur vos solutions.');
    $firstPhone = $ct['phones'][0] ?? null;
@endphp

<x-hb-layout title="Contact" description="Contactez HN BLOOM à Casablanca : demande de devis, renseignement produit, support technique.">

<div class="hb-ct-page" @if($cover) style="--ct-bg:url('{{ $cover }}')" @endif>

    {{-- ============ HERO ============ --}}
    <section class="hb-ct-hero">
        @if($cover || $heroVideo)
            <div class="hb-ct-hero-media" aria-hidden="true">
                @if($cover)
                    <div class="hb-ct-hero-bg" style="background-image:url('{{ $cover }}')"></div>
                @endif
                @if($heroVideo)
                    <video class="hb-ct-hero-video" autoplay muted loop playsinline preload="auto" disablepictureinpicture
                           onplaying="this.classList.add('is-on')" onerror="this.remove()">
                        <source src="{{ $heroVideo }}" type="video/mp4" onerror="this.parentNode.remove()">
                    </video>
                @endif
            </div>
        @endif

        <div class="hb-wrap hb-ct-hero-inner">
            <nav class="hb-ct-crumbs" aria-label="Fil d’Ariane">
                <a href="{{ route('home') }}">Accueil</a><span>/</span><b>Contact</b>
            </nav>
            <p class="hb-eyebrow">CONTACT</p>
            <h1>Parlons de<br>votre projet</h1>
            <p class="hb-ct-lead">Décrivez votre besoin : nos ingénieurs étudient votre demande et reviennent vers vous.</p>

            <div class="hb-ct-hero-actions">
                @if($firstPhone)
                    <a href="tel:{{ preg_replace('/\s+/', '', $firstPhone) }}" class="hb-ct-btn">
                        <x-icon name="phone" :size="15" /> {{ $firstPhone }}
                    </a>
                @endif
                <a href="https://wa.me/{{ $ct['whatsapp'] }}?text={{ $waText }}" target="_blank" rel="noreferrer" class="hb-ct-btn is-outline">
                    <x-icon name="message-circle" :size="15" /> WhatsApp
                </a>
            </div>
        </div>
    </section>

    {{-- ============ CARTES RAPIDES ============ --}}
    <section class="hb-ct-cards-wrap">
        <div class="hb-wrap">
            <div class="hb-ct-cards">
                <div class="hb-ct-card">
                    <span class="hb-ct-ico"><x-icon name="phone" :size="20" /></span>
                    <small>TÉLÉPHONE</small>
                    @foreach($ct['phones'] as $p)
                        <a href="tel:{{ preg_replace('/\s+/', '', $p) }}">{{ $p }}</a>
                    @endforeach
                </div>

                <div class="hb-ct-card">
                    <span class="hb-ct-ico"><x-icon name="mail" :size="20" /></span>
                    <small>E-MAIL</small>
                    <a href="mailto:{{ $ct['email'] }}">{{ $ct['email'] }}</a>
                </div>

                <div class="hb-ct-card">
                    <span class="hb-ct-ico"><x-icon name="map-pin" :size="20" /></span>
                    <small>ADRESSE</small>
                    <a href="{{ $mapLink }}" target="_blank" rel="noreferrer">{{ $ct['address'] }}</a>
                </div>

                <div class="hb-ct-card">
                    <span class="hb-ct-ico"><x-icon name="clock" :size="20" /></span>
                    <small>HORAIRES</small>
                    <span class="hb-ct-text">{{ $ct['hours'] }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FORMULAIRE ============ --}}
    <section class="hb-ct-main">
        <div class="hb-wrap hb-ct-grid">

            <form method="POST" action="{{ route('contact.store') }}" class="hb-ct-form">
                @csrf
                <div style="position:absolute;left:-9999px" aria-hidden="true">
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="hb-ct-form-head">
                    <p class="hb-ct-kicker">ENVOYER UN MESSAGE</p>
                    <h2>Demande de devis<br>ou renseignement</h2>
                </div>

                @if(session('success'))
                    <div class="hb-ct-alert is-ok">
                        <x-icon name="check" :size="18" /> <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="hb-ct-alert is-err">Merci de vérifier les champs signalés ci-dessous.</div>
                @endif

                <div class="hb-ct-row">
                    <div>
                        <label class="hb-ct-label" for="f-name">Nom complet *</label>
                        <input id="f-name" class="hb-ct-input" type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required autocomplete="name">
                        @include('partials.hb-field-error', ['name' => 'name'])
                    </div>
                    <div>
                        <label class="hb-ct-label" for="f-email">E-mail *</label>
                        <input id="f-email" class="hb-ct-input" type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required autocomplete="email">
                        @include('partials.hb-field-error', ['name' => 'email'])
                    </div>
                </div>

                <div class="hb-ct-row">
                    <div>
                        <label class="hb-ct-label" for="f-phone">Téléphone</label>
                        <input id="f-phone" class="hb-ct-input" type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" autocomplete="tel">
                        @include('partials.hb-field-error', ['name' => 'phone'])
                    </div>
                    <div>
                        <label class="hb-ct-label" for="f-company">Société</label>
                        <input id="f-company" class="hb-ct-input" type="text" name="company" value="{{ old('company', auth()->user()->company_name ?? '') }}" autocomplete="organization">
                        @include('partials.hb-field-error', ['name' => 'company'])
                    </div>
                </div>

                <div>
                    <label class="hb-ct-label" for="f-subject">Sujet *</label>
                    <select id="f-subject" class="hb-ct-input" name="subject" required>
                        <option value="">Choisir un sujet</option>
                        @foreach(\App\Models\ContactMessage::SUBJECTS as $k => $l)
                            <option value="{{ $k }}" @selected(old('subject', request('sujet')) == $k)>{{ $l }}</option>
                        @endforeach
                    </select>
                    @include('partials.hb-field-error', ['name' => 'subject'])
                </div>

                <div>
                    <label class="hb-ct-label" for="f-message">Message * <small>(type de bâtiment, ville, solution recherchée, délai…)</small></label>
                    <textarea id="f-message" class="hb-ct-input" name="message" rows="6" required>{{ old('message') }}</textarea>
                    @include('partials.hb-field-error', ['name' => 'message'])
                </div>

                <button type="submit" class="hb-ct-submit">
                    Envoyer le message <x-icon name="arrow-right" :size="16" />
                </button>
            </form>

            {{-- Panneau latéral --}}
            <aside class="hb-ct-side">
                <p class="hb-ct-kicker">COMMENT ÇA SE PASSE</p>
                <ol class="hb-ct-steps">
                    <li><b>01</b><span><strong>Vous nous écrivez</strong>Décrivez votre site et votre besoin.</span></li>
                    <li><b>02</b><span><strong>Nous étudions</strong>Nos ingénieurs analysent votre demande.</span></li>
                    <li><b>03</b><span><strong>Nous vous répondons</strong>Proposition technique adaptée à votre bâtiment.</span></li>
                </ol>

                <div class="hb-ct-side-box">
                    <p class="hb-ct-kicker">RÉPONSE IMMÉDIATE ?</p>
                    <a href="https://wa.me/{{ $ct['whatsapp'] }}?text={{ $waText }}" target="_blank" rel="noreferrer" class="hb-ct-btn is-wide">
                        <x-icon name="message-circle" :size="16" /> Écrire sur WhatsApp
                    </a>
                    @if($firstPhone)
                        <a href="tel:{{ preg_replace('/\s+/', '', $firstPhone) }}" class="hb-ct-btn is-outline is-wide">
                            <x-icon name="phone" :size="16" /> Appeler {{ $firstPhone }}
                        </a>
                    @endif
                </div>

                @if(!empty($ct['socials']))
                    <div class="hb-ct-socials">
                        <p class="hb-ct-kicker">SUIVEZ-NOUS</p>
                        <div>
                            @foreach($ct['socials'] as $s)
                                <a href="{{ $s['href'] }}" target="_blank" rel="noreferrer">{{ $s['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </section>

    {{-- ============ CARTE ============ --}}
    <section class="hb-ct-map" x-data="{ dark: true }">
        <iframe
            title="Carte HN BLOOM"
            :class="dark ? 'is-dark' : ''"
            class="is-dark"
            src="{{ $mapSrc }}"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            allowfullscreen></iframe>

        <div class="hb-wrap hb-ct-map-overlay">
            <div class="hb-ct-map-card">
                <p class="hb-ct-kicker">NOTRE BUREAU</p>
                <h3>HN BLOOM</h3>
                <p>{{ $ct['address'] }}</p>
                <p class="hb-ct-map-hours"><x-icon name="clock" :size="14" /> {{ $ct['hours'] }}</p>
                <a href="{{ $dirLink }}" target="_blank" rel="noreferrer" class="hb-ct-btn is-wide">
                    Itinéraire <x-icon name="arrow-up-right" :size="15" />
                </a>
                <button type="button" class="hb-ct-toggle" @click="dark = !dark" x-text="dark ? 'Carte claire' : 'Carte sombre'"></button>
            </div>
        </div>
    </section>

</div>{{-- /hb-ct-page --}}

    @include('partials.hb-cta', ['heading' => 'Engagez-vous dans l’Excellence avec HN BLOOM'])

    <style>
        /* ====== FOND DE LA PAGE (contact.jpg) ====== */
        .hb-ct-page {
            background:
                linear-gradient(180deg, rgba(9, 9, 8, .72) 0%, rgba(9, 9, 8, .88) 100%),
                var(--ct-bg, none) center / cover no-repeat fixed,
                #090908;
        }

        .hb-ct-hero, .hb-ct-cards-wrap, .hb-ct-main, .hb-ct-map {
            --g:#d8b45e; --gl:#f0d69a; --muted:#b1ada3; --border:rgba(216,180,94,.25); --panel:rgba(16,16,14,.92);
            color:#f6f1e7;
        }

        /* ---------- HERO ---------- */
        .hb-ct-hero { position:relative; overflow:hidden; min-height:460px; display:flex; align-items:flex-end; background:transparent; }
        .hb-ct-hero-media { position:absolute; inset:0; }
        .hb-ct-hero-bg { position:absolute; inset:0; background-size:cover; background-position:center; }
        .hb-ct-hero-video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:block; opacity:0; transition:opacity .8s ease; }
        .hb-ct-hero-video.is-on { opacity:1; }
        /* Voile du hero : plus léger que celui de la page pour bien montrer la photo */
        .hb-ct-hero::after { content:""; position:absolute; inset:0; background:linear-gradient(180deg,rgba(9,9,8,.55),rgba(9,9,8,.38) 45%,rgba(9,9,8,.8) 100%); }
        .hb-ct-hero-inner { position:relative; z-index:1; width:100%; padding-top:120px; padding-bottom:110px; }
        .hb-ct-crumbs { display:flex; gap:10px; align-items:center; margin-bottom:28px; font-size:11px; color:#d0ccc1; }
        .hb-ct-crumbs a { color:#d0ccc1; text-decoration:none; } .hb-ct-crumbs a:hover { color:var(--gl); }
        .hb-ct-crumbs b { color:var(--gl); font-weight:400; } .hb-ct-crumbs span { opacity:.5; }
        .hb-ct-hero .hb-eyebrow { margin:0 0 16px; color:var(--g); font-size:10px; font-weight:600; letter-spacing:2.4px; }
        .hb-ct-hero h1 { margin:0; font-family:Georgia,"Times New Roman",serif; font-size:clamp(38px,6vw,74px); font-weight:400; line-height:1.05; letter-spacing:-1.2px; color:#f6f1e7; }
        .hb-ct-lead { max-width:540px; margin:22px 0 0; color:#d6d2c8; font-size:14px; line-height:1.9; }
        .hb-ct-hero-actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:30px; }

        /* ---------- BOUTONS ---------- */
        .hb-ct-btn { display:inline-flex; align-items:center; justify-content:center; gap:12px; min-height:46px; padding:0 22px; border:1px solid var(--g); background:var(--g); color:#15130d; font-size:11px; font-weight:600; letter-spacing:.04em; text-decoration:none; cursor:pointer; transition:.25s; }
        .hb-ct-btn:hover { background:transparent; color:var(--gl); transform:translateY(-2px); }
        .hb-ct-btn.is-outline { background:rgba(0,0,0,.3); color:var(--gl); }
        .hb-ct-btn.is-outline:hover { background:var(--g); color:#15130d; }
        .hb-ct-btn.is-wide { width:100%; margin-top:12px; }

        /* ---------- CARTES RAPIDES ---------- */
        .hb-ct-cards-wrap { position:relative; z-index:2; margin-top:-70px; background:transparent; }
        .hb-ct-cards { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
        .hb-ct-card { padding:26px 24px 24px; border:1px solid var(--border); background:var(--panel); box-shadow:0 20px 50px rgba(0,0,0,.35); transition:border-color .3s, transform .3s; animation:hb-ct-in .7s cubic-bezier(.2,.7,.2,1) both; }
        .hb-ct-card:nth-child(2){animation-delay:.07s} .hb-ct-card:nth-child(3){animation-delay:.14s} .hb-ct-card:nth-child(4){animation-delay:.21s}
        .hb-ct-card:hover { border-color:var(--g); transform:translateY(-4px); }
        .hb-ct-ico { display:grid; width:46px; height:46px; place-items:center; margin-bottom:20px; border:1px solid var(--border); border-radius:50%; color:var(--gl); }
        .hb-ct-card small { display:block; margin-bottom:10px; color:var(--g); font-size:9px; font-weight:600; letter-spacing:2px; }
        .hb-ct-card a, .hb-ct-text { display:block; margin-top:4px; color:#f1ece0; font-family:Georgia,serif; font-size:16px; line-height:1.5; text-decoration:none; overflow-wrap:anywhere; }
        .hb-ct-card a:hover { color:var(--gl); }

        /* ---------- FORMULAIRE ---------- */
        .hb-ct-main { padding:90px 0 100px; background:rgba(9,9,8,.35); }
        .hb-ct-grid { display:grid; grid-template-columns:minmax(0,1.6fr) minmax(300px,1fr); gap:56px; align-items:start; }
        .hb-ct-form { position:relative; display:grid; gap:20px; padding:40px; border:1px solid var(--border); background:var(--panel); }
        .hb-ct-kicker { margin:0 0 14px; color:var(--g); font-size:10px; font-weight:600; letter-spacing:2.2px; }
        .hb-ct-form-head h2 { margin:0 0 6px; font-family:Georgia,serif; font-size:clamp(26px,3vw,36px); font-weight:400; line-height:1.2; color:#f6f1e7; }
        .hb-ct-row { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
        .hb-ct-label { display:block; margin-bottom:8px; color:var(--muted); font-size:10px; letter-spacing:.14em; text-transform:uppercase; }
        .hb-ct-label small { text-transform:none; letter-spacing:0; opacity:.8; }
        .hb-ct-input { width:100%; padding:14px 15px; border:1px solid var(--border); border-radius:0; background:#0b0b09; color:#f6f1e7; font:inherit; font-size:14px; transition:border-color .25s, background .25s; }
        .hb-ct-input:focus { outline:none; border-color:var(--g); background:#0e0e0b; }
        select.hb-ct-input { appearance:none; background-image:linear-gradient(45deg,transparent 50%,var(--g) 50%),linear-gradient(135deg,var(--g) 50%,transparent 50%); background-position:calc(100% - 20px) 50%,calc(100% - 14px) 50%; background-size:6px 6px; background-repeat:no-repeat; padding-right:40px; }
        select.hb-ct-input option { background:#10100e; color:#f6f1e7; }
        textarea.hb-ct-input { resize:vertical; min-height:140px; }
        .hb-ct-submit { display:inline-flex; align-items:center; justify-content:center; gap:16px; justify-self:start; min-height:52px; padding:0 30px; border:1px solid var(--g); background:linear-gradient(110deg,#c7983e,#f0d58e); color:#15130d; font:inherit; font-size:12px; font-weight:600; letter-spacing:.05em; cursor:pointer; transition:.25s; }
        .hb-ct-submit:hover { transform:translateY(-2px); filter:brightness(1.07); }
        .hb-ct-alert { display:flex; align-items:center; gap:12px; padding:14px 16px; border:1px solid var(--g); background:rgba(216,180,94,.08); color:#f1ece0; font-size:14px; }
        .hb-ct-alert.is-err { border-color:#d9534f; background:rgba(217,83,79,.08); }
        .hb-ct-main .hb-error { margin:6px 0 0; color:#ef8a86; font-size:12px; }

        /* ---------- PANNEAU LATÉRAL ---------- */
        .hb-ct-side { position:sticky; top:100px; }
        .hb-ct-steps { margin:0 0 34px; padding:0; list-style:none; border-top:1px solid var(--border); }
        .hb-ct-steps li { display:flex; gap:20px; padding:20px 0; border-bottom:1px solid var(--border); }
        .hb-ct-steps b { color:var(--g); font-family:Georgia,serif; font-size:24px; font-weight:400; line-height:1; }
        .hb-ct-steps span { color:var(--muted); font-size:13px; line-height:1.7; }
        .hb-ct-steps strong { display:block; margin-bottom:4px; color:#f1ece0; font-family:Georgia,serif; font-size:17px; font-weight:400; }
        .hb-ct-side-box { padding:26px; border:1px solid var(--border); background:var(--panel); }
        .hb-ct-side-box .hb-ct-btn:first-of-type { margin-top:0; }
        .hb-ct-socials { margin-top:34px; }
        .hb-ct-socials div { display:flex; flex-wrap:wrap; gap:10px; }
        .hb-ct-socials a { padding:9px 16px; border:1px solid var(--border); background:rgba(0,0,0,.25); color:#e8e2d4; font-size:12px; text-decoration:none; transition:.25s; }
        .hb-ct-socials a:hover { border-color:var(--g); color:var(--gl); background:rgba(216,180,94,.08); }

        /* ---------- CARTE ---------- */
        .hb-ct-map { position:relative; height:520px; background:#10100e; border-top:1px solid var(--border); }
        .hb-ct-map iframe { display:block; width:100%; height:100%; border:0; transition:filter .4s; }
        .hb-ct-map iframe.is-dark { filter:grayscale(1) invert(.92) contrast(.88) sepia(.35) hue-rotate(5deg); }
        .hb-ct-map-overlay { position:absolute; inset:0; display:flex; align-items:center; pointer-events:none; left:0; right:0; margin:0 auto; }
        .hb-ct-map-card { width:min(100%,340px); padding:28px; border:1px solid var(--border); background:rgba(11,11,9,.94); backdrop-filter:blur(10px); box-shadow:0 25px 60px rgba(0,0,0,.5); pointer-events:auto; }
        .hb-ct-map-card h3 { margin:0 0 10px; font-family:Georgia,serif; font-size:26px; font-weight:400; color:#f6f1e7; }
        .hb-ct-map-card > p:not(.hb-ct-kicker) { margin:0; color:#c0bcb2; font-size:13px; line-height:1.7; }
        .hb-ct-map-hours { display:flex; align-items:center; gap:8px; margin-top:12px !important; color:var(--gl) !important; }
        .hb-ct-toggle { display:block; width:100%; margin-top:12px; padding:0; border:0; background:none; color:var(--muted); font:inherit; font-size:11px; text-decoration:underline; cursor:pointer; }
        .hb-ct-toggle:hover { color:var(--gl); }

        @keyframes hb-ct-in { from { opacity:0; transform:translateY(22px); } to { opacity:1; transform:none; } }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width:1100px){ .hb-ct-cards { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (max-width:900px){
            .hb-ct-grid { grid-template-columns:1fr; gap:40px; }
            .hb-ct-side { position:static; }
            .hb-ct-map { height:auto; }
            .hb-ct-map iframe { height:380px; }
            .hb-ct-map-overlay { position:static; display:block; padding:0 20px; margin-top:-30px; }
            .hb-ct-map-card { width:100%; }
        }
        @media (max-width:600px){
            .hb-ct-hero-inner { padding-top:95px; padding-bottom:95px; }
            .hb-ct-cards { grid-template-columns:1fr; }
            .hb-ct-form { padding:26px 20px; }
            .hb-ct-row { grid-template-columns:1fr; }
            .hb-ct-submit { width:100%; justify-self:stretch; }
        }
        @media (prefers-reduced-motion:reduce){
            .hb-ct-card, .hb-ct-btn, .hb-ct-submit { animation:none; transition:none; }
            .hb-ct-hero-video { display:none; }
        }
    </style>
</x-hb-layout>
