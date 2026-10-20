document.addEventListener('DOMContentLoaded', () => {

  /* Apparition au scroll */
  const items = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e, i) => {
        if (e.isIntersecting) {
          e.target.style.transitionDelay = (i % 4) * 80 + 'ms';
          e.target.classList.add('is-visible');
          io.unobserve(e.target);
          setTimeout(() => { e.target.style.transitionDelay = ''; }, 900);
        }
      });
    }, { threshold: 0.1 });
    items.forEach(el => io.observe(el));
  } else {
    items.forEach(el => el.classList.add('is-visible'));
  }

  /* Image cassée -> message propre */
  document.querySelectorAll('.hb-card-media img, .hb-cat-media img').forEach(img => {
    img.addEventListener('error', () => {
      const span = document.createElement('span');
      span.className = 'hb-card-noimg';
      span.textContent = img.alt || 'Image bientôt disponible';
      img.replaceWith(span);
    });
  });

  /* Galerie produit : miniatures + zoom */
  const main = document.getElementById('pMain');
  if (main) {
    const box = main.parentElement;
    const thumbs = document.querySelectorAll('.hb-thumbs button');
    if (thumbs[0]) thumbs[0].classList.add('is-active');

    thumbs.forEach(btn => {
      btn.addEventListener('click', () => {
        main.style.opacity = 0;
        setTimeout(() => { main.src = btn.dataset.src; main.style.opacity = 1; }, 150);
        thumbs.forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');
        box.classList.remove('is-zoom');
      });
    });

    box.addEventListener('click', () => box.classList.toggle('is-zoom'));
    box.addEventListener('mousemove', (e) => {
      if (!box.classList.contains('is-zoom')) return;
      const r = box.getBoundingClientRect();
      main.style.transformOrigin =
        ((e.clientX - r.left) / r.width * 100) + '% ' +
        ((e.clientY - r.top) / r.height * 100) + '%';
    });
  }
});
