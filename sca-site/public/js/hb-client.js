/* HN Bloom — espace client (menus, slider, galerie, quantités) */
(function () {
    'use strict';
    var d = document;
    d.documentElement.classList.add('js');
    function $$(s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); }

    /* ---------- Menus déroulants ---------- */
    $$('[data-jm-dd]').forEach(function (dd) {
        var btn = dd.querySelector('[data-jm-dd-btn]');
        if (!btn) return;
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = dd.classList.contains('open');
            $$('[data-jm-dd].open').forEach(function (o) { o.classList.remove('open'); });
            if (!open) dd.classList.add('open');
        });
    });
    d.addEventListener('click', function (e) {
        if (!e.target.closest('[data-jm-dd]')) $$('[data-jm-dd].open').forEach(function (o) { o.classList.remove('open'); });
    });
    d.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            $$('[data-jm-dd].open').forEach(function (o) { o.classList.remove('open'); });
            closeDrawer();
        }
    });

    /* ---------- Menu mobile ---------- */
    var drawer = d.querySelector('[data-jm-drawer]');
    function closeDrawer() { if (drawer) { drawer.classList.remove('open'); drawer.setAttribute('aria-hidden', 'true'); d.body.style.overflow = ''; } }
    $$('[data-jm-drawer-open]').forEach(function (b) {
        b.addEventListener('click', function () { if (drawer) { drawer.classList.add('open'); drawer.setAttribute('aria-hidden', 'false'); d.body.style.overflow = 'hidden'; } });
    });
    $$('[data-jm-drawer-close]').forEach(function (b) { b.addEventListener('click', closeDrawer); });

    /* ---------- Slider d'accueil ---------- */
    $$('[data-jm-slider]').forEach(function (box) {
        var slides = $$('.jm-slide', box), dots = box.querySelector('[data-jm-dots]'), i = 0, timer;
        if (slides.length < 2) return;
        slides.forEach(function (s, k) {
            var b = d.createElement('button');
            b.type = 'button'; b.setAttribute('aria-label', 'Diapositive ' + (k + 1));
            b.addEventListener('click', function () { go(k); restart(); });
            dots.appendChild(b);
        });
        function go(n) {
            i = (n + slides.length) % slides.length;
            slides.forEach(function (s, k) { s.classList.toggle('is-on', k === i); });
            $$('button', dots).forEach(function (b, k) { b.classList.toggle('is-on', k === i); });
        }
        function restart() { clearInterval(timer); timer = setInterval(function () { go(i + 1); }, 6000); }
        var p = box.querySelector('[data-jm-prev]'), n = box.querySelector('[data-jm-next]');
        if (p) p.addEventListener('click', function () { go(i - 1); restart(); });
        if (n) n.addEventListener('click', function () { go(i + 1); restart(); });
        box.addEventListener('mouseenter', function () { clearInterval(timer); });
        box.addEventListener('mouseleave', restart);
        go(0); restart();
    });

    /* ---------- Galerie produit ---------- */
    var main = d.getElementById('jm-main-img');
    $$('[data-jm-thumb]').forEach(function (t) {
        t.addEventListener('click', function () {
            if (main) main.src = t.getAttribute('data-jm-thumb');
            $$('[data-jm-thumb]').forEach(function (o) { o.classList.remove('is-on'); });
            t.classList.add('is-on');
        });
    });

    /* ---------- Sélecteur de quantité ---------- */
    $$('[data-jm-qty]').forEach(function (q) {
        var input = q.querySelector('input'), auto = q.hasAttribute('data-auto'), t;
        function submit() { if (!auto) return; clearTimeout(t); t = setTimeout(function () { q.closest('form').submit(); }, 450); }
        $$('button[data-step]', q).forEach(function (b) {
            b.addEventListener('click', function () {
                var v = parseInt(input.value, 10) || 1;
                input.value = Math.max(1, v + parseInt(b.getAttribute('data-step'), 10));
                submit();
            });
        });
        input.addEventListener('change', function () { if (!(parseInt(input.value, 10) >= 1)) input.value = 1; submit(); });
    });

    /* ---------- Confirmation de suppression ---------- */
    $$('form[data-jm-confirm]').forEach(function (f) {
        f.addEventListener('submit', function (e) { if (!window.confirm(f.getAttribute('data-jm-confirm'))) e.preventDefault(); });
    });

    /* ---------- Bouton « ajouter » : retour visuel ---------- */
    $$('.jm-pc-form, .jm-buy').forEach(function (f) {
        f.addEventListener('submit', function () {
            var b = f.querySelector('button[type="submit"], .jm-pc-add');
            if (b) { b.style.opacity = '.7'; b.style.pointerEvents = 'none'; }
        });
    });

    /* ---------- Messages flash ---------- */
    $$('[data-jm-flash]').forEach(function (f) {
        setTimeout(function () { f.style.opacity = '0'; setTimeout(function () { f.remove(); }, 450); }, 5000);
    });

    /* ---------- Haut de page ---------- */
    var top = d.querySelector('[data-jm-totop]');
    if (top) {
        window.addEventListener('scroll', function () { top.classList.toggle('show', window.scrollY > 500); }, { passive: true });
        top.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
    }
})();
