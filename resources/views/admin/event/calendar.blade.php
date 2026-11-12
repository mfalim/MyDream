@extends('layouts.admin')

@section('title', 'Kalender & Jadwal Acara')
@section('page-title', 'Kalender Event')

@push('styles')
    @vite('resources/css/admin/calendar/index.css')
@endpush

@section('content')
<div class="calendar-page-container">
    {{-- Header --}}
    <div class="calendar-header">
        <div>
            <div class="eyebrow">MASTER SCHEDULE &amp; TIMELINE ACARA</div>
            <h2>Kalender &amp; Jadwal Acara</h2>
            <p>Pantau seluruh jadwal pernikahan klien (Hari H) dan agenda internal WO (Briefing, TM, Gladi) dalam satu kalender terpadu.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.organizer-event.create') }}" class="btn-outline-back">
                <i class="bi bi-calendar-plus"></i>
                <span>+ Agenda Internal WO</span>
            </a>
            <a href="{{ route('admin.events.create') }}" class="btn-emerald">
                <i class="bi bi-plus-lg"></i>
                <span>+ Jadwal Acara Klien</span>
            </a>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="row g-4">
        {{-- Kolom Kiri: Kalender Utama --}}
        <div class="col-lg-8">
            <div class="calendar-main-card">
                {{-- Toolbar Kalender --}}
                <div class="calendar-toolbar">
                    <div>
                        <h3 class="calendar-month-title" id="current-month-year">-</h3>
                        <small class="text-muted" id="calendar-stats" style="font-size: 13px;">Memuat data acara...</small>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="calendar-nav-btn" id="prev-month" title="Bulan Sebelumnya">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button type="button" class="calendar-today-btn" id="today-btn">
                            Hari Ini
                        </button>
                        <button type="button" class="calendar-nav-btn" id="next-month" title="Bulan Berikutnya">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                        <input type="month" id="month-picker" class="form-control form-control-sm border ms-1" style="width: 140px; cursor: pointer;">
                    </div>
                </div>

                {{-- Tabel Grid Tanggal --}}
                <div class="table-responsive">
                    <table class="calendar-table" id="calendar-table">
                        <thead>
                            <tr>
                                <th>Sen</th>
                                <th>Sel</th>
                                <th>Rab</th>
                                <th>Kam</th>
                                <th>Jum</th>
                                <th>Sab</th>
                                <th>Min</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Diisi secara dinamis oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>

                {{-- Footer Legend --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between p-3 border-top bg-light" style="font-size: 12.5px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-1">
                            <span class="cal-badge cal-badge-client px-2 py-1">● Pernikahan Klien (Hari H)</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="cal-badge cal-badge-org px-2 py-1">● Agenda Internal WO (TM / Briefing)</span>
                        </div>
                    </div>
                    <small class="text-muted"><i class="bi bi-cursor-fill me-1"></i> Klik tanggal untuk melihat daftar acara</small>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Daftar Acara Tanggal Terpilih --}}
        <div class="col-lg-4">
            <div class="cal-sidebar-card">
                <div id="event-list">
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-check fs-1 d-block mb-2 text-muted"></i>
                        <h6 class="fw-bold text-dark mb-1">Pilih Tanggal di Kalender</h6>
                        <small>Klik salah satu tanggal untuk melihat agenda acara yang berlangsung pada hari tersebut.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Cepat Event --}}
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header modal-header-emerald">
                <div>
                    <small class="text-uppercase fw-bold text-muted" id="modalEventCategory" style="letter-spacing: 0.08em; font-size: 11px;">DETAIL ACARA</small>
                    <h5 class="modal-title mt-1" id="modalEventTitle">Memuat Acara...</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4" id="modalEventDetail">
                <div class="text-center py-5">
                    <div class="spinner-border text-emerald" role="status"></div>
                    <p class="text-muted small mt-2">Mengambil data detail acara...</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-top p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Tutup</button>
                <div id="modalActionLinks" class="d-flex gap-2"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        let currentYear = new Date().getFullYear();
        let currentMonth = new Date().getMonth();
        let selectedDate = null;

        initCalendar();

        function initCalendar() {
            renderCalendar(currentYear, currentMonth);
            attachEventListeners();
        }

        function attachEventListeners() {
            document.getElementById('prev-month').onclick = () => navigateMonth(-1);
            document.getElementById('next-month').onclick = () => navigateMonth(1);
            document.getElementById('today-btn').onclick = goToToday;
            document.getElementById('month-picker').onchange = handleMonthPicker;
        }

        function navigateMonth(direction) {
            currentMonth += direction;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            } else if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar(currentYear, currentMonth);
        }

        function goToToday() {
            const today = new Date();
            currentYear = today.getFullYear();
            currentMonth = today.getMonth();
            renderCalendar(currentYear, currentMonth);

            // Otomatis pilih hari ini
            const todayStr = formatDate(currentYear, currentMonth, today.getDate());
            setTimeout(() => {
                const todayCell = document.querySelector(`.calendar-day[data-date="${todayStr}"]`);
                if (todayCell) todayCell.click();
            }, 300);
        }

        function handleMonthPicker(event) {
            const val = event.target.value;
            if (!val) return;
            const [year, month] = val.split('-');
            currentYear = parseInt(year);
            currentMonth = parseInt(month) - 1;
            renderCalendar(currentYear, currentMonth);
        }

        function renderCalendar(year, month) {
            updateMonthHeader(year, month);
            updateMonthPicker(year, month);
            generateCalendarDays(year, month);
            fetchMonthEvents(year, month);
        }

        function updateMonthHeader(year, month) {
            document.getElementById('current-month-year').textContent = `${monthNames[month]} ${year}`;
        }

        function updateMonthPicker(year, month) {
            const picker = document.getElementById('month-picker');
            if (picker) {
                picker.value = `${year}-${String(month + 1).padStart(2, '0')}`;
            }
        }

        function formatDate(year, month, day) {
            return `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        }

        function isToday(year, month, date) {
            const today = new Date();
            return date === today.getDate() &&
                month === today.getMonth() &&
                year === today.getFullYear();
        }

        function generateCalendarDays(year, month) {
            const firstDay = new Date(year, month, 1);
            const numDays = new Date(year, month + 1, 0).getDate();
            const firstDayOfWeek = (firstDay.getDay() + 6) % 7; // Senin = 0
            const prevMonthDays = new Date(year, month, 0).getDate();
            const tbody = document.querySelector('#calendar-table tbody');

            tbody.innerHTML = '';
            let date = 1;
            let nextMonthDate = 1;

            for (let week = 0; week < 6; week++) {
                const row = document.createElement('tr');

                for (let day = 0; day < 7; day++) {
                    const cell = document.createElement('td');
                    cell.className = 'calendar-day';

                    const cellIndex = week * 7 + day;
                    let cellDateStr = '';
                    let dayNum = 0;
                    let isOtherMonth = false;

                    if (cellIndex < firstDayOfWeek) {
                        dayNum = prevMonthDays - (firstDayOfWeek - cellIndex - 1);
                        const prevMonth = month === 0 ? 11 : month - 1;
                        const prevYear = month === 0 ? year - 1 : year;
                        cellDateStr = formatDate(prevYear, prevMonth, dayNum);
                        cell.classList.add('other-month');
                        isOtherMonth = true;
                    } else if (date <= numDays) {
                        dayNum = date;
                        cellDateStr = formatDate(year, month, date);
                        if (isToday(year, month, date)) {
                            cell.classList.add('today');
                        }
                        date++;
                    } else {
                        dayNum = nextMonthDate;
                        const nextMonth = month === 11 ? 0 : month + 1;
                        const nextYear = month === 11 ? year + 1 : year;
                        cellDateStr = formatDate(nextYear, nextMonth, dayNum);
                        cell.classList.add('other-month');
                        nextMonthDate++;
                        isOtherMonth = true;
                    }

                    cell.setAttribute('data-date', cellDateStr);

                    // Struktur isi cell
                    const headerDiv = document.createElement('div');
                    headerDiv.className = 'calendar-day-header';
                    headerDiv.innerHTML = `<span class="day-number">${dayNum}</span>`;
                    cell.appendChild(headerDiv);

                    const eventsContainer = document.createElement('div');
                    eventsContainer.className = 'calendar-events-container';
                    cell.appendChild(eventsContainer);

                    // Click event
                    cell.onclick = function() {
                        document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                        this.classList.add('selected');
                        selectedDate = this.getAttribute('data-date');
                        fetchEventsByDate(selectedDate);
                    };

                    row.appendChild(cell);
                }

                tbody.appendChild(row);
                if (date > numDays && nextMonthDate > 7) break;
            }
        }

        function fetchMonthEvents(year, month) {
            const monthStr = formatDate(year, month, 1).slice(0, 7);
            const statsEl = document.getElementById('calendar-stats');

            fetch(`/admin/events/by-month?month=${monthStr}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (statsEl && data.stats) {
                    statsEl.textContent = `${data.stats.total || 0} Acara (${data.stats.client_events || 0} Hari H Klien · ${data.stats.organizer_events || 0} Internal WO)`;
                }
                renderBadgesOnCalendar(data.events || []);
            })
            .catch(err => console.error('Gagal memuat event bulanan:', err));
        }

        function renderBadgesOnCalendar(events) {
            // Bersihkan badge lama
            document.querySelectorAll('.calendar-events-container').forEach(c => c.innerHTML = '');

            events.forEach(ev => {
                const cell = document.querySelector(`.calendar-day[data-date="${ev.date}"]`);
                if (cell) {
                    const container = cell.querySelector('.calendar-events-container');
                    if (container) {
                        const isOrg = ev.type === 'organizer_event';
                        const badge = document.createElement('div');
                        badge.className = `cal-badge ${isOrg ? 'cal-badge-org' : 'cal-badge-client'}`;
                        const icon = isOrg ? '💼' : '💍';
                        const titleText = ev.couple_name || ev.title || 'Event';
                        badge.innerHTML = `${icon} ${titleText}`;
                        badge.title = `${isOrg ? '[Internal WO]' : '[Wedding]'} ${ev.title} (${ev.time || 'Waktu fleksibel'})`;
                        container.appendChild(badge);
                    }
                }
            });
        }

        function fetchEventsByDate(date) {
            const sidebar = document.getElementById('event-list');
            sidebar.style.opacity = '0.5';

            fetch(`/admin/events/by-date?date=${date}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                renderSidebarEvents(data, date);
                sidebar.style.opacity = '1';
            })
            .catch(err => {
                sidebar.style.opacity = '1';
                console.error('Gagal mengambil data tanggal:', err);
            });
        }

        function formatDateDisplay(dateStr) {
            const dateObj = new Date(dateStr + 'T00:00:00');
            return dateObj.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function renderSidebarEvents(data, date) {
            const sidebar = document.getElementById('event-list');
            const events = data.events || [];
            const formattedDate = formatDateDisplay(date);

            let html = `
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <h6 class="fw-bold mb-0" style="color: #073d36; font-size: 15px;">Agenda Acara</h6>
                        <small class="text-muted" style="font-size: 12px;">${formattedDate}</small>
                    </div>
                    <span class="badge" style="background: #edf5f2; color: #073d36; font-size: 12px; font-weight: 700;">
                        ${events.length} Agenda
                    </span>
                </div>
            `;

            if (events.length === 0) {
                html += `
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-1 d-block mb-2 text-muted"></i>
                        <p class="mb-0 fw-semibold text-dark">Tidak Ada Acara</p>
                        <small>Belum ada jadwal pernikahan atau briefing internal pada tanggal ini.</small>
                    </div>
                `;
            } else {
                html += events.map(ev => {
                    const isOrg = ev.type === 'organizer_event';
                    const borderClass = isOrg ? 'org-border' : 'client-border';
                    const iconClass = isOrg ? 'bi-briefcase-fill text-warning' : 'bi-heart-fill text-danger';
                    const typeLabel = isOrg ? 'Internal WO' : 'Pernikahan Klien';
                    const title = isOrg ? ev.title : (ev.couple_name || ev.title);

                    return `
                        <div class="cal-event-item ${borderClass}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="small text-muted d-flex align-items-center gap-1" style="font-size: 11px; font-weight: 600;">
                                        <i class="bi ${iconClass}"></i>
                                        <span>${typeLabel}</span>
                                        <span>·</span>
                                        <span>${ev.schedule}</span>
                                    </div>
                                    <strong class="d-block mt-1" style="color: #173f39; font-size: 14.5px;">${title}</strong>
                                </div>
                                <span class="badge ${isOrg ? 'badge-status-scheduled' : 'badge-status-completed'}" style="font-size: 10px;">
                                    ${ev.status}
                                </span>
                            </div>

                            <div class="small text-muted mb-3" style="font-size: 12.5px;">
                                <div class="mb-1"><i class="bi bi-geo-alt me-1 text-secondary"></i> ${ev.venue || 'Venue belum ditentukan'}</div>
                                ${!isOrg && ev.package ? `<div><i class="bi bi-box-seam me-1 text-secondary"></i> Paket: <strong>${ev.package}</strong> (${ev.vendor_count} vendor)</div>` : ''}
                                ${isOrg && ev.priority ? `<div><i class="bi bi-flag-fill me-1 text-secondary"></i> Prioritas: <strong>${ev.priority}</strong></div>` : ''}
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-light border flex-grow-1" onclick="openEventDetailModal(${ev.id}, '${ev.type}')" style="font-size: 12px; font-weight: 600;">
                                    <i class="bi bi-eye me-1"></i> Detail Cepat
                                </button>
                                <a href="${ev.url}" class="btn btn-sm btn-emerald px-3" style="font-size: 12px;">
                                    Buka Acara <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            sidebar.innerHTML = html;
        }
    });

    // Modal Detail Cepat Handler
    function openEventDetailModal(eventId, eventType) {
        const modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
        const modalCategory = document.getElementById('modalEventCategory');
        const modalTitle = document.getElementById('modalEventTitle');
        const modalBody = document.getElementById('modalEventDetail');
        const modalActions = document.getElementById('modalActionLinks');

        modalTitle.textContent = 'Memuat Data...';
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-emerald" role="status"></div>
                <p class="text-muted small mt-2">Mengambil data detail acara...</p>
            </div>
        `;
        modal.show();

        const url = `/admin/events/${eventId}/detail?type=${eventType}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                modalBody.innerHTML = `<div class="alert alert-danger mb-0">${data.error}</div>`;
                return;
            }

            const isOrg = data.type === 'organizer_event';
            modalCategory.textContent = isOrg ? 'AGENDA INTERNAL WO' : 'EVENT PERNIKAHAN KLIEN';
            modalTitle.textContent = data.title;

            let bodyHtml = '';

            if (isOrg) {
                bodyHtml = `
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-light border">
                                <small class="text-muted d-block" style="font-size: 11px;">TANGGAL</small>
                                <strong>${data.event_date}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-light border">
                                <small class="text-muted d-block" style="font-size: 11px;">WAKTU</small>
                                <strong>${data.schedule}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-light border">
                                <small class="text-muted d-block" style="font-size: 11px;">PRIORITAS & STATUS</small>
                                <strong>${data.priority} · ${data.status}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">LOKASI / TEMPAT</label>
                        <div class="p-2 px-3 rounded-3 bg-light border d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt text-danger"></i>
                            <span>${data.venue}</span>
                        </div>
                    </div>

                    ${data.description ? `
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">DESKRIPSI PEMBAHASAN</label>
                            <div class="p-3 rounded-3 bg-light border" style="font-size: 13.5px; line-height: 1.5; white-space: pre-line;">${data.description}</div>
                        </div>
                    ` : ''}

                    ${data.attendees ? `
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">PESERTA / KRU TERLIBAT</label>
                            <div class="p-2 px-3 rounded-3 bg-light border">${data.attendees}</div>
                        </div>
                    ` : ''}

                    ${data.notes ? `
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-muted">CATATAN KHUSUS</label>
                            <div class="p-3 rounded-3 bg-warning-subtle border text-dark" style="font-size: 13.5px;">${data.notes}</div>
                        </div>
                    ` : ''}
                `;
            } else {
                bodyHtml = `
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-light border">
                                <small class="text-muted d-block" style="font-size: 11px;">TANGGAL ACARA</small>
                                <strong>${data.event_date}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-light border">
                                <small class="text-muted d-block" style="font-size: 11px;">SUSUNAN WAKTU</small>
                                <strong>${data.schedule}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-light border">
                                <small class="text-muted d-block" style="font-size: 11px;">STATUS ACARA</small>
                                <strong>${data.status}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">VENUE & LOKASI</label>
                            <div class="p-2 px-3 rounded-3 bg-light border">
                                <strong>${data.venue}</strong>
                                ${data.venue_city ? `<div class="text-muted small">${data.venue_city}</div>` : ''}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">KOORDINATOR / LEAD DIRECTOR</label>
                            <div class="p-2 px-3 rounded-3 bg-light border">
                                <strong>${data.lead_director}</strong>
                                ${data.lead_director_phone ? `<div class="text-muted small">${data.lead_director_phone}</div>` : ''}
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">VENDOR BERTUGAS (${data.vendor_count || 0})</label>
                        <div class="border rounded-3 p-2 bg-light d-flex flex-wrap gap-2" style="max-height: 140px; overflow-y: auto;">
                            ${(data.vendors || []).map(v => `
                                <span class="badge px-2 py-2 bg-white text-dark border d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                                    <i class="bi bi-shop text-success"></i>
                                    <strong>${v.name}</strong>
                                    <span class="text-muted">(${v.category})</span>
                                </span>
                            `).join('') || '<span class="text-muted small p-2">Belum ada vendor terjadwal.</span>'}
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-bold small text-muted">TIM KRU OPERASIONAL (${data.team_count || 0})</label>
                        <div class="border rounded-3 p-2 bg-white d-flex flex-wrap gap-2" style="max-height: 140px; overflow-y: auto;">
                            ${(data.team_members || []).map(tm => `
                                <span class="badge px-2 py-2 rounded-pill ${tm.is_coordinator ? 'bg-success text-white' : 'bg-light text-dark border'}" style="font-size: 11.5px;">
                                    <i class="bi bi-person-fill"></i> ${tm.name} <small class="opacity-75">(${tm.role})</small>
                                </span>
                            `).join('') || '<span class="text-muted small p-2">Belum ada tim yang ditugaskan.</span>'}
                        </div>
                    </div>
                `;
            }

            modalBody.innerHTML = bodyHtml;

            // Tombol Aksi di Modal Footer
            modalActions.innerHTML = `
                <a href="${data.edit_url}" class="btn btn-outline-secondary px-3">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="${data.url}" class="btn-emerald px-3">
                    Buka Halaman Lengkap <i class="bi bi-arrow-right ms-1"></i>
                </a>
            `;
        })
        .catch(err => {
            console.error('Error fetching detail:', err);
            modalBody.innerHTML = `<div class="alert alert-danger mb-0">Gagal mengambil detail acara. Silakan buka halaman acara langsung.</div>`;
        });
    }
</script>
@endpush
@endsection
