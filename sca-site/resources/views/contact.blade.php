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

    {{-- Barre de progression dorée --}}
    <div class="hb-ct-progress" aria-hidden="true"><span></span></div>

    {{-- ============ HERO (plein écran) : texte à gauche sur fond noir, photo / vidéo à droite ============ --}}
    <section class="hb-ct-hero">
        @if($cover || $heroVideo)
            <div class="hb-ct-hero-media" aria-hidden="true">
                @if($cover)
                    <div class="hb-ct-hero-bg" style="background-image:url('{{ $cover }}')"></div>
                @endif
                @if($heroVideo)
                    <video class="hb-ct-hero-video" autoplay muted loop playsinline preload="metadata" disablepictureinpicture>
                        <source src="{{ $heroVideo }}" type="video/mp4">
                    </video>
                @endif
                <div class="hb-ct-hero-shade"></div>
            </div>
        @endif

        <div class="hb-wrap hb-ct-hero-inner">
            <div class="hb-ct-hero-content">
                <nav class="hb-ct-crumbs hb-ct-in" style="--i:0" aria-label="Fil d’Ariane">
                    <a href="{{ route('home') }}">Accueil</a><span>/</span><b>Contact</b>
                </nav>
                <p class="hb-eyebrow hb-ct-in" style="--i:1"><i class="hb-ct-eyebrow-line"></i>CONTACT</p>
                <h1 class="hb-ct-in" style="--i:2">Parlons de<br><span>votre projet</span></h1>
                <p class="hb-ct-lead hb-ct-in" style="--i:3">Décrivez votre besoin : nos ingénieurs étudient votre demande et reviennent vers vous.</p>

                <div class="hb-ct-hero-actions hb-ct-in" style="--i:4">
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
        </div>

        <a href="#ctForm" class="hb-ct-scroll" aria-label="Aller au formulaire"><span></span></a>
    </section>

    {{-- ============ CARTES RAPIDES ============ --}}
    <section class="hb-ct-cards-wrap">
        <div class="hb-wrap">
            <div class="hb-ct-cards">
                <div class="hb-ct-card hb-ct-rv" style="--d:0s">
                    <span class="hb-ct-ico"><x-icon name="phone" :size="20" /></span>
                    <small>TÉLÉPHONE</small>
                    @foreach($ct['phones'] as $p)
                        <a href="tel:{{ preg_replace('/\s+/', '', $p) }}">{{ $p }}</a>
                    @endforeach
                </div>

                <div class="hb-ct-card hb-ct-rv" style="--d:.1s">
                    <span class="hb-ct-ico"><x-icon name="mail" :size="20" /></span>
                    <small>E-MAIL</small>
                    <a href="mailto:{{ $ct['email'] }}">{{ $ct['email'] }}</a>
                </div>

                <div class="hb-ct-card hb-ct-rv" style="--d:.2s">
                    <span class="hb-ct-ico"><x-icon name="map-pin" :size="20" /></span>
                    <small>ADRESSE</small>
                    <a href="{{ $mapLink }}" target="_blank" rel="noreferrer">{{ $ct['address'] }}</a>
                </div>

                <div class="hb-ct-card hb-ct-rv" style="--d:.3s">
                    <span class="hb-ct-ico"><x-icon name="clock" :size="20" /></span>
                    <small>HORAIRES</small>
                    <span class="hb-ct-text">{{ $ct['hours'] }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FORMULAIRE ============ --}}
    <section class="hb-ct-main" id="ctForm">
        <div class="hb-wrap hb-ct-grid">

            <form method="POST" action="{{ route('contact.store') }}" class="hb-ct-form hb-ct-rv" style="--d:0s">
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
                    <span class="hb-ct-submit-label">Envoyer le message</span> <x-icon name="arrow-right" :size="16" />
                </button>
            </form>

            {{-- Panneau latéral --}}
            <aside class="hb-ct-side">
                <p class="hb-ct-kicker hb-ct-rv" style="--d:.1s">COMMENT ÇA SE PASSE</p>
                <ol class="hb-ct-steps">
                    <li class="hb-ct-rv" style="--d:.2s"><b>01</b><span><strong>Vous nous écrivez</strong>Décrivez votre site et votre besoin.</span></li>
                    <li class="hb-ct-rv" style="--d:.35s"><b>02</b><span><strong>Nous étudions</strong>Nos ingénieurs analysent votre demande.</span></li>
                    <li class="hb-ct-rv" style="--d:.5s"><b>03</b><span><strong>Nous vous répondons</strong>Proposition technique adaptée à votre bâtiment.</span></li>
                </ol>

                <div class="hb-ct-side-box hb-ct-rv" style="--d:.2s">
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
                    <div class="hb-ct-socials hb-ct-rv" style="--d:.3s">
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

    {{-- ============ CARTE : TOUJOURS EN BAS DE PAGE ============ --}}
    <section class="hb-ct-location hb-ct-rv" style="--d:0s" aria-labelledby="ctMapTitle">
        <div class="hb-wrap hb-ct-location-head">
            <div>
                <p class="hb-ct-kicker">NOUS TROUVER</p>
                <h2 id="ctMapTitle">Passons à votre rencontre.</h2>
                <p>Retrouvez le bureau HN BLOOM à Casablanca et préparez votre itinéraire.</p>
            </div>
            <a href="{{ $dirLink }}" target="_blank" rel="noreferrer" class="hb-ct-btn is-outline">
                <x-icon name="map-pin" :size="16" /> Ouvrir l’itinéraire
            </a>
        </div>

        <div class="hb-ct-map" x-data="{ dark: true }">
            <iframe
                title="Localisation du bureau HN BLOOM sur Google Maps"
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
        </div>
    </section>

