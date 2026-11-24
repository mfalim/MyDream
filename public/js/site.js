/*
 * MyDream - interaksi halaman publik. Tanpa library, satu file, dimuat di layouts/site.blade.php.
 * Semua fitur bersifat tambahan: kalau JavaScript mati, halaman tetap bisa dipakai penuh.
 */
(function () {
  'use strict';

  var d = document;
  var w = window;
  var root = d.documentElement;
  var reduce = !!(w.matchMedia && w.matchMedia('(prefers-reduced-motion: reduce)').matches);

  var $ = function (sel, ctx) { return (ctx || d).querySelector(sel); };
  var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || d).querySelectorAll(sel)); };

  root.classList.add('js');

  /* ---------- 1. Muncul saat di-scroll (otomatis untuk judul, kartu, dan bagian) ---------- */
  function tagAutomatic() {
    // Anak-anak elemen [data-seq] muncul berurutan (mis. paragraf artikel).
    $$('[data-seq]').forEach(function (box) {
      Array.prototype.slice.call(box.children).forEach(function (el, i) {
        if (el.hasAttribute('data-reveal')) { return; }
        el.setAttribute('data-reveal', '');
        el.style.setProperty('--d', (Math.min(i, 6) * 0.08) + 's');
      });
    });

    // Judul halaman + teks pengantarnya muncul bertahap.
    $$('main h1').forEach(function (h1) {
      [h1.previousElementSibling, h1, h1.nextElementSibling].forEach(function (el, i) {
        if (!el || (el !== h1 && el.tagName !== 'P')) { return; }
        if (el.closest('[data-reveal], [data-hero]') || /animate-fade-up|reveal-in|hero-in/.test(el.className)) { return; }
        el.classList.add('reveal-in');
        el.style.animationDelay = (i * 0.12) + 's';
      });
    });

    // Kartu di dalam grid.
    $$('main .grid').forEach(function (grid) {
      if (grid.closest('form, aside, [data-no-reveal]')) { return; }
      var n = 0;
      Array.prototype.slice.call(grid.children).forEach(function (el) {
        var isCard = /^(A|ARTICLE|BLOCKQUOTE|LI)$/.test(el.tagName) ||
          (el.tagName === 'DIV' && /rounded/.test(el.className));
        if (!isCard || el.hasAttribute('data-reveal') || el.querySelector('[data-reveal]')) { return; }
        el.setAttribute('data-reveal', '');
        el.style.setProperty('--d', (Math.min(n, 8) * 0.07) + 's');
        n++;
      });
    });

    // Judul bagian.
    $$('main h2').forEach(function (h2) {
      if (h2.closest('[data-reveal], form, aside, [data-no-reveal]')) { return; }
      h2.setAttribute('data-reveal', '');
    });
  }

  function startReveal() {
    var items = $$('[data-reveal]');
    if (reduce || !('IntersectionObserver' in w)) {
      items.forEach(function (el) { el.classList.add('is-visible'); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { entry.target.classList.add('is-visible'); io.unobserve(entry.target); }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    items.forEach(function (el) { io.observe(el); });
  }

  /* ---------- 2. Angka yang menghitung naik ---------- */
  function startCounters() {
    var counters = $$('[data-count]');
    if (!counters.length || reduce || !('IntersectionObserver' in w)) { return; }
    var run = function (el) {
      var target = parseInt(el.getAttribute('data-count'), 10) || 0;
      if (target === 0) { return; }
      var start = null;
      var step = function (t) {
        if (start === null) { start = t; }
        var p = Math.min((t - start) / 1400, 1);
        el.textContent = (el.getAttribute('data-prefix') || '') + Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString('id-ID') + (el.getAttribute('data-suffix') || '');
        if (p < 1) { w.requestAnimationFrame(step); }
      };
      w.requestAnimationFrame(step);
    };
    var co = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { run(entry.target); co.unobserve(entry.target); }
      });
    }, { threshold: 0.6 });
    counters.forEach(function (el) { co.observe(el); });
  }

  /* ---------- 3. Navbar, garis kemajuan, tombol ke atas ---------- */
  function startScrollUi() {
    var header = $('.site-header');
    var bar = $('#scroll-progress');
    var top = $('#to-top');
    var ticking = false;

    var update = function () {
      var y = w.pageYOffset || root.scrollTop || 0;
      var max = Math.max(root.scrollHeight - w.innerHeight, 1);
      if (header) { header.classList.toggle('is-scrolled', y > 8); }
      if (bar) { bar.style.width = Math.min(100, (y / max) * 100) + '%'; }
      if (top) { top.classList.toggle('is-visible', y > 600); }
      ticking = false;
    };
    w.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; w.requestAnimationFrame(update); }
    }, { passive: true });
    update();

    if (top) {
      top.addEventListener('click', function () {
        w.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
      });
    }
  }

  /* ---------- 4. Notifikasi: banner flash diubah menjadi toast ---------- */
  function startToasts() {
    var box = $('#toasts');
    if (!box) { return; }
    $$('[data-flash]').forEach(function (banner) {
      var type = banner.getAttribute('data-flash') === 'error' ? 'error' : 'success';
      var text = banner.textContent.trim();
      banner.parentNode.removeChild(banner);
      if (!text) { return; }

      var toast = d.createElement('div');
      toast.className = 'toast';
      toast.setAttribute('data-type', type);
      toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
      var span = d.createElement('span');
      span.textContent = text;
      var close = d.createElement('button');
      close.type = 'button';
      close.setAttribute('aria-label', 'Tutup');
      close.innerHTML = '&times;';
      toast.appendChild(span);
      toast.appendChild(close);
      box.appendChild(toast);

      var dismiss = function () {
        toast.classList.remove('is-in');
        setTimeout(function () { if (toast.parentNode) { toast.parentNode.removeChild(toast); } }, 450);
      };
      close.addEventListener('click', dismiss);
      w.requestAnimationFrame(function () { toast.classList.add('is-in'); });
      setTimeout(dismiss, type === 'error' ? 9000 : 5500);
    });
  }

  /* ---------- 5. Form: status memproses + cegah klik ganda ---------- */
  function startForms() {
    d.addEventListener('submit', function (e) {
      var form = e.target;
      if (!form || form.tagName !== 'FORM' || form.hasAttribute('data-no-loading')) { return; }
      if (form.getAttribute('data-submitting') === '1') { e.preventDefault(); return; }
      form.setAttribute('data-submitting', '1');
      $$('button[type="submit"], button:not([type])', form).forEach(function (b) { b.classList.add('is-loading'); });
    });
    w.addEventListener('pageshow', function (e) {
      if (!e.persisted) { return; }
      $$('form[data-submitting]').forEach(function (f) {
        f.removeAttribute('data-submitting');
        $$('.is-loading', f).forEach(function (b) { b.classList.remove('is-loading'); });
      });
    });

    // Filter yang langsung diterapkan saat diubah.
    $$('form[data-autosubmit]').forEach(function (form) {
      var timer;
      var send = function () { if (form.requestSubmit) { form.requestSubmit(); } else { form.submit(); } };
      form.addEventListener('change', function (e) {
        if (e.target.matches('input[type="radio"], input[type="checkbox"], select')) { send(); }
      });
      form.addEventListener('input', function (e) {
        if (e.target.matches('input[type="text"], input[type="search"]')) {
          clearTimeout(timer);
          timer = setTimeout(send, 800);
        }
      });
    });
  }

  /* ---------- 6. Tombol buka/tutup, salin, pratinjau berkas ---------- */
  function startWidgets() {
    d.addEventListener('click', function (e) {
      var toggle = e.target.closest('[data-toggle]');
      if (toggle) {
        var target = $(toggle.getAttribute('data-toggle'));
        if (target) {
          target.classList.toggle('hidden');
          toggle.setAttribute('aria-expanded', String(!target.classList.contains('hidden')));
        }
        return;
      }

      var copyBtn = e.target.closest('[data-copy]');
      if (copyBtn) {
        e.preventDefault();
        copyText(copyBtn.getAttribute('data-copy')).then(function () {
          if (!copyBtn.hasAttribute('data-label')) { copyBtn.setAttribute('data-label', copyBtn.textContent); }
          copyBtn.textContent = 'Tersalin \u2713';
          copyBtn.classList.add('is-copied');
          setTimeout(function () {
            copyBtn.textContent = copyBtn.getAttribute('data-label');
            copyBtn.classList.remove('is-copied');
          }, 1800);
        });
      }
    });

    $$('input[type="file"][data-preview]').forEach(function (input) {
      var holder = $(input.getAttribute('data-preview'));
      if (!holder) { return; }
      var urls = [];
      input.addEventListener('change', function () {
        urls.forEach(function (u) { URL.revokeObjectURL(u); });
        urls = [];
        holder.innerHTML = '';
        Array.prototype.slice.call(input.files || []).forEach(function (file) {
          var el;
          if (/^image\//.test(file.type)) {
            var url = URL.createObjectURL(file);
            urls.push(url);
            el = d.createElement('img');
            el.src = url;
            el.alt = file.name;
            el.style.cssText = 'width:96px;height:72px;object-fit:cover;border-radius:10px;';
          } else {
            el = d.createElement('span');
            el.textContent = file.name;
            el.style.cssText = 'padding:6px 12px;border-radius:9999px;background:#F4E4E6;font-size:12px;';
          }
          holder.appendChild(el);
        });
      });
    });
  }

  function copyText(text) {
    if (w.navigator.clipboard && w.isSecureContext) { return w.navigator.clipboard.writeText(text); }
    return new Promise(function (resolve) {
      var ta = d.createElement('textarea');
      ta.value = text;
      ta.style.cssText = 'position:fixed;opacity:0;';
      d.body.appendChild(ta);
      ta.select();
      try { d.execCommand('copy'); } catch (err) { /* abaikan */ }
      d.body.removeChild(ta);
      resolve();
    });
  }

  /* ---------- 7. Foto muncul halus setelah dimuat ---------- */
  function startImages() {
    $$('main img, footer img').forEach(function (img) {
      if (/hero-slide/.test(img.className) || img.complete) { return; }
      img.setAttribute('data-fade', '');
      var done = function () { img.classList.add('is-loaded'); };
      img.addEventListener('load', done);
      img.addEventListener('error', done);
    });
  }

  /* ---------- 8. Lightbox galeri ---------- */
  var lb = null;
  var lbList = [];
  var lbIndex = 0;

  function buildLightbox() {
    lb = d.createElement('div');
    lb.className = 'lb';
    lb.setAttribute('role', 'dialog');
    lb.setAttribute('aria-modal', 'true');
    lb.innerHTML =
      '<button type="button" class="lb-btn lb-close" aria-label="Tutup">&times;</button>' +
      '<button type="button" class="lb-btn lb-prev" aria-label="Foto sebelumnya">&lsaquo;</button>' +
      '<img class="lb-img" alt="">' +
      '<button type="button" class="lb-btn lb-next" aria-label="Foto berikutnya">&rsaquo;</button>' +
      '<p class="lb-count"></p>';
    d.body.appendChild(lb);
    lb.addEventListener('click', function (e) {
      if (e.target === lb || e.target.closest('.lb-close')) { closeLightbox(); }
      else if (e.target.closest('.lb-prev')) { showLightbox(lbIndex - 1); }
      else if (e.target.closest('.lb-next')) { showLightbox(lbIndex + 1); }
    });
  }

  function showLightbox(i) {
    lbIndex = (i + lbList.length) % lbList.length;
    var img = lbList[lbIndex];
    $('.lb-img', lb).src = img.currentSrc || img.src;
    $('.lb-img', lb).alt = img.alt || '';
    $('.lb-count', lb).textContent = lbList.length > 1 ? (lbIndex + 1) + ' / ' + lbList.length : '';
    $('.lb-prev', lb).style.display = $('.lb-next', lb).style.display = lbList.length > 1 ? '' : 'none';
  }

  function openLightbox(list, index) {
    if (!lb) { buildLightbox(); }
    lbList = list;
    showLightbox(index);
    root.classList.add('lb-open');
    lb.style.display = 'flex';
    w.requestAnimationFrame(function () { lb.classList.add('is-in'); });
  }

  function closeLightbox() {
    if (!lb) { return; }
    lb.classList.remove('is-in');
    root.classList.remove('lb-open');
    setTimeout(function () { if (lb && !lb.classList.contains('is-in')) { lb.style.display = 'none'; } }, 260);
  }

  function startLightbox() {
    d.addEventListener('click', function (e) {
      var img = e.target.closest ? e.target.closest('[data-gallery] img') : null;
      if (!img) { return; }
      var group = img.closest('[data-gallery]').getAttribute('data-gallery');
      var list = $$('[data-gallery="' + group + '"] img');
      openLightbox(list, Math.max(list.indexOf(img), 0));
    });
    d.addEventListener('keydown', function (e) {
      if (!lb || !root.classList.contains('lb-open')) { return; }
      if (e.key === 'Escape') { closeLightbox(); }
      else if (e.key === 'ArrowLeft') { showLightbox(lbIndex - 1); }
      else if (e.key === 'ArrowRight') { showLightbox(lbIndex + 1); }
    });
  }


  /* ---------- 9. Parallax & efek kursor (beranda) ---------- */
  function startParallax() {
    var els = $$('[data-parallax]');
    var hero = $('[data-hero]');
    if (reduce || (!els.length && !hero)) { return; }
    var heroBg = hero ? $('[data-hero-bg]', hero) : null;
    var heroContent = hero ? $('[data-hero-content]', hero) : null;
    var ticking = false;

    var frame = function () {
      var vh = w.innerHeight;
      var y = w.pageYOffset || root.scrollTop || 0;

      els.forEach(function (el) {
        var r = el.getBoundingClientRect();
        if (r.bottom < -250 || r.top > vh + 250) { return; }
        var speed = parseFloat(el.getAttribute('data-parallax')) || 0.1;
        var off = (r.top + r.height / 2 - vh / 2) * speed;
        el.style.transform = 'translate3d(0,' + off.toFixed(1) + 'px,0)';
      });

      if (hero && y < hero.offsetHeight + 200) {
        var p = Math.min(y / (hero.offsetHeight || 1), 1);
        if (heroBg) { heroBg.style.transform = 'translate3d(0,' + (y * 0.3).toFixed(1) + 'px,0)'; }
        if (heroContent) {
          heroContent.style.transform = 'translate3d(0,' + (y * 0.16).toFixed(1) + 'px,0)';
          heroContent.style.opacity = String(Math.max(0, 1 - p * 1.15).toFixed(3));
        }
      }
      ticking = false;
    };
    w.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; w.requestAnimationFrame(frame); }
    }, { passive: true });
    w.addEventListener('resize', frame);
    frame();
  }

  function startPointerFx() {
    var fine = w.matchMedia && w.matchMedia('(hover: hover) and (pointer: fine)').matches;
    if (reduce || !fine) { return; }

    // Cahaya lembut mengikuti kursor di hero
    var hero = $('[data-hero]');
    if (hero) {
      hero.addEventListener('pointermove', function (e) {
        var r = hero.getBoundingClientRect();
        hero.style.setProperty('--mx', (e.clientX - r.left) + 'px');
        hero.style.setProperty('--my', (e.clientY - r.top) + 'px');
      });
    }

    // Kartu miring 3D + sorot cahaya
    $$('[data-tilt]').forEach(function (card) {
      card.addEventListener('pointermove', function (e) {
        var r = card.getBoundingClientRect();
        var x = (e.clientX - r.left) / r.width;
        var y = (e.clientY - r.top) / r.height;
        card.style.setProperty('--mx', (x * 100) + '%');
        card.style.setProperty('--my', (y * 100) + '%');
        card.style.transition = 'transform 0.12s ease-out, box-shadow 0.3s ease';
        card.style.transform = 'perspective(900px) rotateX(' + ((0.5 - y) * 9).toFixed(2) + 'deg) rotateY(' + ((x - 0.5) * 11).toFixed(2) + 'deg)';
      });
      card.addEventListener('pointerleave', function () {
        card.style.transition = '';
        card.style.transform = '';
      });
    });

    // Tombol magnetik
    $$('[data-magnetic]').forEach(function (el) {
      el.addEventListener('pointermove', function (e) {
        var r = el.getBoundingClientRect();
        var dx = e.clientX - (r.left + r.width / 2);
        var dy = e.clientY - (r.top + r.height / 2);
        el.style.translate = (dx * 0.22).toFixed(1) + 'px ' + (dy * 0.32).toFixed(1) + 'px';
      });
      el.addEventListener('pointerleave', function () { el.style.translate = ''; });
    });
  }


  /* ---------- 10. Transisi halaman ---------- */
  function startPageTransitions() {
    if (reduce) { return; }
    d.addEventListener('click', function (e) {
      if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) { return; }
      var a = e.target.closest ? e.target.closest('a[href]') : null;
      if (!a || a.target === '_blank' || a.hasAttribute('download') || a.hasAttribute('data-no-transition')) { return; }
      var url;
      try { url = new URL(a.href, w.location.href); } catch (err) { return; }
      if (url.origin !== w.location.origin || !/^https?:$/.test(url.protocol)) { return; }
      if (url.pathname === w.location.pathname && url.search === w.location.search) { return; }
      if (/^\/(admin|storage)/.test(url.pathname)) { return; }
      e.preventDefault();
      root.classList.add('page-leave');
      var bar = $('#scroll-progress');
      if (bar) { bar.style.transition = 'width 1.2s cubic-bezier(0.2, 0.7, 0.2, 1)'; bar.style.width = '78%'; }
      setTimeout(function () { w.location.href = url.href; }, 190);
    });
    w.addEventListener('pageshow', function (e) {
      if (e.persisted) { root.classList.remove('page-leave'); }
    });
  }

  /* ---------- 11. Carousel horizontal ([data-scroller]) ---------- */
  function startScrollers() {
    $$('[data-scroller]').forEach(function (box) {
      var track = $('.scroller-track', box);
      var prev = $('[data-scroll="prev"]', box);
      var next = $('[data-scroll="next"]', box);
      if (!track) { return; }

      var update = function () {
        var max = track.scrollWidth - track.clientWidth - 4;
        if (prev) { prev.disabled = track.scrollLeft <= 4; }
        if (next) { next.disabled = track.scrollLeft >= max; }
        if (max <= 0) { if (prev) { prev.style.display = 'none'; } if (next) { next.style.display = 'none'; } }
      };
      var go = function (dir) {
        track.scrollBy({ left: dir * track.clientWidth * 0.85, behavior: reduce ? 'auto' : 'smooth' });
      };
      if (prev) { prev.addEventListener('click', function () { go(-1); }); }
      if (next) { next.addEventListener('click', function () { go(1); }); }
      track.addEventListener('scroll', update, { passive: true });
      w.addEventListener('resize', update);
      update();

      // Geser dengan mouse (drag)
      var down = false, startX = 0, startLeft = 0, moved = 0;
      track.addEventListener('pointerdown', function (e) {
        if (e.pointerType !== 'mouse') { return; }
        down = true; moved = 0; startX = e.clientX; startLeft = track.scrollLeft;
      });
      w.addEventListener('pointermove', function (e) {
        if (!down) { return; }
        var dx = e.clientX - startX;
        moved = Math.max(moved, Math.abs(dx));
        if (moved > 6) { track.classList.add('is-drag'); track.scrollLeft = startLeft - dx; }
      });
      w.addEventListener('pointerup', function () {
        if (!down) { return; }
        down = false;
        setTimeout(function () { track.classList.remove('is-drag'); }, 0);
      });
      track.addEventListener('click', function (e) {
        if (moved > 6) { e.preventDefault(); e.stopPropagation(); moved = 0; }
      }, true);
    });
  }

  /* ---------- 12. Hitung mundur ([data-countdown="2026-11-20T00:00:00+07:00"]) ---------- */
  function startCountdowns() {
    $$('[data-countdown]').forEach(function (box) {
      var target = Date.parse(box.getAttribute('data-countdown'));
      if (isNaN(target)) { return; }
      var cells = { d: $('[data-cd="d"]', box), h: $('[data-cd="h"]', box), m: $('[data-cd="m"]', box), s: $('[data-cd="s"]', box) };
      var pad = function (n) { return n < 10 ? '0' + n : String(n); };
      var set = function (key, val) {
        var el = cells[key];
        if (!el || el.textContent === val) { return; }
        el.textContent = val;
        if (!reduce) { el.classList.remove('tick'); void el.offsetWidth; el.classList.add('tick'); }
      };
      var tickFn = function () {
        var diff = Math.max(0, target - Date.now());
        var secs = Math.floor(diff / 1000);
        set('d', pad(Math.floor(secs / 86400)));
        set('h', pad(Math.floor((secs % 86400) / 3600)));
        set('m', pad(Math.floor((secs % 3600) / 60)));
        set('s', pad(secs % 60));
        if (diff <= 0) { clearInterval(timer); }
      };
      var timer = setInterval(tickFn, 1000);
      tickFn();
    });
  }

  /* ---------- 13. Sorot cahaya pada kartu ---------- */
  function startSpotCards() {
    var fine = w.matchMedia && w.matchMedia('(hover: hover) and (pointer: fine)').matches;
    if (reduce || !fine) { return; }
    $$('main a.group.block').forEach(function (card) {
      card.classList.add('spot-card');
      card.addEventListener('pointermove', function (e) {
        var r = card.getBoundingClientRect();
        card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
        card.style.setProperty('--my', (e.clientY - r.top) + 'px');
      });
    });
  }

  /* ---------- 14. Terbang ke keranjang (form[data-fly-cart]) ---------- */
  function startFlyToCart() {
    d.addEventListener('submit', function (e) {
      var form = e.target;
      if (reduce || !form || !form.matches || !form.matches('form[data-fly-cart]')) { return; }
      var target = $('[data-cart-target]');
      var btn = $('button[type="submit"]', form);
      var img = $('[data-gallery] img') || $('main img');
      if (!target || !btn || !img || !btn.animate) { return; }

      e.preventDefault();
      var b = btn.getBoundingClientRect();
      var t = target.getBoundingClientRect();
      var dot = d.createElement('div');
      dot.className = 'fly-dot';
      dot.style.backgroundImage = 'url("' + (img.currentSrc || img.src) + '")';
      dot.style.left = (b.left + b.width / 2 - 26) + 'px';
      dot.style.top = (b.top + b.height / 2 - 26) + 'px';
      d.body.appendChild(dot);

      var dx = t.left + t.width / 2 - (b.left + b.width / 2);
      var dy = t.top + t.height / 2 - (b.top + b.height / 2);
      var anim = dot.animate([
        { transform: 'translate(0,0) scale(1)', opacity: 1 },
        { transform: 'translate(' + (dx * 0.5) + 'px,' + (dy * 0.5 - 90) + 'px) scale(0.8)', opacity: 1, offset: 0.5 },
        { transform: 'translate(' + dx + 'px,' + dy + 'px) scale(0.15)', opacity: 0.4 }
      ], { duration: 750, easing: 'cubic-bezier(0.5, 0, 0.6, 1)' });
      anim.onfinish = function () {
        if (dot.parentNode) { dot.parentNode.removeChild(dot); }
        target.classList.add('cart-bump');
        setTimeout(function () { form.submit(); }, 260);
      };
    }, true);
  }

  /* ---------- 15. Hasil katalog memudar saat filter diterapkan ---------- */
  function startBusyResults() {
    $$('form[data-autosubmit]').forEach(function (form) {
      form.addEventListener('submit', function () {
        var results = $('[data-results]');
        if (results) { results.classList.add('is-busy'); }
      });
    });
  }

  /* ---------- Jalankan ---------- */
  function init() {
    tagAutomatic();
    startReveal();
    startCounters();
    startScrollUi();
    startToasts();
    startForms();
    startWidgets();
    startImages();
    startLightbox();
    startParallax();
    startPointerFx();
    startSpotCards();
    startScrollers();
    startCountdowns();
    startFlyToCart();
    startBusyResults();
    startPageTransitions();
  }

  if (d.readyState === 'loading') { d.addEventListener('DOMContentLoaded', init); } else { init(); }
})();
