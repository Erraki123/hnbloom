{{-- HN BLOOM — Appartement intelligent (maquette réaliste : image sombre + image éclairée révélée pièce par pièce) --}}
@php
    $img = fn ($f) => asset('images/smart-apartment/'.$f);
    $vid = fn ($f) => asset('videos/smart-apartment/'.$f);
@endphp

<section class="ha" id="ha" aria-label="Démonstration appartement intelligent">
    <div class="ha-shell">

        {{-- ===================== SCÈNE ===================== --}}
        <div class="ha-stage" id="haStage">
            <div class="ha-view">
                <img class="ha-base" src="{{ $img('dark.webp') }}" alt="Appartement moderne en coupe, vue isométrique" draggable="false">
                <div id="haLayers" data-lit="{{ $img('lit.webp') }}"></div>
                <svg class="ha-sv" id="haSv" viewBox="0 0 1536 1024" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M122 180 L192 172 L198 730 L110 745Z"/>
                    <path d="M235 760 L660 735 L662 795 L250 815Z"/>
                    <path d="M770 680 L1030 670 L1032 720 L770 735Z"/>
                    <path d="M1165 710 L1375 705 L1375 765 L1165 775Z"/>
                    <path d="M1140 130 L1200 150 L1195 260 L1145 230Z"/>
                </svg>
                <div id="haHot"></div>
            </div>
            <canvas class="ha-cv" id="haCv"></canvas>
            <div class="ha-chips" id="haChips"></div>
            <div class="ha-toast" id="haToast" role="status"></div>
            <p class="ha-hint">Touchez une pièce pour la piloter · Utilisez le téléphone pour tout contrôler</p>

            <div class="ha-vid" id="haVid"
                 data-intrusion="{{ $vid('intrusion.mp4') }}" data-intrusion-poster="{{ $img('intrusion-poster.jpg') }}"
                 data-arrival="{{ $vid('arrival.mp4') }}" data-arrival-poster="{{ $img('arrival-poster.jpg') }}">
                <video id="haVideo" playsinline preload="none"></video>
                <span class="ha-vt" id="haVt"></span>
                <button type="button" class="ha-vx" id="haVx" aria-label="Fermer la vidéo">✕</button>
            </div>
        </div>

        {{-- ===================== TÉLÉPHONE ===================== --}}
        <aside class="ha-phonewrap">
            <div class="ha-phone">
                <i class="ha-notch"></i>
                <div class="ha-scr">
                    <div class="ha-ah"><div><small>SMART G4</small><h3>Mon Appartement</h3></div><span class="ha-live">● En ligne</span></div>
                    <div class="ha-ban" id="haBan" hidden>🚨 Intrusion détectée à l’entrée — Police alertée</div>

                    <div class="ha-rooms" id="haRooms"></div>

                    <div class="ha-card">
                        <div class="ha-r"><span class="ha-i">☼</span><div><b>Éclairage</b><small id="haLSt"></small></div><button type="button" class="ha-sw" id="haL" role="switch" aria-checked="false" aria-label="Éclairage"><i></i></button></div>
                        <div class="ha-rg"><label for="haLv">Intensité</label><output id="haLvO">80%</output></div>
                        <input type="range" id="haLv" min="10" max="100" value="80">
                    </div>

                    <div class="ha-card">
                        <div class="ha-r"><span class="ha-i">▤</span><div><b>Volets &amp; baies vitrées</b><small id="haVSt"></small></div><button type="button" class="ha-sw" id="haV" role="switch" aria-checked="true" aria-label="Volets"><i></i></button></div>
                        <div class="ha-rg"><label for="haVv">Ouverture</label><output id="haVvO">100%</output></div>
                        <input type="range" id="haVv" min="0" max="100" value="100">
                    </div>

                    <div class="ha-card">
                        <div class="ha-r"><span class="ha-i">❄</span><div><b>Climatisation</b><small id="haTSt"></small></div></div>
                        <div class="ha-clim"><button type="button" id="haTd" aria-label="Diminuer">−</button><strong id="haTv">22°</strong><button type="button" id="haTu" aria-label="Augmenter">+</button></div>
                        <div class="ha-modes"><button type="button" data-m="cool">❄ Froid</button><button type="button" data-m="auto">⟳ Auto</button><button type="button" data-m="heat">☀ Chaud</button><button type="button" data-m="fan">≋ Ventil.</button></div>
                    </div>

                    <div class="ha-card">
                        <div class="ha-r"><span class="ha-i">♫</span><div><b>Sonorisation</b><small id="haSSt">Inactive</small></div><button type="button" class="ha-sw" id="haS" role="switch" aria-checked="false" aria-label="Sonorisation"><i></i></button></div>
                        <div class="ha-np"><div class="ha-art" id="haArt">♪</div><div><b id="haTt"></b><small id="haTa"></small></div></div>
                        <div class="ha-bar"><i id="haPg"></i></div>
                        <div class="ha-tm"><span id="haPt">0:00</span><span id="haDt">0:00</span></div>
                        <div class="ha-ctl"><button type="button" id="haPv" aria-label="Précédent">⏮</button><button type="button" id="haPl" class="ha-play" aria-label="Lecture">▶</button><button type="button" id="haNx" aria-label="Suivant">⏭</button></div>
                        <div class="ha-vol"><span>♪</span><input type="range" id="haVol" min="0" max="100" value="60" aria-label="Volume"><output id="haVolO">60%</output></div>
                        <ul class="ha-pl" id="haPls"></ul>
                    </div>

                    <div class="ha-card">
                        <div class="ha-r"><span class="ha-i">⚡</span><div><b>Énergie</b><small id="haWSt">0 W</small></div></div>
                        <div class="ha-en" id="haEn"></div>
                        <small class="ha-sv2">Économie estimée ce mois : <b>−18 %</b></small>
                    </div>

                    <div class="ha-card">
                        <div class="ha-r"><span class="ha-i">◇</span><div><b>Sécurité</b><small id="haSecSt"></small></div><button type="button" class="ha-sw" id="haSec" role="switch" aria-checked="false" aria-label="Alarme"><i></i></button></div>
                        <div class="ha-btns">
                            <button type="button" id="haIntr" class="ha-danger">🚨 Simuler une intrusion</button>
                            <button type="button" id="haArr">🔓 Simuler une arrivée</button>
                            <button type="button" id="haOff" hidden>Désactiver l’alarme</button>
                        </div>
                    </div>

                    <div class="ha-scn">
                        <span>SCÉNARIOS</span>
                        <div>
                            <button type="button" data-sc="confort">☼ Confort</button>
                            <button type="button" data-sc="cinema">▶ Cinéma</button>
                            <button type="button" data-sc="nuit">☾ Nuit</button>
                            <button type="button" data-sc="depart">⇥ Départ</button>
                        </div>
                    </div>
                    <p class="ha-note">Interface de démonstration : aucun appareil réel n’est commandé.</p>
                </div>
            </div>
        </aside>
    </div>
