// MyDream Organizer — helper JS kecil (vanilla, tanpa framework)

// 1) Countdown timer — elemen dengan [data-countdown="<detik>"] berisi 3 <span data-seg="h|m|s">
document.querySelectorAll('[data-countdown]').forEach(function (box) {
    var remaining = parseInt(box.getAttribute('data-countdown'), 10) || 0;
    var segH = box.querySelector('[data-seg="h"]');
    var segM = box.querySelector('[data-seg="m"]');
    var segS = box.querySelector('[data-seg="s"]');

    function pad(n) { return n.toString().padStart(2, '0'); }
    function render() {
        var h = Math.floor(remaining / 3600);
        var m = Math.floor((remaining % 3600) / 60);
        var s = remaining % 60;
        if (segH) segH.textContent = pad(h);
        if (segM) segM.textContent = pad(m);
        if (segS) segS.textContent = pad(s);
    }
    render();
    setInterval(function () {
        if (remaining > 0) { remaining -= 1; render(); }
    }, 1000);
});

// 2) Quantity stepper — tombol [data-qty="minus|plus"] di dalam .qty-control
document.querySelectorAll('.qty-control').forEach(function (control) {
    var display = control.querySelector('[data-qty-value]');
    var minus = control.querySelector('[data-qty="minus"]');
    var plus = control.querySelector('[data-qty="plus"]');
    if (!display) return;
    function current() { return parseInt(display.textContent, 10) || 1; }
    if (minus) minus.addEventListener('click', function () {
        var v = Math.max(1, current() - 1);
        display.textContent = v;
    });
    if (plus) plus.addEventListener('click', function () {
        display.textContent = current() + 1;
    });
});

// 3) Dropzone — tampilkan nama file yang dipilih
document.querySelectorAll('.dropzone input[type="file"]').forEach(function (input) {
    input.addEventListener('change', function () {
        var label = input.closest('.dropzone').querySelector('[data-file-name]');
        if (label && input.files && input.files[0]) {
            label.textContent = input.files[0].name;
        }
    });
});
