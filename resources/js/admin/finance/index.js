/**
 * Admin Finance Center (Pusat Keuangan & Kas) Interactive Scripts.
 */
window.openProofPreview = function openProofPreview(url, title) {
    const modalEl = document.getElementById('proofPreviewModal');
    const titleEl = document.getElementById('proofPreviewTitle');
    const container = document.getElementById('proofPreviewContainer');
    const newTabBtn = document.getElementById('proofOpenNewTabBtn');
    if (!modalEl || !container) return;

    if (titleEl) titleEl.textContent = title || 'Preview Bukti Transfer';
    if (newTabBtn) newTabBtn.href = url;

    if (url.toLowerCase().endsWith('.pdf')) {
        container.innerHTML = `<iframe src="${url}" style="width:100%; height:65vh; border:0; border-radius:8px; background:#fff;"></iframe>`;
    } else {
        container.innerHTML = `<img src="${url}" alt="Bukti Transfer">`;
    }

    const modal = window.bootstrap?.Modal.getOrCreateInstance(modalEl);
    modal?.show();
};

window.exportOrganizerReportCsv = function exportOrganizerReportCsv() {
    const table = document.getElementById('organizerReportTable');
    if (!table) return;

    const rows = Array.from(table.querySelectorAll('tr')).map((tr) =>
        Array.from(tr.querySelectorAll('th, td'))
            .map((cell) => {
                const text = cell.innerText.replace(/\s+/g, ' ').trim();
                return '"' + text.replace(/"/g, '""') + '"';
            })
            .join(',')
    );

    const blob = new Blob(['\uFEFF' + rows.join('\n')], {
        type: 'text/csv;charset=utf-8;',
    });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download =
        'laporan-keuangan-organizer-' +
        new Date().toISOString().slice(0, 10) +
        '.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
};

function initFinanceWorkspaceWidgets() {
    document.querySelectorAll('.vendor-scheme-select').forEach(function (select) {
        const targetSelector = select.getAttribute('data-target');
        const targetEl = targetSelector
            ? document.querySelector(targetSelector)
            : null;
        if (!targetEl) return;

        const syncVisibility = () => {
            targetEl.style.display =
                select.value === 'custom' ? 'block' : 'none';
        };
        syncVisibility();
        select.addEventListener('change', syncVisibility);
    });

    const bookingSearchInput = document.getElementById('bookingSearchInput');
    const bookingClearBtn = document.getElementById('bookingClearSearchBtn');
    const filterButtons = document.querySelectorAll(
        '#bookingStatusFilterGroup button[data-filter]'
    );
    const bookingRows = document.querySelectorAll(
        '#bookingFinanceTable tbody tr.booking-ledger-row'
    );
    const noMatchRow = document.getElementById('bookingNoMatchRow');

    let currentFilter = 'all';

    function applyBookingFilter() {
        const query = (bookingSearchInput ? bookingSearchInput.value : '')
            .toLowerCase()
            .trim();
        if (bookingClearBtn) {
            bookingClearBtn.classList.toggle('d-none', query.length === 0);
        }

        let visibleCount = 0;
        bookingRows.forEach(function (row) {
            const rowSearch = (
                row.getAttribute('data-search') || ''
            ).toLowerCase();
            const rowStatus = row.getAttribute('data-status') || '';

            const matchSearch = query === '' || rowSearch.includes(query);
            let matchStatus = false;

            if (currentFilter === 'all') {
                matchStatus = true;
            } else if (currentFilter === 'paid') {
                matchStatus = rowStatus === 'paid';
            } else if (currentFilter === 'dp_paid') {
                matchStatus = rowStatus === 'dp_paid';
            } else if (currentFilter === 'unpaid') {
                matchStatus =
                    rowStatus === 'unpaid' ||
                    rowStatus === 'awaiting_verification';
            }

            if (matchSearch && matchStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noMatchRow) {
            noMatchRow.style.display =
                visibleCount === 0 && bookingRows.length > 0 ? '' : 'none';
        }
    }

    if (bookingSearchInput) {
        bookingSearchInput.addEventListener('input', applyBookingFilter);
    }

    if (bookingClearBtn) {
        bookingClearBtn.addEventListener('click', function () {
            bookingSearchInput.value = '';
            applyBookingFilter();
            bookingSearchInput.focus();
        });
    }

    filterButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterButtons.forEach((b) => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter') || 'all';
            applyBookingFilter();
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initFinanceWorkspaceWidgets();

    const workspaceEl = document.getElementById('financeWorkspace');
    workspaceEl?.addEventListener(
        'ajax-workspace:updated',
        initFinanceWorkspaceWidgets
    );

    const bookingSelect = document.getElementById('manualBookingSelect');
    const typeSelect = document.getElementById('manualTypeSelect');
    const amountInput = document.getElementById('manualAmountInput');

    function syncManualAmount() {
        if (!bookingSelect || !typeSelect || !amountInput) return;
        const opt = bookingSelect.selectedOptions[0];
        if (!opt || !opt.value) return;

        const type = typeSelect.value;
        if (type === 'dp') {
            amountInput.value = opt.getAttribute('data-dp') || '';
        } else if (type === 'remaining') {
            amountInput.value = opt.getAttribute('data-remaining') || '';
        } else {
            amountInput.value = opt.getAttribute('data-total') || '';
        }
    }

    bookingSelect?.addEventListener('change', syncManualAmount);
    typeSelect?.addEventListener('change', syncManualAmount);
});