</section>

<script>
(function () {
    'use strict';
    var $ = function (id) { return document.getElementById(id); };
    var qa = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
    var stage = $('haStage');

    /* ---------- pièces : clé, nom, centre x/y et rayons (px sur 1536×1024) ---------- */
    var R = [
        ['entree', 'Entrée', 640, 260, 95, 125], ['cuisine', 'Cuisine', 360, 240, 235, 150],
        ['salon', 'Salon & salle à manger', 400, 560, 300, 235], ['bureau', 'Bureau', 770, 190, 95, 95],
        ['sdb1', 'Salle d’eau', 880, 190, 85, 100], ['ch1', 'Chambre 1', 1050, 190, 145, 100],
        ['ch2', 'Chambre 2', 770, 520, 135, 180], ['ch3', 'Chambre 3', 970, 520, 115, 150],
        ['suite', 'Suite parentale', 1210, 520, 155, 190], ['sdb2', 'Salle de bain', 1390, 420, 100, 130],
        ['terrasse', 'Terrasse', 640, 850, 470, 75]
    ];
    var S = {}, L = {}, H = {}, KEYS = R.map(function (r) { return r[0]; });
    var sel = null, sh = 100, mode = '', temp = 22, sound = false, trk = 0, pos = 0, vol = 60, armed = false, alarm = false;
    KEYS.forEach(function (k) { S[k] = { on: false, lv: 80 }; });

    /* ---------- calques lumineux (image éclairée masquée en ellipse douce) ---------- */
    var layers = $('haLayers'), hot = $('haHot'), roomsBar = $('haRooms'), lit = layers.dataset.lit;
    function chip(label, key) {
        var b = document.createElement('button'); b.type = 'button'; b.textContent = label; b.dataset.k = key;
        b.addEventListener('click', function () { pick(key === '*' ? null : key); });
        roomsBar.appendChild(b);
    }
    chip('Toute la maison', '*');
    R.forEach(function (r) {
        var k = r[0], m = 'radial-gradient(ellipse ' + (r[4] / 15.36) + '% ' + (r[5] / 10.24) + '% at ' + (r[2] / 15.36) + '% ' + (r[3] / 10.24) + '%, #000 55%, transparent 100%)';
        var d = document.createElement('div'); d.className = 'ha-L'; d.style.backgroundImage = 'url(' + lit + ')';
        d.style.webkitMaskImage = m; d.style.maskImage = m; layers.appendChild(d); L[k] = d;
        var h = document.createElement('button'); h.type = 'button'; h.className = 'ha-hot'; h.dataset.n = r[1]; h.setAttribute('aria-label', r[1]);
        h.style.left = (r[2] / 15.36) + '%'; h.style.top = (r[3] / 10.24) + '%';
        h.addEventListener('click', function () { pick(sel === k ? null : k); });
        hot.appendChild(h); H[k] = h; chip(r[1], k);
    });
    function pick(k) { sel = k; render(); }
    function targets() { return sel ? [sel] : KEYS; }
    function nameOf(k) { return R.filter(function (r) { return r[0] === k; })[0][1]; }
    function sw(el, on) { el.classList.toggle('is-on', on); el.setAttribute('aria-checked', on ? 'true' : 'false'); }

    /* ---------- contrôles ---------- */
    $('haL').addEventListener('click', function () {
        var t = targets(), on = t.some(function (k) { return S[k].on; });
        t.forEach(function (k) { S[k].on = !on; }); render();
    });
    $('haLv').addEventListener('input', function () { var v = +this.value; targets().forEach(function (k) { S[k].lv = v; if (!S[k].on) S[k].on = true; }); render(); });
    $('haV').addEventListener('click', function () { sh = sh > 0 ? 0 : 100; render(); });
    $('haVv').addEventListener('input', function () { sh = +this.value; render(); });
    $('haTd').addEventListener('click', function () { temp = Math.max(16, temp - 1); render(); });
    $('haTu').addEventListener('click', function () { temp = Math.min(30, temp + 1); render(); });
    qa('[data-m]').forEach(function (b) { b.addEventListener('click', function () { mode = (mode === b.dataset.m) ? '' : b.dataset.m; render(); }); });
    $('haSec').addEventListener('click', function () { armed = !armed; render(); });

    /* ---------- scénarios ---------- */
    function all(on, lv) { KEYS.forEach(function (k) { S[k].on = on; S[k].lv = lv || 80; }); }
    var SC = {
        confort: function () { all(true, 80); sh = 100; mode = ''; sound = false; armed = false; },
        cinema: function () { all(false); S.salon.on = true; S.salon.lv = 25; sh = 0; mode = 'cool'; temp = 21; sound = true; trk = 1; pos = 0; },
        nuit: function () { all(false); S.entree.on = true; S.entree.lv = 15; sh = 0; mode = ''; sound = false; armed = true; },
        depart: function () { all(false); sh = 0; mode = ''; sound = false; armed = true; }
    };
    qa('[data-sc]').forEach(function (b) { b.addEventListener('click', function () { SC[b.dataset.sc](); sel = null; render(); }); });

    /* ---------- vidéos : intrusion / arrivée ---------- */
    var vb = $('haVid'), v = $('haVideo'), vmode = null, resume = false, tt = null;
    function toast(msg) { var t = $('haToast'); t.textContent = msg; t.classList.add('show'); clearTimeout(tt); tt = setTimeout(function () { t.classList.remove('show'); }, 5200); }
    function openVid(kind) {
        if (vmode) return;
        vmode = kind; resume = sound; sound = false; stage.style.transform = '';
        $('haVt').textContent = kind === 'intrusion' ? '● REC · Caméra d’entrée' : '● Arrivée détectée · Porte d’entrée';
        v.poster = vb.dataset[kind + 'Poster']; v.src = vb.dataset[kind]; vb.classList.add('on'); v.currentTime = 0; v.muted = false;
        var p = v.play(); if (p && p.catch) p.catch(function () { v.muted = true; v.play(); });
        render();
    }
    function closeVid(done) {
        v.pause(); vb.classList.remove('on');
        var k = vmode; vmode = null; sound = resume;
        if (k === 'intrusion') { stage.classList.remove('alarm'); if (done) { alarm = true; toast('🚨 Intrusion détectée — la police a été alertée'); } }
        if (k === 'arrival' && done) { SC.confort(); toast('Bienvenue à la maison — scénario Confort activé'); }
        render();
    }
    v.addEventListener('ended', function () { closeVid(true); });
    v.addEventListener('timeupdate', function () { if (vmode === 'intrusion') stage.classList.toggle('alarm', v.currentTime >= 2); });
    $('haVx').addEventListener('click', function () { closeVid(false); });
    $('haIntr').addEventListener('click', function () { openVid('intrusion'); });
    $('haArr').addEventListener('click', function () { openVid('arrival'); });
    $('haOff').addEventListener('click', function () { alarm = false; stage.classList.remove('alarm'); toast('Alarme désactivée'); render(); });

    /* ---------- musique : lecteur + moteur WebAudio (aucun fichier audio) ---------- */
    var TR = [['Midnight Drive', 'Neon Waves', 212], ['Ocean Breeze', 'Lounge Collective', 187], ['Atlas', 'Deep Piano', 243], ['Café Marrakech', 'Oud Sessions', 198]];
    var SCL = [[0, 3, 5, 7, 10], [0, 2, 4, 7, 9], [0, 2, 3, 7, 8], [0, 1, 4, 5, 7]], ROOT = [196, 220, 174.6, 233], BPM = [84, 66, 54, 96];
    var AC = null, mg, playing = false, tmr = null, stp = 0, lastTrk = -1, pls = $('haPls');
    function fm(x) { return Math.floor(x / 60) + ':' + ('0' + (x % 60)).slice(-2); }
    TR.forEach(function (t, i) {
        var li = document.createElement('li');
        li.innerHTML = '<b>' + (i + 1) + '</b><span>' + t[0] + '<small>' + t[1] + '</small></span><em>' + fm(t[2]) + '</em>';
        li.addEventListener('click', function () { trk = i; pos = 0; sound = true; render(); });
        pls.appendChild(li);
    });
    function go(d) { trk = (trk + d + TR.length) % TR.length; pos = 0; render(); }
    $('haPv').addEventListener('click', function () { go(-1); });
    $('haNx').addEventListener('click', function () { go(1); });
    $('haPl').addEventListener('click', function () { sound = !sound; render(); });
    $('haS').addEventListener('click', function () { sound = !sound; render(); });
    $('haVol').addEventListener('input', function () { vol = +this.value; render(); });
    setInterval(function () { if (sound) { pos++; if (pos >= TR[trk][2]) go(1); else render(); } }, 1000);
    function note(f, d, ty, g) {
        var t = AC.currentTime, o = AC.createOscillator(), n = AC.createGain();
        o.type = ty; o.frequency.value = f; n.gain.setValueAtTime(0, t); n.gain.linearRampToValueAtTime(g, t + .06);
        n.gain.exponentialRampToValueAtTime(.0001, t + d); o.connect(n); n.connect(mg); o.start(t); o.stop(t + d + .1);
    }
    function loop() {
        var sc = SCL[trk], r = ROOT[trk], f = function (d, o) { return r * o * Math.pow(2, d / 12); };
        note(f(sc[Math.floor(Math.random() * sc.length)], Math.random() < .3 ? 2 : 1), 1.8, 'triangle', .2);
        if (stp % 4 === 0) [0, sc[2], sc[4]].forEach(function (d) { note(f(d, .5), 3.6, 'sine', .13); });
        stp++; tmr = setTimeout(loop, 60000 / BPM[trk]);
    }
    function audio() {
        var Ctx = window.AudioContext || window.webkitAudioContext;
        if (sound && !AC && Ctx) {
            AC = new Ctx(); mg = AC.createGain(); var lp = AC.createBiquadFilter(); lp.type = 'lowpass'; lp.frequency.value = 1800;
            mg.connect(lp); lp.connect(AC.destination);
        }
        if (!AC) return;
        mg.gain.value = vol / 100 * .6;
        if (sound && (!playing || lastTrk !== trk)) { clearTimeout(tmr); AC.resume(); playing = true; lastTrk = trk; stp = 0; loop(); }
        if (!sound && playing) { clearTimeout(tmr); playing = false; }
    }

    /* ---------- effet climatisation (canvas) ---------- */
    var cv = $('haCv'), cx = cv.getContext('2d'), P = [], i0;
    for (i0 = 0; i0 < 90; i0++) P.push({ x: Math.random(), y: Math.random(), r: .6 + Math.random() * 2.2, s: .0006 + Math.random() * .0016 });
    function fit() { cv.width = stage.clientWidth; cv.height = stage.clientHeight; }
    fit(); window.addEventListener('resize', fit);
    var CL = { cool: ['190,225,255', 1], auto: ['170,240,220', .5], heat: ['255,165,85', -1], fan: ['220,235,255', 0] };
    (function frame() {
        cx.clearRect(0, 0, cv.width, cv.height);
        var c = CL[mode];
        if (c && !document.hidden) {
            var g = cx.createRadialGradient(cv.width / 2, cv.height / 2, 0, cv.width / 2, cv.height / 2, cv.width * .7);
            g.addColorStop(0, 'rgba(' + c[0] + ',0)'); g.addColorStop(1, 'rgba(' + c[0] + ',.16)');
            cx.fillStyle = g; cx.fillRect(0, 0, cv.width, cv.height);
            P.forEach(function (q) {
                cx.beginPath(); cx.fillStyle = 'rgba(' + c[0] + ',.55)';
                if (mode === 'fan') { q.x += q.s * 5; if (q.x > 1) q.x = 0; cx.fillRect(q.x * cv.width, q.y * cv.height, q.r * 14, 1); }
                else { q.y += q.s * c[1]; q.x += Math.sin(q.y * 14) * .0004; if (q.y > 1) q.y = 0; if (q.y < 0) q.y = 1; cx.arc(q.x * cv.width, q.y * cv.height, q.r, 0, 6.3); }
                cx.fill();
            });
        }
        requestAnimationFrame(frame);
    })();

    /* ---------- énergie : barres de la semaine ---------- */
    var WEEK = [62, 55, 71, 48, 66, 52], en = $('haEn');
    WEEK.forEach(function (h, i) { en.innerHTML += '<span style="--h:' + h + '%"><i></i><em>' + 'LMMJVS'.charAt(i) + '</em></span>'; });
    en.innerHTML += '<span class="now" id="haNow" style="--h:40%"><i></i><em>Auj.</em></span>';

    /* ---------- rendu ---------- */
    function render() {
        KEYS.forEach(function (k) {
            L[k].style.opacity = S[k].on ? S[k].lv / 100 : 0;
            H[k].classList.toggle('on', S[k].on); H[k].classList.toggle('sel', sel === k);
        });
        qa('button', roomsBar).forEach(function (b) { b.classList.toggle('is-active', (b.dataset.k === '*' && !sel) || b.dataset.k === sel); });
        var t = targets(), on = t.filter(function (k) { return S[k].on; }), nOn = KEYS.filter(function (k) { return S[k].on; }).length;
        sw($('haL'), on.length > 0);
        var avg = on.length ? Math.round(on.reduce(function (a, k) { return a + S[k].lv; }, 0) / on.length) : S[t[0]].lv;
        $('haLv').value = avg; $('haLvO').textContent = avg + '%';
        $('haLSt').textContent = (sel ? nameOf(sel) : 'Toute la maison') + ' · ' + on.length + '/' + t.length + ' allumé(s)';
        sw($('haV'), sh > 0); $('haVv').value = sh; $('haVvO').textContent = sh + '%';
        $('haVSt').textContent = sh > 0 ? 'Ouverts' : 'Fermés';
        qa('#haSv path').forEach(function (p) { p.style.opacity = (100 - sh) / 100; });
        $('haTv').textContent = temp + '°';
        $('haTSt').textContent = (mode ? { cool: 'Froid', auto: 'Auto', heat: 'Chaud', fan: 'Ventilation' }[mode] : 'Arrêtée') + ' · consigne ' + temp + ' °C';
        qa('[data-m]').forEach(function (b) { b.classList.toggle('is-active', b.dataset.m === mode); });
        var tr = TR[trk];
        sw($('haS'), sound); $('haSSt').textContent = sound ? 'Lecture · Tout l’appartement' : 'Inactive';
        $('haTt').textContent = tr[0]; $('haTa').textContent = tr[1]; $('haPl').textContent = sound ? '❚❚' : '▶';
        $('haPg').style.width = (pos / tr[2] * 100) + '%'; $('haPt').textContent = fm(pos); $('haDt').textContent = fm(tr[2]);
        $('haVol').value = vol; $('haVolO').textContent = vol + '%'; $('haArt').classList.toggle('is-play', sound);
        qa('li', pls).forEach(function (li, i) { li.classList.toggle('is-active', i === trk); });
        var W = 60 + (mode ? 850 : 0) + (sound ? 15 : 0) + KEYS.reduce(function (a, k) { return a + (S[k].on ? 45 * S[k].lv / 100 : 0); }, 0);
        $('haWSt').textContent = 'Puissance actuelle : ' + Math.round(W) + ' W'; $('haNow').style.setProperty('--h', Math.min(100, W / 14) + '%');
        sw($('haSec'), armed); $('haSecSt').textContent = alarm ? 'ALARME DÉCLENCHÉE' : (armed ? 'Armée · tout est surveillé' : 'Désarmée');
        $('haBan').hidden = !alarm; $('haOff').hidden = !alarm;
        $('haChips').innerHTML = '<span>☼ ' + nOn + '/' + KEYS.length + ' pièces</span><span>❄ ' + temp + '°C</span><span class="' + (armed && !alarm ? 'ok' : (alarm ? 'bad' : '')) + '">◇ ' + (alarm ? 'ALARME' : (armed ? 'Sécurisé' : 'Non armé')) + '</span>' + (sound ? '<span class="ok">♫ ' + tr[0] + '</span>' : '');
        audio();
    }

    /* ---------- inclinaison 3D douce ---------- */
    stage.addEventListener('pointermove', function (e) {
        if (e.pointerType !== 'mouse' || vmode) return;
        var r = stage.getBoundingClientRect(), px = (e.clientX - r.left) / r.width - .5, py = (e.clientY - r.top) / r.height - .5;
        stage.style.transform = 'perspective(1500px) rotateX(' + (-py * 3.5) + 'deg) rotateY(' + (px * 5) + 'deg)';
    });
    stage.addEventListener('pointerleave', function () { stage.style.transform = ''; });

    SC.confort(); armed = false; render();
})();
</script>

