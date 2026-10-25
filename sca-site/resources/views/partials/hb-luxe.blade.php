{{-- =====================================================================
     HN BLOOM — DESIGN SYSTEM LUXE (partagé par toutes les pages)
     Fichier : resources/views/partials/hb-luxe.blade.php
     Usage   : @include('partials.hb-luxe')   (en bas de chaque page)
     Les pages utilisent la classe .lx-page sur leur conteneur principal.
     ===================================================================== --}}
<style>
    /* ================= BASE & TOKENS ================= */
    .lx-page {
        --g:#d6ad55; --gl:#f1d58e; --panel:#10100e; --border:rgba(214,173,85,.25);
        --muted:#b1b0a8; --ink:#f5f1e8;
        --serif:Georgia,"Times New Roman",serif; --sans:Arial,Helvetica,sans-serif;
        position:relative; isolation:isolate; background:#090908; color:var(--ink); font-family:var(--sans);
    }
    .lx-page *, .lx-page *::before, .lx-page *::after { box-sizing:border-box; }
    /* Fond fixe (fluide, compatible mobile) */
    .lx-page::before { content:""; position:fixed; inset:0; z-index:-2; background:var(--lx-bg, none) center / cover no-repeat; }
    .lx-page::after  { content:""; position:fixed; inset:0; z-index:-1; background:linear-gradient(180deg,rgba(9,9,8,.78),rgba(9,9,8,.9)); }
    html { scroll-behavior:smooth; }

    /* ================= TYPOGRAPHIE UNIQUE ================= */
    .lx-eyebrow { display:flex; align-items:center; gap:14px; margin:0 0 18px; color:var(--gl); font:700 10.5px/1.5 var(--sans); letter-spacing:4px; text-transform:uppercase; }
    .lx-eyebrow::before { content:""; flex:0 0 38px; height:1px; background:#dfb95f; transform-origin:left; animation:lxRule 1s .5s ease-out both; }
    .lx-h1 { margin:0; color:#fffdf7; font:400 clamp(32px,3.7vw,52px)/1.15 var(--serif); letter-spacing:-.6px; text-shadow:0 2px 20px rgba(0,0,0,.45); }
    .lx-h1 span { display:block; margin-top:4px; color:#f2cf7b; font-style:italic; }
    .lx-h2 { margin:0; color:#f8f4eb; font:400 clamp(34px,4vw,49px)/1.12 var(--serif); letter-spacing:-.7px; }
    .lx-h2 span { display:block; color:var(--gl); }
    .lx-h3 { margin:0; color:#f5e4bd; font:400 clamp(24px,2.4vw,30px)/1.25 var(--serif); }
    .lx-lead { max-width:520px; margin:20px 0 0; color:#e8e4db; font:400 13.5px/1.9 var(--sans); letter-spacing:.1px; text-shadow:0 1px 12px rgba(0,0,0,.55); }
    .lx-text { color:var(--muted); font:400 13px/1.95 var(--sans); }
    .lx-label { margin:0 0 14px; color:var(--g); font:700 9.5px/1.4 var(--sans); letter-spacing:2.2px; text-transform:uppercase; }

    /* ================= BOUTONS & LIENS ================= */
    .lx-btn { position:relative; overflow:hidden; display:inline-flex; align-items:center; justify-content:center; gap:18px; min-height:48px; padding:0 24px; border:1px solid rgba(223,185,95,.8); background:rgba(9,9,8,.45); color:var(--gl); font:700 11px/1.4 var(--sans); letter-spacing:2px; text-decoration:none; text-transform:uppercase; cursor:pointer; transition:background .3s,color .3s,transform .3s,opacity .3s; }
    .lx-btn:hover { background:#e0b95f; color:#15120b; transform:translateY(-2px); }
    .lx-btn.is-solid { background:var(--g); border-color:var(--g); color:#17130a; }
    .lx-btn.is-solid:hover { background:transparent; color:var(--gl); }
    .lx-btn.is-wide { width:100%; }
    .lx-btn svg { display:block; flex:none; width:19px; height:19px; transition:transform .3s; }
    .lx-btn:hover svg { transform:translateX(5px); }
    .lx-btn::after { content:""; position:absolute; top:0; bottom:0; left:-70%; width:40%; pointer-events:none; background:linear-gradient(100deg,transparent,rgba(255,244,214,.35),transparent); transform:skewX(-18deg); }
    .lx-btn:hover::after { left:130%; transition:left .9s ease; }
    .lx-btn:disabled { opacity:.7; cursor:wait; }
    .lx-link { display:inline-flex; align-items:center; gap:12px; color:var(--gl); font:700 11px/1.4 var(--sans); letter-spacing:2px; text-decoration:none; text-transform:uppercase; transition:gap .3s,color .3s; }
    .lx-link:hover { gap:18px; color:#fff1cf; }
    .lx-actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:28px; }

    /* ================= HERO PLEIN ÉCRAN ================= */
    .lx-hero { position:relative; isolation:isolate; min-height:100vh; min-height:100svh; display:flex; align-items:center; overflow:hidden; padding:110px clamp(25px,5vw,80px); background:#090908; }
    .lx-hero-media { position:absolute; z-index:-2; inset:0; background-color:#151511; background-position:center; background-size:cover; animation:lxZoom 10s ease-out both; will-change:transform; }
    .lx-hero-media::after { content:""; position:absolute; inset:0; background:
        linear-gradient(90deg,rgba(5,6,5,.62) 0%,rgba(5,6,5,.22) 45%,rgba(5,6,5,.08) 100%),
        linear-gradient(0deg,rgba(5,6,5,.55) 0%,rgba(5,6,5,0) 40%),
        linear-gradient(180deg,rgba(5,6,5,.35) 0%,rgba(5,6,5,0) 22%); }
    .lx-video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
    .lx-hero-inner { display:grid; grid-template-columns:minmax(0,1fr); align-items:center; gap:clamp(30px,4vw,70px); width:100%; max-width:1400px; margin:0 auto; }
    .lx-hero-inner.has-side { grid-template-columns:minmax(0,1fr) 330px; }
    .lx-hero-content { max-width:640px; }
    .lx-crumbs { display:flex; gap:10px; align-items:center; margin-bottom:26px; font:400 11px/1.4 var(--sans); letter-spacing:.4px; color:#d0ccc1; }
    .lx-crumbs a { color:#d0ccc1; text-decoration:none; transition:color .2s; }
    .lx-crumbs a:hover { color:var(--gl); }
    .lx-crumbs b { color:var(--gl); font-weight:400; }
    .lx-crumbs span { opacity:.5; }
    .lx-count { display:inline-block; margin:26px 0 0; padding-top:14px; border-top:1px solid var(--border); color:var(--gl); font:400 15px/1.4 var(--serif); letter-spacing:1px; }
    .lx-scroll { position:absolute; z-index:2; left:50%; bottom:28px; display:block; width:1px; height:56px; overflow:hidden; background:rgba(242,211,138,.25); transform:translateX(-50%); animation:lxFade 1s 1.6s backwards; }
    .lx-scroll span { display:block; width:100%; height:100%; background:#f2d38a; animation:lxScroll 2.2s 2s ease-in-out infinite; }

    /* Entrée du hero en cascade */
    .lx-in { animation:lxUp 1s calc(.15s + var(--i,0) * .16s) cubic-bezier(.2,.7,.2,1) backwards; }

    /* ================= SECTIONS ================= */
    .lx-section { position:relative; padding:80px 6% 95px; background:rgba(9,9,8,.45); }
    .lx-section::before { content:""; position:absolute; top:0; left:6%; right:6%; height:1px; background:linear-gradient(90deg,transparent,var(--g),transparent); animation:lxRule 1.6s .2s ease-out both; }
    .lx-wrap { max-width:1250px; margin:0 auto; }
    .lx-head { display:flex; align-items:flex-end; justify-content:space-between; gap:40px; margin-bottom:38px; }
    .lx-head .lx-eyebrow { margin-bottom:18px; }
    .lx-head-text { max-width:360px; margin:0 0 4px; }

    /* ================= GRILLE & CARTES ================= */
    .lx-grid { display:grid; gap:22px; grid-template-columns:repeat(var(--cols,3),minmax(0,1fr)); }
    .lx-grid.cols-2 { --cols:2; } .lx-grid.cols-3 { --cols:3; } .lx-grid.cols-4 { --cols:4; } .lx-grid.cols-5 { --cols:5; gap:14px; }
    .lx-grid.is-auto { grid-template-columns:repeat(auto-fit,minmax(230px,1fr)); }

    .lx-card { position:relative; display:flex; flex-direction:column; min-width:0; overflow:hidden; border:1px solid var(--border); background:var(--panel); color:inherit; text-decoration:none; transform:perspective(1100px) rotateX(var(--rx,0deg)) rotateY(var(--ry,0deg)); transition:border-color .4s,background .4s,transform .35s ease-out,box-shadow .5s; }
    .lx-card:hover { transform:perspective(1100px) translateY(-8px) rotateX(var(--rx,0deg)) rotateY(var(--ry,0deg)); border-color:rgba(241,213,142,.8); background:#13130f; box-shadow:0 30px 60px -20px rgba(0,0,0,.7),0 0 40px -10px rgba(214,173,85,.35); }
    .lx-card::before { content:""; position:absolute; z-index:3; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg,transparent,#f1d58e,transparent); transform:scaleX(0); transition:transform .6s cubic-bezier(.2,.7,.2,1); }
    .lx-card:hover::before { transform:scaleX(1); }
    .lx-card::after { content:""; position:absolute; inset:0; z-index:2; pointer-events:none; opacity:0; transition:opacity .4s; background:radial-gradient(420px circle at var(--mx,50%) var(--my,50%),rgba(241,213,142,.14),transparent 60%); }
    .lx-card:hover::after { opacity:1; }
    .lx-card:focus-visible { outline:2px solid var(--g); outline-offset:3px; }

    .lx-media { position:relative; height:clamp(200px,20vw,270px); overflow:hidden; background:#1a1915; }
    .lx-media img, .lx-media video { display:block; width:100%; height:100%; object-fit:cover; transition:transform .8s cubic-bezier(.2,.7,.2,1),filter .4s; }
    .lx-card:hover .lx-media img, .lx-card:hover .lx-media video { transform:scale(1.06); filter:brightness(1.06); }
    .lx-media::after { content:""; position:absolute; top:0; bottom:0; left:-70%; width:45%; pointer-events:none; background:linear-gradient(100deg,transparent,rgba(255,244,214,.28),transparent); transform:skewX(-18deg); }
    .lx-card:hover .lx-media::after { left:130%; transition:left 1s ease; }
    .lx-badge { position:absolute; z-index:1; top:17px; left:17px; display:grid; width:37px; height:37px; place-items:center; border:1px solid rgba(241,213,142,.65); background:rgba(9,9,8,.82); color:var(--gl); font:400 11px/1 var(--sans); transition:transform .4s,background .3s,color .3s; }
    .lx-card:hover .lx-badge { transform:translateY(-3px); background:var(--g); color:#11100c; }
    .lx-tag { position:absolute; z-index:1; bottom:17px; left:17px; padding:7px 11px; border:1px solid rgba(241,213,142,.65); background:rgba(9,9,8,.82); color:var(--gl); font:400 10px/1.2 var(--sans); letter-spacing:1.2px; transition:transform .4s; }
    .lx-card:hover .lx-tag { transform:translateX(4px); }
    .lx-arrow { position:absolute; z-index:1; right:17px; bottom:17px; display:grid; width:42px; height:42px; place-items:center; border:1px solid rgba(241,213,142,.75); background:rgba(9,9,8,.75); color:var(--gl); transition:background .25s,color .25s; }
    .lx-card:hover .lx-arrow { background:var(--g); color:#11100c; }

    .lx-body { display:flex; flex:1; flex-direction:column; align-items:flex-start; padding:26px 26px 24px; }
    .lx-meta { display:flex; align-items:center; gap:13px; margin-bottom:20px; }
    .lx-icon { display:grid; flex:0 0 40px; width:40px; height:40px; place-items:center; border:1px solid var(--border); border-radius:50%; color:var(--gl); transition:background .35s,color .35s,transform .5s; }
    .lx-card:hover .lx-icon { background:var(--g); color:#11100c; transform:rotate(360deg); animation:lxPulse 1.2s ease-out; }
    .lx-cat { color:var(--g); font:700 9px/1.4 var(--sans); letter-spacing:1.8px; text-transform:uppercase; }
    .lx-body .lx-h3 { transition:color .3s,letter-spacing .5s; }
    .lx-card:hover .lx-body .lx-h3 { color:#fff1cf; letter-spacing:.4px; }
    .lx-desc { min-height:46px; margin:13px 0 18px; color:var(--muted); font:400 12px/1.9 var(--sans); }
    .lx-feats { display:grid; gap:11px; width:100%; padding:17px 0; border-top:1px solid var(--border); border-bottom:1px solid var(--border); }
    .lx-feats span { display:flex; align-items:flex-start; gap:9px; color:#ded9ce; font:400 11px/1.6 var(--sans); }
    .lx-feats svg { flex:0 0 13px; margin-top:2px; color:var(--g); transition:transform .35s; }
    .lx-feats .is-more { color:var(--g); font-size:10px; letter-spacing:.6px; }
    .lx-cta { position:relative; overflow:hidden; z-index:0; display:flex; align-items:center; justify-content:space-between; gap:14px; width:100%; min-height:48px; margin-top:auto; padding:0 18px; border:1px solid var(--g); background:var(--g); color:#17130a; font:700 12px/1.3 var(--sans); transition:color .25s; }
    .lx-feats + .lx-cta, .lx-desc + .lx-cta { margin-top:22px; }
    .lx-cta::before { content:""; position:absolute; z-index:-1; inset:0; background:#f1d58e; transform:translateX(-101%); transition:transform .5s cubic-bezier(.7,0,.2,1); }
    .lx-card:hover .lx-cta::before { transform:none; }
    .lx-cta svg { transition:transform .25s; }
    .lx-card:hover .lx-cta svg { transform:translateX(5px); }

    /* Variante compacte (5 cartes sur une ligne) */
    .lx-card.is-compact .lx-media { height:clamp(150px,14vw,200px); }
    .lx-card.is-compact .lx-body { padding:20px 16px 18px; }
    .lx-card.is-compact .lx-h3 { font-size:clamp(18px,1.6vw,21px); }
    .lx-card.is-compact .lx-desc { min-height:0; font-size:11px; line-height:1.8; }
    .lx-card.is-compact .lx-meta { margin-bottom:14px; }
    .lx-card.is-compact .lx-cta { min-height:42px; padding:0 14px; font-size:11px; }

    /* ================= ANIMATIONS ================= */
    @keyframes lxUp     { from { opacity:0; transform:translateY(26px); } to { opacity:1; transform:none; } }
    @keyframes lxLeft   { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:none; } }
    @keyframes lxRise   { from { opacity:0; transform:translateY(48px) scale(.97); } to { opacity:1; transform:none; } }
    @keyframes lxPop    { from { opacity:0; transform:scale(.94); } to { opacity:1; transform:none; } }
    @keyframes lxRule   { from { transform:scaleX(0); } to { transform:scaleX(1); } }
    @keyframes lxZoom   { from { transform:scale(1.08); } to { transform:scale(1); } }
    @keyframes lxFade   { from { opacity:0; } to { opacity:1; } }
    @keyframes lxScroll { 0% { transform:translateY(-100%); } 60%,100% { transform:translateY(100%); } }
    @keyframes lxClip   { from { clip-path:inset(0 0 100% 0); } to { clip-path:inset(0 0 0 0); } }
    @keyframes lxClipL  { from { clip-path:inset(0 100% 0 0); } to { clip-path:inset(0 0 0 0); } }
    @keyframes lxImgIn  { from { transform:scale(1.25); } to { transform:scale(1); } }
    @keyframes lxItem   { from { opacity:0; transform:translateX(-14px); } to { opacity:1; transform:none; } }
    @keyframes lxPulse  { 0% { box-shadow:0 0 0 0 rgba(241,213,142,.45); } 100% { box-shadow:0 0 0 14px rgba(241,213,142,0); } }

    /* Apparition au scroll */
    .lx-rv:not(.is-in) { opacity:0; }
    .lx-rv.is-in { animation:lxRise 1.1s var(--d,0s) cubic-bezier(.2,.7,.2,1) backwards; }
    .lx-rv.is-clip.is-in { animation:lxClipL 1.5s var(--d,0s) cubic-bezier(.7,0,.2,1) backwards; }
    .lx-rv.is-pop { animation:lxPop .55s cubic-bezier(.2,.7,.2,1) backwards; }
    .lx-card.is-in .lx-media { animation:lxClip 1.3s calc(var(--d,0s) + .15s) cubic-bezier(.7,0,.2,1) backwards; }
    .lx-card.is-in .lx-media img, .lx-card.is-in .lx-media video { animation:lxImgIn 1.8s calc(var(--d,0s) + .15s) cubic-bezier(.2,.7,.2,1) backwards; }
    .lx-card.is-in .lx-feats span { animation:lxItem .7s backwards cubic-bezier(.2,.7,.2,1); }
    .lx-card.is-in .lx-feats span:nth-child(1) { animation-delay:calc(var(--d,0s) + .7s); }
    .lx-card.is-in .lx-feats span:nth-child(2) { animation-delay:calc(var(--d,0s) + .82s); }
    .lx-card.is-in .lx-feats span:nth-child(3) { animation-delay:calc(var(--d,0s) + .94s); }
    .lx-card.is-in .lx-feats span:nth-child(4) { animation-delay:calc(var(--d,0s) + 1.06s); }

    /* Barre de progression dorée */
    .lx-progress { position:fixed; z-index:9999; top:0; left:0; right:0; height:2px; pointer-events:none; }
    .lx-progress span { display:block; width:100%; height:100%; background:linear-gradient(90deg,#d6ad55,#f1d58e); transform:scaleX(0); transform-origin:left; }

    /* ================= RESPONSIVE ================= */
    @media (max-width:1100px) {
        .lx-hero { padding-inline:4%; }
        .lx-hero-inner.has-side { grid-template-columns:minmax(0,1fr) 295px; gap:24px; }
        .lx-grid.cols-5 { --cols:3; }
        .lx-grid.cols-4 { --cols:2; }
    }
    @media (max-width:1000px) { .lx-grid.cols-3 { --cols:2; } }
    @media (max-width:900px) { .lx-head { flex-direction:column; align-items:flex-start; gap:17px; } }
    @media (max-width:760px) {
        .lx-hero { padding:100px 21px 80px; }
        .lx-hero-media { animation:none; will-change:auto; }
        .lx-hero-inner.has-side { display:flex; flex-direction:column; align-items:stretch; gap:34px; }
        .lx-hero-content { width:100%; max-width:none; }
        .lx-eyebrow { gap:10px; margin-bottom:16px; font-size:9.5px; letter-spacing:2.5px; }
        .lx-eyebrow::before { flex-basis:26px; }
        .lx-h1 { font-size:clamp(30px,8.2vw,40px); }
        .lx-lead { font-size:13px; line-height:1.85; }
        .lx-card { transform:none !important; }
    }
    @media (max-width:620px) {
        .lx-section { padding:60px 6%; }
        .lx-grid, .lx-grid.cols-2, .lx-grid.cols-3, .lx-grid.cols-4, .lx-grid.cols-5 { --cols:1; gap:20px; }
        .lx-grid.cols-5 { --cols:2; gap:12px; }
        .lx-media { height:235px; }
        .lx-desc { min-height:0; }
    }
    @media (prefers-reduced-motion:reduce) {
        .lx-rv:not(.is-in) { opacity:1; }
        .lx-page *, .lx-page *::before, .lx-page *::after { animation:none !important; }
        .lx-card { transform:none !important; }
        html { scroll-behavior:auto; }
    }
</style>
<noscript><style>.lx-rv:not(.is-in){opacity:1 !important}</style></noscript>

<script>
(function () {
    var reduce = window.matchMedia('(prefers-reduced-motion:reduce)').matches;
    var all = function (s) { return Array.prototype.slice.call(document.querySelectorAll(s)); };

    /* 1) Apparition au scroll */
    var rv = all('.lx-rv');
    if (!('IntersectionObserver' in window) || reduce) {
        rv.forEach(function (el) { el.classList.add('is-in'); });
    } else {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
        rv.forEach(function (el) { io.observe(el); });
    }

    /* 2) Vidéos : ne tournent que lorsqu'elles sont visibles (site rapide) */
    var vids = all('.lx-video, .lx-vid');
    var saver = navigator.connection && navigator.connection.saveData;
    vids.forEach(function (v) {
        v.muted = true;
        v.addEventListener('error', function () { if (v.classList.contains('lx-video')) v.remove(); });
        var s = v.querySelector('source');
        if (s) s.addEventListener('error', function () { if (v.classList.contains('lx-video')) v.remove(); });
    });
    if (saver) {
        all('.lx-video').forEach(function (v) {
            var poster = v.getAttribute('poster'), m = v.parentNode;
            if (poster && m) m.style.backgroundImage = "url('" + poster + "')";
            v.remove();
        });
    } else if ('IntersectionObserver' in window) {
        var vio = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                var v = e.target;
                if (e.isIntersecting) { var p = v.play(); if (p && p.catch) p.catch(function () {}); }
                else { v.pause(); }
            });
        }, { threshold: 0.15 });
        vids.forEach(function (v) { vio.observe(v); });
    } else {
        vids.forEach(function (v) { var p = v.play && v.play(); if (p && p.catch) p.catch(function () {}); });
    }

    /* 3) Barre de progression */
    var bar = document.querySelector('.lx-progress span');
    if (bar && !reduce) {
        var ticking = false;
        var upd = function () {
            var h = document.documentElement.scrollHeight - window.innerHeight;
            bar.style.transform = 'scaleX(' + (h > 0 ? Math.min(window.scrollY / h, 1) : 0) + ')';
            ticking = false;
        };
        window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(upd); } }, { passive: true });
        upd();
    }

    /* 4) Inclinaison 3D + halo doré sur les cartes (souris uniquement) */
    if (!reduce && window.matchMedia('(hover:hover) and (pointer:fine)').matches) {
        all('.lx-card').forEach(function (card) {
            var raf = 0, ev = null;
            var apply = function () {
                raf = 0;
                var r = card.getBoundingClientRect();
                var x = (ev.clientX - r.left) / r.width, y = (ev.clientY - r.top) / r.height;
                card.style.setProperty('--mx', (x * 100) + '%');
                card.style.setProperty('--my', (y * 100) + '%');
                card.style.setProperty('--ry', ((x - .5) * 6) + 'deg');
                card.style.setProperty('--rx', ((.5 - y) * 6) + 'deg');
            };
            card.addEventListener('pointermove', function (e) { ev = e; if (!raf) raf = requestAnimationFrame(apply); }, { passive: true });
            card.addEventListener('pointerleave', function () {
                card.style.setProperty('--rx', '0deg');
                card.style.setProperty('--ry', '0deg');
            });
        });
    }
})();
</script>
