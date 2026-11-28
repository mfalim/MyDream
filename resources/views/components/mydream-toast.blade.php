{{-- Satu komponen Toast / Alert Popup terpusat untuk seluruh halaman Web MyDream (Admin, Member, User/Frontend) --}}
<div class="mydream-toast-stack" id="mydreamToastStack" aria-live="polite" aria-atomic="true"></div>

<style>
.mydream-toast-stack {
    position: fixed;
    top: 22px;
    right: 22px;
    z-index: 1095;
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: min(390px, calc(100vw - 28px));
    pointer-events: none;
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
}

.mydream-toast {
    pointer-events: auto;
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    background: #FFFDFB;
    border: 1px solid #E8D8CC;
    border-left: 4px solid #7A1F2B;
    border-radius: 14px;
    box-shadow: 0 18px 40px rgba(43, 33, 28, 0.14), 0 4px 12px rgba(92, 22, 32, 0.08);
    color: #2B211C;
    overflow: hidden;
    transform: translateX(18px);
    opacity: 0;
    transition: transform 0.26s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.24s ease;
}

.mydream-toast.is-visible {
    transform: translateX(0);
    opacity: 1;
}

.mydream-toast.is-leaving {
    transform: translateX(24px);
    opacity: 0;
}

.mydream-toast--success { border-left-color: #0d6e4c; }
.mydream-toast--warning { border-left-color: #b47b1e; }
.mydream-toast--error   { border-left-color: #b91c1c; }
.mydream-toast--info    { border-left-color: #7A1F2B; }

.mydream-toast__icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    flex-shrink: 0;
    background: #F4E4E6;
    color: #7A1F2B;
}

.mydream-toast--success .mydream-toast__icon { background: #dff7ee; color: #0d6e4c; }
.mydream-toast--warning .mydream-toast__icon { background: #fff3d6; color: #9a6507; }
.mydream-toast--error   .mydream-toast__icon { background: #fee2e2; color: #b91c1c; }

.mydream-toast__body {
    flex: 1;
    min-width: 0;
}

.mydream-toast__title {
    font-size: 13.5px;
    font-weight: 700;
    color: #2B211C;
    line-height: 1.3;
    margin-bottom: 2px;
}

.mydream-toast__message {
    font-size: 12.8px;
    color: #6B5B54;
    line-height: 1.45;
    word-break: break-word;
}

.mydream-toast__close {
    border: 0;
    background: transparent;
    color: #8C7A72;
    width: 26px;
    height: 26px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    font-size: 16px;
    line-height: 1;
    transition: background-color 0.15s ease, color 0.15s ease;
}

.mydream-toast__close:hover {
    background: #F4ECE6;
    color: #2B211C;
}

@media (max-width: 576px) {
    .mydream-toast-stack {
        top: 14px;
        right: 14px;
        left: 14px;
        width: auto;
    }
}

@media (prefers-reduced-motion: reduce) {
    .mydream-toast {
        transition: none !important;
    }
}
</style>

<script>
(function () {
    if (window.showMyDreamToast) return;

    window.showMyDreamToast = function (message, type = 'info', title = '') {
        const stack = document.getElementById('mydreamToastStack');
        if (!stack || !message) return;

        // Kompatibilitas pemanggilan showToast(msg, true/false)
        if (typeof type === 'boolean') {
            type = type ? 'error' : 'success';
        }

        const normalizedType = ['success', 'warning', 'error', 'info'].includes(type) ? type : 'info';
        const defaults = {
            success: { title: 'Berhasil', symbol: '✓' },
            warning: { title: 'Perhatian', symbol: '!' },
            error:   { title: 'Gagal Memproses', symbol: '✕' },
            info:    { title: 'Informasi', symbol: 'i' },
        };
        const meta = defaults[normalizedType];

        const toast = document.createElement('div');
        toast.className = `mydream-toast mydream-toast--${normalizedType}`;
        toast.setAttribute('role', 'status');

        const iconWrap = document.createElement('div');
        iconWrap.className = 'mydream-toast__icon';
        iconWrap.textContent = meta.symbol;

        const body = document.createElement('div');
        body.className = 'mydream-toast__body';

        const titleEl = document.createElement('div');
        titleEl.className = 'mydream-toast__title';
        titleEl.textContent = title || meta.title;

        const msgEl = document.createElement('div');
        msgEl.className = 'mydream-toast__message';
        msgEl.textContent = message;

        body.append(titleEl, msgEl);

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'mydream-toast__close';
        closeBtn.setAttribute('aria-label', 'Tutup notifikasi');
        closeBtn.textContent = '×';

        const dismiss = () => {
            if (toast.dataset.closing) return;
            toast.dataset.closing = '1';
            toast.classList.remove('is-visible');
            toast.classList.add('is-leaving');
            setTimeout(() => toast.remove(), 260);
        };

        closeBtn.addEventListener('click', dismiss);
        toast.append(iconWrap, body, closeBtn);
        stack.appendChild(toast);

        requestAnimationFrame(() => toast.classList.add('is-visible'));

        let timer = setTimeout(dismiss, 4200);
        toast.addEventListener('mouseenter', () => clearTimeout(timer));
        toast.addEventListener('mouseleave', () => { timer = setTimeout(dismiss, 2200); });
    };

    window.queueMyDreamToast = function (message, type = 'success', title = '') {
        try {
            sessionStorage.setItem('mydream_pending_toast', JSON.stringify({ message, type, title }));
        } catch (_) {}
    };

    // Alias terpadu agar satu sistem toast dipakai semua modul
    window.showAdminToast = window.showMyDreamToast;
    window.showToast = window.showMyDreamToast;

    document.addEventListener('DOMContentLoaded', function () {
        try {
            const raw = sessionStorage.getItem('mydream_pending_toast');
            if (raw) {
                sessionStorage.removeItem('mydream_pending_toast');
                const parsed = JSON.parse(raw);
                if (parsed && parsed.message) {
                    window.showMyDreamToast(parsed.message, parsed.type || 'success', parsed.title || '');
                }
            }
        } catch (_) {}
    });
})();
</script>