<style>
.ha { --g:#d8b45e; --gl:#f0d69a; --bd:rgba(216,180,94,.28); margin:10px 0 34px; color:#e8e4da; }
.ha-shell { display:grid; grid-template-columns:minmax(0,1fr) 372px; gap:24px; align-items:start; }

/* scène */
.ha-stage { position:relative; aspect-ratio:3/2; overflow:hidden; border:1px solid var(--bd); border-radius:16px; background:#111;
    box-shadow:0 30px 70px rgba(0,0,0,.55); transition:transform .25s ease-out; will-change:transform; }
.ha-view, .ha-base, .ha-L, .ha-sv { position:absolute; inset:0; width:100%; height:100%; }
.ha-base { object-fit:cover; user-select:none; pointer-events:none; }
.ha-L { background-size:100% 100%; opacity:0; transition:opacity .75s ease; pointer-events:none; -webkit-mask-size:100% 100%; mask-size:100% 100%; -webkit-mask-repeat:no-repeat; mask-repeat:no-repeat; }
.ha-sv { pointer-events:none; } .ha-sv path { fill:rgba(5,9,18,.72); opacity:0; transition:opacity .9s ease; }
.ha-stage::after { content:""; position:absolute; inset:0; z-index:1; pointer-events:none; background:radial-gradient(ellipse at center,transparent 58%,rgba(0,0,0,.5) 100%); }
.ha-cv { position:absolute; inset:0; width:100%; height:100%; z-index:1; pointer-events:none; }
.ha-hot { position:absolute; z-index:3; width:24px; height:24px; margin:-12px 0 0 -12px; padding:0; border-radius:50%; border:1.5px solid rgba(255,255,255,.55); background:rgba(10,10,8,.5); cursor:pointer; transition:.25s; }
.ha-hot::before { content:""; position:absolute; inset:7px; border-radius:50%; background:#8d8a80; }
.ha-hot.on::before { background:var(--gl); box-shadow:0 0 10px 2px rgba(240,214,154,.8); }
.ha-hot.sel { border-color:var(--g); box-shadow:0 0 0 4px rgba(216,180,94,.35); }
.ha-hot::after { content:attr(data-n); position:absolute; left:50%; bottom:130%; transform:translateX(-50%); padding:4px 9px; border-radius:6px; background:rgba(10,10,8,.9); color:#fff; font-size:10px; white-space:nowrap; opacity:0; pointer-events:none; transition:.2s; }
.ha-hot:hover::after, .ha-hot.sel::after { opacity:1; }
.ha-chips { position:absolute; top:12px; left:12px; z-index:3; display:flex; gap:6px; flex-wrap:wrap; max-width:70%; pointer-events:none; }
.ha-chips span { padding:7px 11px; border:1px solid rgba(255,255,255,.14); border-radius:30px; background:rgba(10,10,8,.78); color:#d4d0c7; font-size:10px; letter-spacing:.4px; }
.ha-chips .ok { border-color:rgba(125,220,154,.5); color:#9be8b2; } .ha-chips .bad { border-color:#ff5a5a; color:#ff8a8a; animation:haB 1s infinite; }
.ha-hint { position:absolute; left:0; right:0; bottom:10px; z-index:3; margin:0; text-align:center; color:#b9b4a8; font-size:10px; letter-spacing:.5px; pointer-events:none; }
.ha-toast { position:absolute; left:50%; top:14px; z-index:7; transform:translate(-50%,-20px); padding:11px 18px; border:1px solid var(--bd); border-radius:30px; background:rgba(10,10,8,.94); color:#fff; font-size:12px; opacity:0; pointer-events:none; transition:.4s; }
.ha-toast.show { opacity:1; transform:translate(-50%,0); }
.ha-stage.alarm::before { content:""; position:absolute; inset:0; z-index:8; pointer-events:none; box-shadow:inset 0 0 90px 12px rgba(255,30,30,.75); animation:haB .9s infinite; }
@keyframes haB { 50% { opacity:.25; } }
.ha-vid { position:absolute; inset:0; z-index:6; display:none; background:#000; }
.ha-vid.on { display:block; animation:haIn .5s ease; }
@keyframes haIn { from { opacity:0; } to { opacity:1; } }
.ha-vid video { width:100%; height:100%; object-fit:cover; display:block; }
.ha-vt { position:absolute; left:14px; top:14px; padding:7px 12px; border-radius:30px; background:rgba(0,0,0,.65); color:#ff6b6b; font-size:11px; letter-spacing:1px; }
.ha-vx { position:absolute; right:14px; top:12px; width:36px; height:36px; border:0; border-radius:50%; background:rgba(0,0,0,.65); color:#fff; font-size:15px; cursor:pointer; }

/* téléphone */
.ha-phonewrap { display:flex; justify-content:center; }
.ha-phone { position:relative; width:100%; max-width:350px; height:780px; border:9px solid #22221e; border-radius:44px; background:#0b0b09; box-shadow:0 0 0 1.5px #55524a,0 24px 50px rgba(0,0,0,.6); overflow:hidden; }
.ha-notch { position:absolute; top:8px; left:50%; width:92px; height:22px; transform:translateX(-50%); border-radius:20px; background:#000; z-index:3; }
.ha-scr { height:100%; overflow-y:auto; padding:42px 14px 20px; scrollbar-width:none; }
.ha-scr::-webkit-scrollbar { display:none; }
.ha-ah { display:flex; justify-content:space-between; align-items:flex-start; }
.ha-ah small { color:var(--g); font-size:9px; font-weight:700; letter-spacing:2.5px; } .ha-ah h3 { margin:2px 0 0; font-family:Georgia,serif; font-size:20px; font-weight:400; color:#fff; }
.ha-live { color:#7ddc9a; font-size:10px; }
.ha-ban { margin-top:10px; padding:10px 12px; border-radius:12px; background:rgba(255,60,60,.18); border:1px solid #ff5a5a; color:#ffb0b0; font-size:12px; animation:haB 1.2s infinite; }
.ha-rooms { display:flex; gap:6px; overflow-x:auto; margin:12px -14px 0; padding:0 14px 4px; scrollbar-width:none; }
.ha-rooms::-webkit-scrollbar { display:none; }
.ha-rooms button { flex:0 0 auto; padding:8px 12px; border:1px solid rgba(255,255,255,.14); border-radius:30px; background:transparent; color:#c9c5ba; font-size:11px; cursor:pointer; white-space:nowrap; }
.ha-rooms button.is-active { border-color:var(--g); background:rgba(216,180,94,.15); color:var(--gl); }
.ha-card { margin-top:10px; padding:6px 14px 13px; border:1px solid var(--bd); border-radius:16px; background:rgba(255,255,255,.035); }
.ha-r { display:flex; align-items:center; gap:12px; padding:9px 0; }
.ha-r > div { flex:1; min-width:0; } .ha-r b { display:block; color:#fff; font-size:13px; font-weight:500; } .ha-r small { display:block; color:#9a968b; font-size:10px; margin-top:2px; }
.ha-i { width:34px; height:34px; flex:0 0 34px; display:grid; place-items:center; border:1px solid var(--bd); border-radius:50%; color:var(--g); font-size:15px; }
.ha-sw { position:relative; width:46px; height:26px; flex:0 0 46px; border:0; border-radius:26px; background:#3a3935; cursor:pointer; transition:.25s; }
.ha-sw i { position:absolute; top:3px; left:3px; width:20px; height:20px; border-radius:50%; background:#fff; transition:.25s; }
.ha-sw.is-on { background:var(--g); } .ha-sw.is-on i { transform:translateX(20px); }
.ha-rg { display:flex; justify-content:space-between; color:#9a968b; font-size:10px; margin-top:2px; } .ha-rg output { color:var(--gl); }
.ha input[type=range] { width:100%; accent-color:var(--g); }
.ha-clim { display:flex; align-items:center; justify-content:space-between; margin:4px 0 10px; }
.ha-clim strong { font-family:Georgia,serif; font-size:44px; font-weight:400; color:var(--gl); }
.ha-clim button { width:46px; height:46px; border:1px solid rgba(216,180,94,.4); border-radius:50%; background:transparent; color:var(--gl); font-size:22px; cursor:pointer; }
.ha-modes { display:grid; grid-template-columns:repeat(4,1fr); gap:5px; }
.ha-modes button { padding:9px 2px; border:1px solid rgba(255,255,255,.12); border-radius:10px; background:transparent; color:#d4d0c7; font-size:10px; cursor:pointer; }
.ha-modes button.is-active { border-color:var(--g); background:rgba(216,180,94,.14); color:var(--gl); }
.ha-np { display:flex; align-items:center; gap:12px; margin:4px 0 10px; } .ha-np b { display:block; color:#fff; font-size:14px; font-weight:500; } .ha-np small { color:#99978f; font-size:11px; }
.ha-art { display:grid; place-items:center; width:56px; height:56px; flex:0 0 56px; border-radius:14px; background:linear-gradient(135deg,#d6ad55,#6b3f1d); color:#111; font-size:26px; }
.ha-art.is-play { animation:haP 1.8s infinite; } @keyframes haP { 50% { transform:scale(1.06); } }
.ha-bar { height:4px; border-radius:4px; background:rgba(255,255,255,.12); overflow:hidden; } .ha-bar i { display:block; height:100%; width:0; background:var(--g); }
.ha-tm { display:flex; justify-content:space-between; margin-top:5px; color:#8f8a7e; font-size:10px; }
.ha-ctl { display:flex; align-items:center; justify-content:center; gap:22px; margin:8px 0; }
.ha-ctl button { width:42px; height:42px; border:0; border-radius:50%; background:transparent; color:#f5f2ea; font-size:18px; cursor:pointer; }
.ha-ctl .ha-play { width:54px; height:54px; background:var(--g); color:#111; }
.ha-vol { display:flex; align-items:center; gap:10px; color:var(--g); font-size:12px; } .ha-vol input { flex:1; } .ha-vol output { color:var(--gl); font-size:10px; width:32px; text-align:right; }
.ha-pl { margin:10px 0 0; padding:0; list-style:none; }
.ha-pl li { display:flex; align-items:center; gap:10px; padding:9px 8px; border-radius:10px; cursor:pointer; color:#d4d0c7; font-size:12px; }
.ha-pl li:hover { background:rgba(216,180,94,.07); } .ha-pl li.is-active { background:rgba(216,180,94,.14); color:var(--gl); }
.ha-pl b { width:14px; color:var(--g); font-size:10px; } .ha-pl span { flex:1; } .ha-pl small { display:block; color:#8f8a7e; font-size:10px; } .ha-pl em { font-style:normal; color:#8f8a7e; font-size:10px; }
.ha-en { display:flex; align-items:flex-end; gap:8px; height:78px; margin:6px 0 8px; }
.ha-en span { flex:1; display:flex; flex-direction:column; justify-content:flex-end; align-items:center; height:100%; gap:4px; }
.ha-en i { display:block; width:100%; height:var(--h); border-radius:5px 5px 2px 2px; background:rgba(216,180,94,.35); transition:height .5s; }
.ha-en .now i { background:var(--gl); } .ha-en em { font-style:normal; color:#8f8a7e; font-size:9px; }
.ha-sv2 { color:#9a968b; font-size:10px; } .ha-sv2 b { color:#7ddc9a; }
.ha-btns { display:grid; gap:7px; margin-top:4px; }
.ha-btns button { padding:12px; border:1px solid var(--bd); border-radius:12px; background:rgba(255,255,255,.04); color:#eee; font-size:12px; cursor:pointer; }
.ha-btns .ha-danger { border-color:rgba(255,90,90,.55); color:#ff9a9a; background:rgba(255,60,60,.1); }
.ha-scn { margin-top:10px; padding:12px; border:1px solid var(--bd); border-radius:16px; } .ha-scn > span { color:var(--g); font-size:9px; letter-spacing:2.5px; font-weight:700; }
.ha-scn div { display:grid; grid-template-columns:1fr 1fr; gap:7px; margin-top:10px; }
.ha-scn button { padding:11px; border:1px solid rgba(255,255,255,.14); border-radius:12px; background:transparent; color:#e8e4da; font-size:12px; cursor:pointer; }
.ha-scn button:hover, .ha-btns button:hover { border-color:var(--g); color:var(--gl); }
.ha-note { margin:12px 0 0; color:#7d796e; font-size:9px; text-align:center; }
@media (max-width:1060px) { .ha-shell { grid-template-columns:1fr; } .ha-phone { height:720px; } .ha-chips { max-width:90%; } .ha-stage { transform:none !important; } }
@media (prefers-reduced-motion:reduce) { .ha-stage, .ha-L, .ha-sv path { transition:none; } .ha-stage.alarm::before, .ha-ban { animation:none; } }
</style>