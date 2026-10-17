/* HN BLOOM — Administration (JavaScript natif, sans dépendance) */
(function () {
    'use strict';

    var root = document.documentElement;
    var $ = function (s, c) { return (c || document).querySelector(s); };
    var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var NS = 'http://www.w3.org/2000/svg';
    var nf = new Intl.NumberFormat('fr-FR');

    function svgEl(name, attrs, parent) {
        var n = document.createElementNS(NS, name);
        for (var k in attrs) { if (Object.prototype.hasOwnProperty.call(attrs, k)) n.setAttribute(k, attrs[k]); }
        if (parent) parent.appendChild(n);
        return n;
    }
    function store(key, val) { try { localStorage.setItem(key, val); } catch (e) {} }

    /* ---------- Thème clair / sombre ---------- */
    var themeBtn = $('#ad-theme');
    if (themeBtn) {
        themeBtn.addEventListener('click', function () {
            var next = root.getAttribute('data-ad-theme') === 'light' ? 'dark' : 'light';
            root.setAttribute('data-ad-theme', next);
            store('hn-admin-theme', next);
            document.dispatchEvent(new CustomEvent('ad:theme'));
        });
    }

    /* ---------- Menu latéral : mobile + réduction ---------- */
    var shell = $('#ad-shell');
    var menuBtn = $('#ad-menu-btn');
    function closeMenu() { if (shell) shell.classList.remove('ad-open'); }
    if (menuBtn && shell) menuBtn.addEventListener('click', function () { shell.classList.toggle('ad-open'); });
    var overlay = $('#ad-overlay');
    if (overlay) overlay.addEventListener('click', closeMenu);
    $$('.ad-sidebar a').forEach(function (a) { a.addEventListener('click', closeMenu); });

    var collapse = $('#ad-collapse');
    if (collapse) {
        collapse.addEventListener('click', function () {
            var on = root.classList.toggle('ad-collapsed');
            store('hn-admin-collapsed', on ? '1' : '0');
            setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 300);
        });
    }

    /* ---------- Menu utilisateur ---------- */
    var user = $('#ad-user');
    var userBtn = $('#ad-user-btn');
    if (user && userBtn) {
        userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = user.classList.toggle('is-open');
            userBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        document.addEventListener('click', function (e) { if (!user.contains(e.target)) user.classList.remove('is-open'); });
    }

    /* ---------- Notifications (flash) ---------- */
    function dismiss(t) {
        if (!t || t.classList.contains('is-out')) return;
        t.classList.add('is-out');
        setTimeout(function () { if (t.parentNode) t.parentNode.removeChild(t); }, 320);
    }
    $$('.ad-toast').forEach(function (t) {
        var x = $('.ad-toast-x', t);
        if (x) x.addEventListener('click', function () { dismiss(t); });
        setTimeout(function () { dismiss(t); }, 5200);
    });

    /* ---------- Fenêtre de confirmation ---------- */
    var modal = $('#ad-modal');
    var pending = null;
    function openModal(text, ok) {
        if (!modal) { if (window.confirm(text)) ok(); return; }
        $('#ad-modal-text').textContent = text || 'Confirmer cette action ?';
        pending = ok;
        modal.hidden = false;
        var b = $('[data-modal-ok]', modal); if (b) b.focus();
    }
    function closeModal() { if (modal) modal.hidden = true; pending = null; }
    if (modal) {
        $('[data-modal-cancel]', modal).addEventListener('click', closeModal);
        $('[data-modal-ok]', modal).addEventListener('click', function () { var fn = pending; closeModal(); if (fn) fn(); });
        modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    }

    // Les anciens formulaires utilisent onsubmit="return confirm('…')" : on les convertit en fenêtre élégante.
    $$('form[onsubmit]').forEach(function (f) {
        var m = /confirm\((['"])([\s\S]*?)\1\)/.exec(f.getAttribute('onsubmit') || '');
        if (m) { f.setAttribute('data-confirm', m[2]); f.removeAttribute('onsubmit'); }
    });

    document.addEventListener('submit', function (e) {
        var f = e.target;
        if (!(f instanceof HTMLFormElement)) return;
        if (f.hasAttribute('data-confirm') && !f.__confirmed) {
            e.preventDefault();
            openModal(f.getAttribute('data-confirm'), function () {
                f.__confirmed = true;
                if (f.requestSubmit) f.requestSubmit(); else f.submit();
            });
            return;
        }
        // état de chargement (évite les doubles envois)
        var btn = f.querySelector('button[type="submit"], button:not([type])');
        if (btn && !btn.classList.contains('is-loading')) {
            setTimeout(function () { btn.classList.add('is-loading'); }, 0);
            setTimeout(function () { btn.classList.remove('is-loading'); }, 8000);
        }
    });
    window.addEventListener('pageshow', function () { $$('.is-loading').forEach(function (b) { b.classList.remove('is-loading'); }); });

    /* ---------- Filtre rapide des tableaux + raccourci « / » ---------- */
    var filter = $('#ad-filter');
    if (filter) {
        var hasTable = !!$('.ad-main table');
        if (!hasTable) { var box = filter.closest('.ad-search'); if (box) box.style.display = 'none'; }
        filter.addEventListener('input', function () {
            var q = filter.value.trim().toLowerCase();
            $$('.ad-main table tbody tr').forEach(function (tr) {
                tr.style.display = !q || tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
            });
        });
    }
    document.addEventListener('keydown', function (e) {
        var tag = (e.target.tagName || '').toLowerCase();
        if (e.key === '/' && filter && tag !== 'input' && tag !== 'textarea' && tag !== 'select') { e.preventDefault(); filter.focus(); }
        if (e.key === 'Escape') { closeMenu(); closeModal(); if (user) user.classList.remove('is-open'); if (filter && document.activeElement === filter) filter.blur(); }
    });

    /* ---------- Aperçu des images choisies (formulaires) ---------- */
    $$('input[type="file"][data-preview]').forEach(function (inp) {
        var box = document.getElementById(inp.getAttribute('data-preview'));
        if (!box) return;
        inp.addEventListener('change', function () {
            box.innerHTML = '';
            Array.prototype.slice.call(inp.files).forEach(function (file) {
                if (!/^image\//.test(file.type)) return;
                var img = document.createElement('img');
                img.className = 'ad-thumb-lg';
                img.alt = '';
                img.src = URL.createObjectURL(file);
                box.appendChild(img);
            });
        });
    });

    /* ---------- Compteurs animés ---------- */
    $$('[data-count]').forEach(function (n) {
        var to = parseFloat(n.getAttribute('data-count')) || 0;
        var suf = n.getAttribute('data-suffix') || '';
        if (reduce || to === 0) return;
        var start = null, dur = 900;
        n.textContent = '0' + suf;
        function step(ts) {
            if (start === null) start = ts;
            var p = Math.min((ts - start) / dur, 1), e = 1 - Math.pow(1 - p, 3);
            n.textContent = nf.format(Math.round(to * e)) + suf;
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    });

    /* ---------- Mini-courbes ---------- */
    $$('[data-spark]').forEach(function (s) {
        var v = (s.getAttribute('data-spark') || '').split(',').map(Number).filter(function (x) { return !isNaN(x); });
        if (!v.length) return;
        var max = Math.max.apply(null, v), min = Math.min.apply(null, v), rng = (max - min) || 1;
        var pts = v.map(function (y, i) { return [(i / ((v.length - 1) || 1)) * 100, max === min ? 20 : 25 - ((y - min) / rng) * 19]; });
        var line = 'M' + pts.map(function (p) { return p[0].toFixed(2) + ',' + p[1].toFixed(2); }).join(' L');
        var defs = svgEl('defs', {}, s);
        var g = svgEl('linearGradient', { id: 'adSparkGrad', x1: 0, y1: 0, x2: 0, y2: 1 }, defs);
        svgEl('stop', { offset: '0%', 'stop-color': '#d8b45e', 'stop-opacity': '.35' }, g);
        svgEl('stop', { offset: '100%', 'stop-color': '#d8b45e', 'stop-opacity': '0' }, g);
        svgEl('path', { d: line + ' L100,28 L0,28 Z', 'class': 'a' }, s);
        svgEl('path', { d: line, 'class': 'l' }, s);
    });

    /* ---------- Graphique d'activité ---------- */
    function niceMax(m) {
        if (m <= 0) return 4;
        var p = Math.pow(10, Math.floor(Math.log(m) / Math.LN10)), n = m / p;
        return (n <= 1 ? 1 : n <= 2 ? 2 : n <= 5 ? 5 : 10) * p;
    }
    function fmtAxis(v) { return v >= 1000 ? (v / 1000).toFixed(v % 1000 ? 1 : 0).replace('.', ',') + 'k' : String(Math.round(v)); }

    var chartBox = $('#ad-chart'), chartData = null;
    try { var cd = $('#ad-chart-data'); chartData = cd ? JSON.parse(cd.textContent) : null; } catch (e) { chartData = null; }

    if (chartBox && chartData) {
        var state = { metric: 'revenue', range: 30 };
        var tip = document.createElement('div');
        tip.className = 'ad-tip';

        var render = function () {
            var w = chartBox.clientWidth - 24 || 640, h = 290;
            var pad = { l: 48, r: 12, t: 14, b: 30 };
            var labels = chartData.labels.slice(-state.range);
            var vals = (state.metric === 'revenue' ? chartData.revenue : chartData.orders).slice(-state.range);
            var max = Math.max.apply(null, vals.concat([0]));
            var top = state.metric === 'orders' ? Math.max(4, Math.ceil(max / 4) * 4) : niceMax(max);
            var iw = w - pad.l - pad.r, ih = h - pad.t - pad.b, n = vals.length;
            var cx = function (i) { return pad.l + iw * (i + 0.5) / n; };
            var Y = function (v) { return pad.t + ih - ih * v / top; };

            chartBox.innerHTML = '';
            var svg = svgEl('svg', { viewBox: '0 0 ' + w + ' ' + h, width: w, height: h, role: 'img', 'aria-label': 'Graphique d’activité' }, chartBox);
            var defs = svgEl('defs', {}, svg);
            var g = svgEl('linearGradient', { id: 'adAreaGrad', x1: 0, y1: 0, x2: 0, y2: 1 }, defs);
            svgEl('stop', { offset: '0%', 'stop-color': '#d8b45e', 'stop-opacity': '.38' }, g);
            svgEl('stop', { offset: '100%', 'stop-color': '#d8b45e', 'stop-opacity': '0' }, g);

            for (var i = 0; i <= 4; i++) {
                var y = pad.t + ih - ih * i / 4;
                svgEl('line', { x1: pad.l, x2: w - pad.r, y1: y, y2: y, 'class': 'ad-grid-line' }, svg);
                var t = svgEl('text', { x: pad.l - 8, y: y + 4, 'text-anchor': 'end', 'class': 'ad-axis' }, svg);
                t.textContent = fmtAxis(top * i / 4);
            }
            var every = Math.ceil(n / (w < 520 ? 5 : 8));
            labels.forEach(function (l, i) {
                if (i % every !== 0 && i !== n - 1) return;
                var tx = svgEl('text', { x: cx(i), y: h - 8, 'text-anchor': 'middle', 'class': 'ad-axis' }, svg);
                tx.textContent = l;
            });

            var bars = [];
            if (state.metric === 'orders') {
                var bw = Math.max(3, Math.min(26, iw / n * 0.62));
                vals.forEach(function (v, i) {
                    var by = Y(v);
                    bars.push(svgEl('rect', { x: cx(i) - bw / 2, y: v ? by : Y(0) - 1, width: bw, height: Math.max(v ? Y(0) - by : 1, 1), rx: 2, 'class': 'ad-bar-rect' }, svg));
                });
            } else {
                var d = vals.map(function (v, i) { return (i ? 'L' : 'M') + cx(i).toFixed(1) + ',' + Y(v).toFixed(1); }).join(' ');
                svgEl('path', { d: d + ' L' + cx(n - 1).toFixed(1) + ',' + Y(0) + ' L' + cx(0).toFixed(1) + ',' + Y(0) + ' Z', fill: 'url(#adAreaGrad)' }, svg);
                svgEl('path', { d: d, 'class': 'ad-line' }, svg);
            }

            var guide = svgEl('line', { y1: pad.t, y2: pad.t + ih, 'class': 'ad-guide', style: 'display:none' }, svg);
            var dot = svgEl('circle', { r: 5, 'class': 'ad-point', style: 'display:none' }, svg);
            var hit = svgEl('rect', { x: pad.l, y: pad.t, width: iw, height: ih, fill: 'transparent' }, svg);
            chartBox.appendChild(tip);

            function show(ev) {
                var r = svg.getBoundingClientRect(), scale = w / r.width;
                var px = ((ev.touches ? ev.touches[0].clientX : ev.clientX) - r.left) * scale;
                var idx = Math.max(0, Math.min(n - 1, Math.floor((px - pad.l) / iw * n)));
                var xx = cx(idx), yy = Y(vals[idx]);
                guide.setAttribute('x1', xx); guide.setAttribute('x2', xx); guide.style.display = '';
                bars.forEach(function (b, k) { b.classList.toggle('is-hot', k === idx); });
                if (state.metric === 'revenue') { dot.setAttribute('cx', xx); dot.setAttribute('cy', yy); dot.style.display = ''; }
                tip.innerHTML = '<span>' + labels[idx] + '</span><b>' + (state.metric === 'revenue' ? nf.format(Math.round(vals[idx])) + ' MAD' : vals[idx] + (vals[idx] > 1 ? ' commandes' : ' commande')) + '</b>';
                tip.style.display = 'flex';
                tip.style.left = Math.max(60, Math.min(chartBox.clientWidth - 60, xx / scale + 12)) + 'px';
                tip.style.top = Math.max(40, yy / scale - 8) + 'px';
            }
            function hide() { guide.style.display = 'none'; dot.style.display = 'none'; tip.style.display = 'none'; bars.forEach(function (b) { b.classList.remove('is-hot'); }); }
            hit.addEventListener('mousemove', show); hit.addEventListener('touchmove', show, { passive: true });
            hit.addEventListener('touchstart', show, { passive: true });
            hit.addEventListener('mouseleave', hide);
        };

        $$('[data-metric]').forEach(function (b) {
            b.addEventListener('click', function () {
                state.metric = b.getAttribute('data-metric');
                $$('[data-metric]').forEach(function (x) { x.classList.toggle('is-active', x === b); });
                render();
            });
        });
        $$('[data-range]').forEach(function (b) {
            b.addEventListener('click', function () {
                state.range = parseInt(b.getAttribute('data-range'), 10);
                $$('[data-range]').forEach(function (x) { x.classList.toggle('is-active', x === b); });
                render();
            });
        });
        var rt;
        var onResize = function () { clearTimeout(rt); rt = setTimeout(render, 120); };
        if (window.ResizeObserver) new ResizeObserver(onResize).observe(chartBox); else window.addEventListener('resize', onResize);
        render();
    }

    /* ---------- Anneau des statuts ---------- */
    var donutBox = $('#ad-donut'), donutData = null;
    try { var dd = $('#ad-donut-data'); donutData = dd ? JSON.parse(dd.textContent) : null; } catch (e) { donutData = null; }
    if (donutBox && donutData) {
        var total = donutData.reduce(function (a, b) { return a + b.n; }, 0);
        var S = 170, c0 = S / 2, r = 64, circ = 2 * Math.PI * r;
        var svg = svgEl('svg', { viewBox: '0 0 ' + S + ' ' + S, role: 'img', 'aria-label': 'Commandes par statut' }, donutBox);
        svgEl('circle', { cx: c0, cy: c0, r: r, fill: 'none', 'stroke-width': 17, 'class': 'ad-donut-track' }, svg);
        var offset = 0;
        donutData.forEach(function (s) {
            if (!s.n || !total) return;
            var len = circ * s.n / total, gap = donutData.filter(function (x) { return x.n; }).length > 1 ? 3 : 0;
            var arc = svgEl('circle', {
                cx: c0, cy: c0, r: r, fill: 'none', stroke: s.color, 'stroke-width': 17,
                'stroke-dasharray': Math.max(len - gap, 0.1) + ' ' + (circ - Math.max(len - gap, 0.1)),
                'stroke-dashoffset': -offset, transform: 'rotate(-90 ' + c0 + ' ' + c0 + ')'
            }, svg);
            var title = svgEl('title', {}, arc); title.textContent = s.label + ' : ' + s.n;
            offset += len;
        });
        var num = svgEl('text', { x: c0, y: c0 + 6, 'class': 'ad-donut-num' }, svg); num.textContent = total;
        var sub = svgEl('text', { x: c0, y: c0 + 26, 'class': 'ad-donut-sub' }, svg); sub.textContent = total > 1 ? 'commandes' : 'commande';
    }
})();
