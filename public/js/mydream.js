/* MyDream — interaksi kecil (tanpa dependensi selain Bootstrap) */
document.addEventListener('DOMContentLoaded', function () {

  var rupiah = function (n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); };

  /* 1. Landing: filter portofolio */
  var tabs = document.querySelectorAll('[data-filter]');
  tabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var f = btn.dataset.filter;
      tabs.forEach(function (b) { b.classList.toggle('is-active', b === btn); });
      document.querySelectorAll('[data-category]').forEach(function (card) {
        card.hidden = !(f === 'all' || card.dataset.category === f);
      });
    });
  });

  /* 2. Store: tombol geser kiri/kanan */
  document.querySelectorAll('[data-scroller]').forEach(function (wrap) {
    var track = wrap.querySelector('.md-scroller__track');
    wrap.querySelectorAll('[data-dir]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var dir = btn.dataset.dir === 'next' ? 1 : -1;
        track.scrollBy({ left: dir * track.clientWidth * 0.8, behavior: 'smooth' });
      });
    });
  });

  /* 3. Tombol simpan (hati) di card — jangan ikut membuka link card */
  document.querySelectorAll('.md-fav').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      var icon = btn.querySelector('i');
      icon.classList.toggle('bi-heart');
      icon.classList.toggle('bi-heart-fill');
      btn.style.color = icon.classList.contains('bi-heart-fill') ? '#9b3d52' : '';
    });
  });

  /* 4. Galeri paket: klik thumbnail mengganti foto utama */
  document.querySelectorAll('[data-gallery]').forEach(function (g) {
    var main = g.querySelector('[data-gallery-main]');
    g.querySelectorAll('[data-gallery-thumb]').forEach(function (t) {
      t.addEventListener('click', function () {
        main.src = t.dataset.src;
        g.querySelectorAll('[data-gallery-thumb]').forEach(function (x) { x.classList.toggle('is-active', x === t); });
      });
    });
  });

  /* 5. Bagikan */
  document.querySelectorAll('[data-share]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var data = { title: document.title, url: location.href };
      if (navigator.share) { navigator.share(data).catch(function () {}); return; }
      if (navigator.clipboard) {
        navigator.clipboard.writeText(location.href).then(function () {
          var old = btn.innerHTML;
          btn.innerHTML = '<i class="bi bi-check2"></i> Tersalin';
          setTimeout(function () { btn.innerHTML = old; }, 1600);
        });
      }
    });
  });

  /* 6. Overview PAKET: ganti opsi pax → harga berubah, lalu beli via WhatsApp */
  var box = document.querySelector('[data-package-box]');
  if (box) {
    var q = function (s) { return box.querySelector(s); };
    var update = function () {
      var opt = q('input[name="option"]:checked');
      if (!opt) return;
      var price = +opt.dataset.price, old = +opt.dataset.old;
      q('[data-price]').textContent = rupiah(price);
      q('[data-old]').textContent = rupiah(old);
      q('[data-save]').textContent = rupiah(old - price);
      q('[data-installment]').textContent = rupiah(price / 24);
      q('[data-discount]').textContent = 'Diskon ' + Math.round((old - price) / old * 100) + '%';
    };
    box.querySelectorAll('input[name="option"]').forEach(function (r) { r.addEventListener('change', update); });

    q('[data-package-buy]').addEventListener('click', function () {
      var opt = q('input[name="option"]:checked');
      var ses = q('input[name="session"]:checked');
      var msg = 'Halo MyDream, saya ingin membeli voucher paket: ' + box.dataset.title +
                '\nOpsi: ' + (opt ? opt.dataset.label : '-') +
                '\nSesi: ' + (ses ? ses.value : '-') +
                '\nHarga: ' + (opt ? rupiah(+opt.dataset.price) : '-');
      window.open('https://wa.me/' + box.dataset.wa + '?text=' + encodeURIComponent(msg), '_blank', 'noopener');
    });
  }

  /* 7. Overview VENDOR: form cek ketersediaan → WhatsApp */
  var vf = document.querySelector('[data-vendor-form]');
  if (vf) {
    vf.querySelector('[data-vendor-send]').addEventListener('click', function () {
      var date = vf.querySelector('[name="date"]').value;
      var guests = vf.querySelector('[name="guests"]').value;
      var ses = vf.querySelector('[name="session"]:checked');
      var msg = 'Halo MyDream, saya ingin minta brosur & cek ketersediaan ' + vf.dataset.vendor +
                '\nTanggal acara: ' + (date || 'belum ditentukan') +
                '\nJumlah tamu: ' + guests +
                '\nWaktu: ' + (ses ? ses.value : '-');
      window.open('https://wa.me/' + vf.dataset.wa + '?text=' + encodeURIComponent(msg), '_blank', 'noopener');
    });
  }

  /* 8. Tab anchor: tandai yang aktif saat diklik */
  document.querySelectorAll('.detail-tabs a').forEach(function (a) {
    a.addEventListener('click', function () {
      a.parentElement.querySelectorAll('a').forEach(function (x) { x.classList.toggle('is-active', x === a); });
    });
  });
});