</div>{{-- /hb-ct-page --}}

    @include('partials.hb-cta', ['heading' => 'Engagez-vous dans l’Excellence avec HN BLOOM'])

    <style>
        /* ====== FOND DE LA PAGE (contact.jpg) ======
           Calque fixe : fluide (pas de background-attachment:fixed, lent et cassé sur mobile) */
        .hb-ct-page { position:relative; isolation:isolate; background:#090908; font-family:Arial,Helvetica,sans-serif; }
        .hb-ct-page::before { content:""; position:fixed; inset:0; z-index:-2; background:var(--ct-bg, none) center / cover no-repeat; }
        .hb-ct-page::after { content:""; position:fixed; inset:0; z-index:-1; background:linear-gradient(180deg, rgba(9,9,8,.72) 0%, rgba(9,9,8,.88) 100%); }

        .hb-ct-hero, .hb-ct-cards-wrap, .hb-ct-main, .hb-ct-map {
            --g:#d8b45e; --gl:#f0d69a; --muted:#b1ada3; --border:rgba(216,180,94,.25); --panel:rgba(16,16,14,.92);
            color:#f6f1e7;
        }

        /* ---------- HERO : PLEIN ÉCRAN (texte à gauche, photo / vidéo à droite) ---------- */
        .hb-ct-hero { position:relative; overflow:hidden; min-height:clamp(590px, 76vh, 790px); display:flex; align-items:center; background:#090908; }

        /* Lumière dorée douce derrière le texte (côté noir) */
        .hb-ct-hero::before { content:""; position:absolute; inset:0; z-index:1; pointer-events:none;
            background:radial-gradient(ellipse 50% 70% at 20% 50%, rgba(216,180,94,.14) 0%, rgba(216,180,94,.04) 45%, transparent 70%); }

        /* Photo / vidéo : zone de droite avec fondu vers la gauche.
           Décalage de l'image : "30% center" -> 15% pour aller plus à droite, 50% plus à gauche. */
        .hb-ct-hero-media { position:absolute; top:0; bottom:0; right:0; width:58%; z-index:0; background:#090908; overflow:hidden;
            -webkit-mask-image:linear-gradient(90deg, transparent 0%, rgba(0,0,0,.3) 16%, rgba(0,0,0,.8) 36%, #000 52%);
                    mask-image:linear-gradient(90deg, transparent 0%, rgba(0,0,0,.3) 16%, rgba(0,0,0,.8) 36%, #000 52%);
            animation:hbCtFade 1.4s .1s ease-out backwards; }
        .hb-ct-hero-bg { position:absolute; inset:0; background-size:cover; background-position:30% center; background-repeat:no-repeat; animation:hbCtZoom 2.2s .2s cubic-bezier(.2,.7,.2,1) backwards; }
        .hb-ct-hero-video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:30% center; display:block; opacity:0; transition:opacity .8s ease; }
        .hb-ct-hero-video.is-on { opacity:1; }
        .hb-ct-hero-shade { position:absolute; inset:0; pointer-events:none; background:linear-gradient(180deg, rgba(9,9,8,.45) 0%, rgba(9,9,8,0) 35%, rgba(9,9,8,.7) 100%); }

        .hb-ct-hero-inner { position:relative; z-index:2; width:100%; padding-top:105px; padding-bottom:105px; }
        .hb-ct-hero-content { max-width:620px; }
        .hb-ct-crumbs { display:flex; gap:10px; align-items:center; margin-bottom:28px; font-size:11px; color:#d0ccc1; }
        .hb-ct-crumbs a { color:#d0ccc1; text-decoration:none; transition:color .2s; } .hb-ct-crumbs a:hover { color:var(--gl); }
        .hb-ct-crumbs b { color:var(--gl); font-weight:400; } .hb-ct-crumbs span { opacity:.5; }
        .hb-ct-hero .hb-eyebrow { display:flex; align-items:center; gap:14px; margin:0 0 18px; color:var(--gl); font:700 10.5px/1.5 Arial,Helvetica,sans-serif; letter-spacing:4px; text-transform:uppercase; }
        .hb-ct-eyebrow-line { display:inline-block; flex:0 0 40px; height:1px; background:var(--g); transform-origin:left; animation:hbCtRule 1s .6s ease-out both; }
        .hb-ct-hero h1 { margin:0; font-family:Georgia,"Times New Roman",serif; font-size:clamp(32px,3.7vw,52px); font-weight:400; line-height:1.15; letter-spacing:-.6px; color:#fffdf7; text-shadow:0 2px 24px rgba(0,0,0,.4); }
        .hb-ct-hero h1 span { display:block; color:var(--gl); font-style:italic; }
        .hb-ct-lead { max-width:520px; margin:20px 0 0; color:#e8e4db; font:400 13.5px/1.9 Arial,Helvetica,sans-serif; text-shadow:0 1px 12px rgba(0,0,0,.5); }
        .hb-ct-hero-actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:30px; }

        /* Indicateur de défilement (au-dessus du chevauchement des cartes) */
        .hb-ct-scroll { position:absolute; z-index:2; left:50%; bottom:100px; display:block; width:1px; height:52px; overflow:hidden; background:rgba(240,214,154,.25); transform:translateX(-50%); animation:hbCtFade 1s 1.8s backwards; }
        .hb-ct-scroll span { display:block; width:100%; height:100%; background:var(--gl); animation:hbCtScroll 2.2s 2.2s ease-in-out infinite; }

        /* ---------- BOUTONS ---------- */
        .hb-ct-btn { position:relative; overflow:hidden; display:inline-flex; align-items:center; justify-content:center; gap:12px; min-height:48px; padding:0 24px; border:1px solid var(--g); background:var(--g); color:#15130d; font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; text-decoration:none; cursor:pointer; transition:background .3s, color .3s, transform .3s; }
        .hb-ct-btn:hover { background:transparent; color:var(--gl); transform:translateY(-2px); }
        .hb-ct-btn.is-outline { background:rgba(0,0,0,.4); color:var(--gl); }
        .hb-ct-btn.is-outline:hover { background:var(--g); color:#15130d; }
        .hb-ct-btn.is-wide { width:100%; margin-top:12px; }
        .hb-ct-btn::after, .hb-ct-submit::after { content:""; position:absolute; top:0; bottom:0; left:-70%; width:40%; pointer-events:none; background:linear-gradient(100deg,transparent,rgba(255,244,214,.35),transparent); transform:skewX(-18deg); }
        .hb-ct-btn:hover::after, .hb-ct-submit:hover::after { left:130%; transition:left .9s ease; }

        /* ---------- CARTES RAPIDES ---------- */
        .hb-ct-cards-wrap { position:relative; z-index:2; margin-top:-48px; background:transparent; }
        .hb-ct-cards { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
        .hb-ct-card { position:relative; overflow:hidden; padding:26px 24px 24px; border:1px solid var(--border); background:var(--panel); box-shadow:0 20px 50px rgba(0,0,0,.35); transition:border-color .4s, transform .4s, box-shadow .5s; }
        .hb-ct-card:hover { border-color:var(--g); transform:translateY(-6px); box-shadow:0 30px 60px -20px rgba(0,0,0,.7), 0 0 40px -10px rgba(216,180,94,.35); }
        .hb-ct-card::before { content:""; position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg,transparent,var(--gl),transparent); transform:scaleX(0); transition:transform .6s cubic-bezier(.2,.7,.2,1); }
        .hb-ct-card:hover::before { transform:scaleX(1); }
        .hb-ct-card::after { content:""; position:absolute; inset:0; pointer-events:none; opacity:0; transition:opacity .4s; background:radial-gradient(300px circle at var(--mx,50%) var(--my,50%), rgba(240,214,154,.13), transparent 60%); }
        .hb-ct-card:hover::after { opacity:1; }
        .hb-ct-ico { display:grid; width:46px; height:46px; place-items:center; margin-bottom:20px; border:1px solid var(--border); border-radius:50%; color:var(--gl); transition:background .35s, color .35s, transform .6s ease; }
        .hb-ct-card:hover .hb-ct-ico { background:var(--g); color:#15130d; transform:rotate(360deg); }
        .hb-ct-card small { display:block; margin-bottom:10px; color:var(--g); font-size:9px; font-weight:600; letter-spacing:2px; }
        .hb-ct-card a, .hb-ct-text { position:relative; z-index:1; display:block; margin-top:4px; color:#f1ece0; font-family:Georgia,serif; font-size:16px; line-height:1.5; text-decoration:none; overflow-wrap:anywhere; transition:color .3s; }
        .hb-ct-card a:hover { color:var(--gl); }

        /* ---------- FORMULAIRE ---------- */
        .hb-ct-main { padding:72px 0 86px; background:linear-gradient(180deg,rgba(9,9,8,.35),rgba(9,9,8,.82)); }
        .hb-ct-grid { display:grid; grid-template-columns:minmax(0,1.55fr) minmax(300px,.95fr); gap:28px; align-items:stretch; }
        .hb-ct-form { position:relative; display:grid; align-content:start; gap:19px; padding:clamp(24px,3vw,36px); border:1px solid var(--border); background:linear-gradient(145deg,rgba(18,18,15,.97),rgba(8,8,7,.97)); box-shadow:0 24px 70px rgba(0,0,0,.22); }
        .hb-ct-kicker { margin:0 0 14px; color:var(--g); font-size:10px; font-weight:600; letter-spacing:2.2px; }
        .hb-ct-form-head h2 { margin:0 0 6px; font-family:Georgia,serif; font-size:clamp(26px,3vw,36px); font-weight:400; line-height:1.2; color:#f6f1e7; }
        .hb-ct-row { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
        .hb-ct-label { display:block; margin-bottom:8px; color:var(--muted); font-size:10px; letter-spacing:.14em; text-transform:uppercase; transition:color .25s; }
        .hb-ct-label small { text-transform:none; letter-spacing:0; opacity:.8; }
        .hb-ct-row > div:focus-within .hb-ct-label, .hb-ct-form > div:focus-within > .hb-ct-label { color:var(--gl); }
        .hb-ct-input { box-sizing:border-box; width:100%; min-height:46px; padding:13px 14px; border:1px solid var(--border); border-radius:2px; background:#0b0b09; color:#f6f1e7; font:inherit; font-size:13px; transition:border-color .25s, background .25s, box-shadow .3s; }
        .hb-ct-input::placeholder { color:#77746d; }
        .hb-ct-input:focus-visible, .hb-ct-btn:focus-visible, .hb-ct-submit:focus-visible { outline:2px solid var(--gl); outline-offset:3px; }
        .hb-ct-input:focus { outline:none; border-color:var(--g); background:#0e0e0b; box-shadow:0 0 0 3px rgba(216,180,94,.13), 0 0 24px -8px rgba(216,180,94,.45); }
        select.hb-ct-input { appearance:none; background-image:linear-gradient(45deg,transparent 50%,var(--g) 50%),linear-gradient(135deg,var(--g) 50%,transparent 50%); background-position:calc(100% - 20px) 50%,calc(100% - 14px) 50%; background-size:6px 6px; background-repeat:no-repeat; padding-right:40px; }
        select.hb-ct-input option { background:#10100e; color:#f6f1e7; }
        textarea.hb-ct-input { resize:vertical; min-height:140px; }
        .hb-ct-submit { position:relative; overflow:hidden; display:inline-flex; align-items:center; justify-content:center; gap:16px; justify-self:start; min-height:52px; padding:0 30px; border:1px solid var(--g); background:linear-gradient(110deg,#c7983e,#f0d58e); color:#15130d; font:inherit; font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; cursor:pointer; transition:transform .3s, filter .3s, opacity .3s; }
        .hb-ct-submit:hover { transform:translateY(-2px); filter:brightness(1.07); }
        .hb-ct-submit svg { transition:transform .3s; }
        .hb-ct-submit:hover svg { transform:translateX(5px); }
        .hb-ct-submit:disabled { opacity:.7; cursor:wait; }
        .hb-ct-alert { display:flex; align-items:center; gap:12px; padding:14px 16px; border:1px solid var(--g); background:rgba(216,180,94,.08); color:#f1ece0; font-size:14px; animation:hbCtUp .7s both; }
        .hb-ct-alert.is-err { border-color:#d9534f; background:rgba(217,83,79,.08); }
        .hb-ct-main .hb-error { margin:6px 0 0; color:#ef8a86; font-size:12px; }

        /* ---------- PANNEAU LATÉRAL ---------- */
        .hb-ct-side { position:relative; overflow:hidden; padding:30px; border:1px solid var(--border); background:linear-gradient(145deg,rgba(16,16,13,.97),rgba(8,8,7,.96)); }
        .hb-ct-steps { margin:0 0 34px; padding:0; list-style:none; border-top:1px solid var(--border); }
        .hb-ct-steps li { display:flex; gap:20px; padding:20px 0; border-bottom:1px solid var(--border); transition:padding .4s; }
        .hb-ct-steps li:hover { padding-left:8px; }
        .hb-ct-steps b { color:var(--g); font-family:Georgia,serif; font-size:24px; font-weight:400; line-height:1; transition:color .3s; }
        .hb-ct-steps li:hover b { color:var(--gl); }
        .hb-ct-steps span { color:var(--muted); font-size:13px; line-height:1.7; }
        .hb-ct-steps strong { display:block; margin-bottom:4px; color:#f1ece0; font-family:Georgia,serif; font-size:17px; font-weight:400; }
        .hb-ct-side-box { padding:26px; border:1px solid var(--border); background:var(--panel); }
        .hb-ct-side-box .hb-ct-btn:first-of-type { margin-top:0; }
        .hb-ct-socials { margin-top:34px; }
        .hb-ct-socials div { display:flex; flex-wrap:wrap; gap:10px; }
        .hb-ct-socials a { padding:9px 16px; border:1px solid var(--border); background:rgba(0,0,0,.25); color:#e8e2d4; font-size:12px; text-decoration:none; transition:.3s; }
        .hb-ct-socials a:hover { border-color:var(--g); color:var(--gl); background:rgba(216,180,94,.08); transform:translateY(-2px); }

        /* ---------- LOCALISATION : SECTION SÉPARÉE SOUS LE FORMULAIRE ---------- */
        .hb-ct-location { --g:#d8b45e; --gl:#f0d69a; --muted:#b1ada3; --border:rgba(216,180,94,.25); color:#f6f1e7; padding-top:10px; background:#090908; }
        .hb-ct-location-head { display:flex; align-items:flex-end; justify-content:space-between; gap:28px; padding-top:22px; padding-bottom:30px; }
        .hb-ct-location-head h2 { margin:0; color:#f6f1e7; font:400 clamp(28px,3.4vw,42px)/1.15 Georgia,"Times New Roman",serif; }
        .hb-ct-location-head > div > p:last-child { margin:12px 0 0; color:var(--muted); font-size:13px; line-height:1.8; }
        .hb-ct-map { position:relative; height:480px; background:#10100e; border-top:1px solid var(--border); border-bottom:1px solid var(--border); }
        .hb-ct-map iframe { display:block; width:100%; height:100%; border:0; transition:filter .4s; }
        .hb-ct-map iframe.is-dark { filter:grayscale(1) invert(.92) contrast(.88) sepia(.35) hue-rotate(5deg); }
        .hb-ct-map-overlay { position:absolute; inset:0; display:flex; align-items:center; pointer-events:none; left:0; right:0; margin:0 auto; }
        .hb-ct-map-card { width:min(100%,340px); padding:28px; border:1px solid var(--border); background:rgba(11,11,9,.96); box-shadow:0 25px 60px rgba(0,0,0,.5); pointer-events:auto; }
        .hb-ct-map.is-in .hb-ct-map-card { animation:hbCtSlide 1.1s .4s cubic-bezier(.2,.7,.2,1) backwards; }
        .hb-ct-map-card h3 { margin:0 0 10px; font-family:Georgia,serif; font-size:26px; font-weight:400; color:#f6f1e7; }
        .hb-ct-map-card > p:not(.hb-ct-kicker) { margin:0; color:#c0bcb2; font-size:13px; line-height:1.7; }
        .hb-ct-map-hours { display:flex; align-items:center; gap:8px; margin-top:12px !important; color:var(--gl) !important; }
        .hb-ct-toggle { display:block; width:100%; margin-top:12px; padding:0; border:0; background:none; color:var(--muted); font:inherit; font-size:11px; text-decoration:underline; cursor:pointer; }
        .hb-ct-toggle:hover { color:var(--gl); }

        /* ====== ANIMATIONS ====== */
        html { scroll-behavior:smooth; }
        @keyframes hbCtZoom   { from { transform:scale(1.2); } to { transform:scale(1); } }
        @keyframes hbCtRule   { from { transform:scaleX(0); } to { transform:scaleX(1); } }
        @keyframes hbCtFade   { from { opacity:0; } to { opacity:1; } }
        @keyframes hbCtUp     { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:none; } }
        @keyframes hbCtRise   { from { opacity:0; transform:translateY(46px) scale(.97); } to { opacity:1; transform:none; } }
        @keyframes hbCtSlide  { from { opacity:0; transform:translateX(-40px); } to { opacity:1; transform:none; } }
        @keyframes hbCtScroll { 0% { transform:translateY(-100%); } 60%,100% { transform:translateY(100%); } }

        .hb-ct-in { animation:hbCtUp 1s calc(.15s + var(--i,0) * .16s) cubic-bezier(.2,.7,.2,1) backwards; }

        .hb-ct-rv:not(.is-in) { opacity:0; }
        .hb-ct-rv.is-in { animation:hbCtRise 1.1s var(--d,0s) cubic-bezier(.2,.7,.2,1) backwards; }
        .hb-ct-map.hb-ct-rv.is-in { animation:hbCtFade 1.2s both; }

        .hb-ct-progress { position:fixed; z-index:9999; top:0; left:0; right:0; height:2px; pointer-events:none; }
        .hb-ct-progress span { display:block; width:100%; height:100%; background:linear-gradient(90deg,#d8b45e,#f0d69a); transform:scaleX(0); transform-origin:left; }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width:1100px){ .hb-ct-cards { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media (max-width:1050px){
            /* Écran moyen : photo un peu plus large mais atténuée */
            .hb-ct-hero-media { width:64%; opacity:.8; }
        }
        @media (max-width:900px){
            .hb-ct-grid { grid-template-columns:1fr; gap:24px; }
            .hb-ct-side { position:static; }
            .hb-ct-location-head { align-items:flex-start; flex-direction:column; }
            .hb-ct-map { height:420px; }
            .hb-ct-map-overlay { position:absolute; display:flex; padding:0 20px; margin:0; }
            .hb-ct-map-card { width:min(100%,340px); }
        }
        @media (max-width:700px){
            /* Mobile : photo / vidéo en plein fond, atténuée pour garder le texte lisible */
            .hb-ct-hero-media { width:100%; opacity:.4;
                -webkit-mask-image:linear-gradient(180deg, #000 0%, rgba(0,0,0,.6) 70%, transparent 100%);
                        mask-image:linear-gradient(180deg, #000 0%, rgba(0,0,0,.6) 70%, transparent 100%); }
            .hb-ct-hero-bg, .hb-ct-hero-video { background-position:center center; object-position:center center; }
        }
        @media (max-width:600px){
            .hb-ct-hero { min-height:610px; }
            .hb-ct-hero-inner { padding-top:90px; padding-bottom:100px; }
            .hb-ct-hero h1 { font-size:clamp(36px,10vw,48px); }
            .hb-ct-lead { max-width:100%; }
            .hb-ct-cards { grid-template-columns:1fr; gap:10px; }
            .hb-ct-card { display:grid; grid-template-columns:48px 1fr; column-gap:15px; align-items:center; padding:18px; }
            .hb-ct-ico { grid-row:span 2; margin:0; }
            .hb-ct-card small { margin:0 0 3px; }
            .hb-ct-card a, .hb-ct-text { margin-top:0; font-size:14px; }
            .hb-ct-form { padding:24px 18px; }
            .hb-ct-row { grid-template-columns:1fr; gap:18px; }
            .hb-ct-submit { width:100%; justify-self:stretch; }
            .hb-ct-map { height:360px; }
            .hb-ct-map-overlay { align-items:flex-end; padding:0 14px 14px; }
            .hb-ct-map-card { width:100%; padding:18px; }
            .hb-ct-map-card h3 { font-size:22px; }
        }
        @media (prefers-reduced-motion:reduce){
            .hb-ct-rv:not(.is-in) { opacity:1; }
            .hb-ct-in, .hb-ct-rv, .hb-ct-rv *, .hb-ct-hero-media, .hb-ct-hero-bg, .hb-ct-scroll, .hb-ct-scroll span, .hb-ct-eyebrow-line, .hb-ct-alert { animation:none !important; }
            .hb-ct-card, .hb-ct-btn, .hb-ct-submit, .hb-ct-ico { transition:none; }
            .hb-ct-hero-video { display:none; }
            html { scroll-behavior:auto; }
        }
    </style>
    <noscript><style>.hb-ct-rv:not(.is-in){opacity:1 !important}</style></noscript>

    <script>
        (function () {
            var reduce = window.matchMedia('(prefers-reduced-motion:reduce)').matches;

            /* ---------- Vidéo hero : affichage seulement si elle démarre ; pause hors écran ---------- */
            var v = document.querySelector('.hb-ct-hero-video');
            if (v) {
                v.addEventListener('playing', function () { v.classList.add('is-on'); });
                v.addEventListener('error', function () { v.remove(); });
                var src = v.querySelector('source');
                if (src) src.addEventListener('error', function () { v.remove(); });
                var saver = navigator.connection && navigator.connection.saveData;
                if (saver) {
                    v.remove();
                } else {
                    var p = v.play();
                    if (p && p.catch) p.catch(function () {});
                    if ('IntersectionObserver' in window) {
                        new IntersectionObserver(function (entries) {
                            entries.forEach(function (e) {
                                if (e.isIntersecting) { var pp = v.play(); if (pp && pp.catch) pp.catch(function () {}); }
                                else { v.pause(); }
                            });
                        }, { threshold: 0.1 }).observe(v);
                    }
                }
            }

            /* ---------- Apparition au scroll ---------- */
            var els = Array.prototype.slice.call(document.querySelectorAll('.hb-ct-rv'));
            if ('IntersectionObserver' in window && !reduce) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
                els.forEach(function (el) { io.observe(el); });
            } else {
                els.forEach(function (el) { el.classList.add('is-in'); });
            }

            /* ---------- Barre de progression ---------- */
            var bar = document.querySelector('.hb-ct-progress span');
            if (bar && !reduce) {
                var ticking = false;
                var upd = function () {
                    var h = document.documentElement.scrollHeight - window.innerHeight;
                    bar.style.transform = 'scaleX(' + (h > 0 ? Math.min(window.scrollY / h, 1) : 0) + ')';
                    ticking = false;
                };
                window.addEventListener('scroll', function () {
                    if (!ticking) { ticking = true; requestAnimationFrame(upd); }
                }, { passive: true });
                upd();
            }

            /* ---------- Halo doré qui suit la souris sur les cartes ---------- */
            if (!reduce && window.matchMedia('(hover:hover) and (pointer:fine)').matches) {
                document.querySelectorAll('.hb-ct-card').forEach(function (card) {
                    var raf = 0, ev = null;
                    var apply = function () {
                        raf = 0;
                        var r = card.getBoundingClientRect();
                        card.style.setProperty('--mx', ((ev.clientX - r.left) / r.width * 100) + '%');
                        card.style.setProperty('--my', ((ev.clientY - r.top) / r.height * 100) + '%');
                    };
                    card.addEventListener('pointermove', function (e) { ev = e; if (!raf) raf = requestAnimationFrame(apply); }, { passive: true });
                });
            }

            /* ---------- Bouton d'envoi : état "envoi en cours" (évite les doubles envois) ---------- */
            var form = document.querySelector('.hb-ct-form');
            if (form) {
                form.addEventListener('submit', function () {
                    var b = form.querySelector('.hb-ct-submit');
                    if (!b) return;
                    var l = b.querySelector('.hb-ct-submit-label');
                    if (l) l.textContent = 'Envoi en cours…';
                    setTimeout(function () { b.disabled = true; }, 0);
                });
            }

            /* ---------- Le message de succès / erreur est amené à l'écran ---------- */
            var alert = document.querySelector('.hb-ct-alert');
            if (alert) {
                setTimeout(function () {
                    alert.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' });
                }, 300);
            }
        })();
    </script>
</x-hb-layout>