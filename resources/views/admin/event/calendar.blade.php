@extends('layouts.admin')
@section('title', 'Kalender & Tanggal Acara')
@section('page-title', 'Kalender Event')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <p class="text-muted mb-1" style="font-size: 0.875rem;">MASTER EVENT SCHEDULE & MILESTONE</p>
                <h2 class="fw-bold mb-2">Kalender & Tanggal Acara Berlangsung</h2>
                <p class="text-muted" style="font-size: 0.9rem;">Menampilkan jadwal rangkaian acara, ganti next, dan timeline operasional WO PROJECT</p>
            </div>
            <button class="btn btn-dark"><i class="bi bi-plus-circle"></i> Jadwal Acara Baru</button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-1" id="current-month-year"></h4>
                            <small class="text-muted" id="calendar-stats"></small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary" id="prev-month"><i class="bi bi-chevron-left"></i></button>
                            <button class="btn btn-sm btn-outline-secondary" id="today-btn">Hari Ini</button>
                            <button class="btn btn-sm btn-outline-secondary" id="next-month"><i class="bi bi-chevron-right"></i></button>
                            <input type="month" class="btn btn-sm btn-outline-secondary" id="month-picker" style="cursor:pointer;">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless" id="calendar-table" style="table-layout: fixed;">
                            <thead>
                                <tr class="text-center text-muted" style="font-size: 0.875rem;">
                                    <th style="width: 14.28%;">Sen</th>
                                    <th style="width: 14.28%;">Sel</th>
                                    <th style="width: 14.28%;">Rab</th>
                                    <th style="width: 14.28%;">Kam</th>
                                    <th style="width: 14.28%;">Jum</th>
                                    <th style="width: 14.28%;">Sab</th>
                                    <th style="width: 14.28%;">Min</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="mt-4 d-flex gap-4 small">
                        <div><span class="badge bg-dark me-2">●</span> Client Events (Wedding)</div>
                        <div><span class="badge bg-primary me-2">●</span> Organizer Events (Internal)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3" id="event-list">
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-check fs-1"></i>
                        <p>Klik tanggal untuk melihat acara</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table td {
    border: 1px solid #e9ecef;
}
.badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    font-weight: 500;
}
.calendar-day:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s;
}
.calendar-day.selected {
    background-color: #e7f3ff;
    border: 2px solid #0d6efd;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    let currentYear = new Date().getFullYear();
    let currentMonth = new Date().getMonth();

    // Initialize
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
    }

    function handleMonthPicker(event) {
        const [year, month] = event.target.value.split('-');
        currentYear = parseInt(year);
        currentMonth = parseInt(month) - 1;
        renderCalendar(currentYear, currentMonth);
    }

    function renderCalendar(year, month) {
        updateMonthHeader(year, month);
        updateMonthPicker(year, month);
        generateCalendarDays(year, month);
        attachDayClickEvents();
        fetchMonthEvents(year, month);
    }

    function updateMonthHeader(year, month) {
        document.getElementById('current-month-year').textContent = `${monthNames[month]} ${year}`;
    }

    function updateMonthPicker(year, month) {
        const picker = document.getElementById('month-picker');
        picker.value = `${year}-${String(month + 1).padStart(2, '0')}`;
    }

    function generateCalendarDays(year, month) {
        const firstDay = new Date(year, month, 1);
        const numDays = new Date(year, month + 1, 0).getDate();
        const firstDayOfWeek = firstDay.getDay();
        const prevMonthDays = new Date(year, month, 0).getDate();
        const tbody = document.querySelector('#calendar-table tbody');
        
        tbody.innerHTML = '';
        let date = 1;
        let nextMonthDate = 1;

        for (let week = 0; week < 6; week++) {
            const row = document.createElement('tr');
            
            for (let day = 0; day < 7; day++) {
                const cell = createDayCell(week, day, firstDayOfWeek, date, nextMonthDate, numDays, year, month, prevMonthDays);
                
                if (week * 7 + day >= firstDayOfWeek && date <= numDays) {
                    date++;
                } else if (date > numDays) {
                    nextMonthDate++;
                }
                
                row.appendChild(cell);
            }
            
            tbody.appendChild(row);
            if (date > numDays && nextMonthDate > 7) break;
        }
    }

    function createDayCell(week, day, firstDayOfWeek, date, nextMonthDate, numDays, year, month, prevMonthDays) {
        const cell = document.createElement('td');
        const dayNumber = document.createElement('div');
        const cellIndex = week * 7 + day;
        
        cell.className = 'p-2 calendar-day';
        cell.style.cssText = 'height:100px;vertical-align:top;cursor:pointer';
        dayNumber.className = 'small';

        if (cellIndex < firstDayOfWeek) {
            // Previous month days
            const prevDay = prevMonthDays - (firstDayOfWeek - cellIndex - 1);
            const prevMonth = month === 0 ? 11 : month - 1;
            const prevYear = month === 0 ? year - 1 : year;
            
            dayNumber.textContent = prevDay;
            cell.classList.add('text-muted');
            cell.setAttribute('data-date', formatDate(prevYear, prevMonth, prevDay));
            
        } else if (date <= numDays) {
            // Current month days
            dayNumber.textContent = date;
            dayNumber.classList.add('fw-bold');
            cell.setAttribute('data-date', formatDate(year, month, date));
            
            // Highlight today
            if (isToday(year, month, date)) {
                cell.style.backgroundColor = '#fff3cd';
            }
            
        } else {
            // Next month days
            const nextMonth = month === 11 ? 0 : month + 1;
            const nextYear = month === 11 ? year + 1 : year;
            
            dayNumber.textContent = nextMonthDate;
            cell.classList.add('text-muted');
            cell.setAttribute('data-date', formatDate(nextYear, nextMonth, nextMonthDate));
        }

        cell.appendChild(dayNumber);
        return cell;
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

    function attachDayClickEvents() {
        document.querySelectorAll('.calendar-day').forEach(day => {
            day.onclick = function() {
                document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                this.classList.add('selected');
                fetchEventData(this.getAttribute('data-date'));
            };
        });
    }

    function fetchMonthEvents(year, month) {
        const monthStr = formatDate(year, month, 1).slice(0, 7);
        
        fetch(`/admin/events/by-month?month=${monthStr}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            updateCalendarStats(data.stats);
            addEventBadgesToCalendar(data.events);
        })
        .catch(error => console.error('Error fetching month events:', error));
    }

    function updateCalendarStats(stats) {
        if (stats) {
            document.getElementById('calendar-stats').textContent = `${stats.total || 0} Acara Terjadwal`;
        }
    }

    function addEventBadgesToCalendar(events) {
        if (!events) return;
        
        events.forEach(event => {
            const cell = document.querySelector(`[data-date="${event.date}"]`);
            if (cell) {
                const badge = document.createElement('div');
                const isOrganizerEvent = event.type === 'organizer_event';
                badge.className = `badge ${isOrganizerEvent ? 'bg-primary' : 'bg-dark'} text-white small mt-1`;
                badge.style.fontSize = '0.65rem';
                badge.textContent = event.couple_name || 'Event';
                badge.title = isOrganizerEvent ? 'Internal Event' : 'Client Event';
                cell.appendChild(badge);
            }
        });
    }

    function fetchEventData(date) {
        const sidebar = document.getElementById('event-list');
        sidebar.style.opacity = '0.5';
        
        fetch(`/admin/events/by-date?date=${date}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            updateSidebar(data, date);
            sidebar.style.opacity = '1';
        })
        .catch(error => {
            sidebar.style.opacity = '1';
            alert('Gagal mengambil data acara. Silakan coba lagi.');
            console.error('Error fetching event data:', error);
        });
    }

    function updateSidebar(data, date) {
        const formattedDate = formatDateDisplay(date);
        const eventList = document.getElementById('event-list');
        const events = data.events || [];
        
        let html = buildSidebarHeader(events.length, formattedDate);
        html += events.length === 0 ? buildEmptyState() : buildEventList(events);
        
        eventList.innerHTML = html;
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

    function buildSidebarHeader(count, date) {
        return `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">Daftar Acara (${count})</h6>
                <small class="text-muted">${date}</small>
            </div>
        `;
    }

    function buildEmptyState() {
        return `
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                <p>Tidak ada acara pada tanggal ini</p>
            </div>
        `;
    }

    function buildEventList(events) {
        return events.map((event, index) => {
            const isOrganizerEvent = event.type === 'organizer_event';
            const borderColor = isOrganizerEvent ? 'primary' : ['success', 'warning', 'info', 'danger'][index % 4];
            const eventIcon = isOrganizerEvent ? 'bi-briefcase-fill' : 'bi-heart-fill';
            const eventLabel = isOrganizerEvent ? 'Internal Event' : 'Client Event';
            
            return `
                <div class="border-start border-4 border-${borderColor} ps-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <div class="small text-muted">
                                <i class="bi ${eventIcon} me-1"></i>
                                ${event.time || 'MALAM'} • ${eventLabel}
                            </div>
                            <div class="fw-bold">${event.couple_name || 'Nama Pengantin'}</div>
                            ${isOrganizerEvent && event.description ? `<div class="small text-muted mt-1">${event.description}</div>` : ''}
                        </div>
                        <span class="badge bg-${borderColor}">${event.status || 'Terjadwal'}</span>
                    </div>
                    <div class="small text-muted">
                        <div><i class="bi bi-geo-alt me-1"></i> ${event.venue || 'Venue'}</div>
                        <div><i class="bi bi-clock me-1"></i> ${event.schedule || 'Jadwal'}</div>
                        ${isOrganizerEvent && event.priority ? `<div><i class="bi bi-flag-fill me-1"></i> Priority: ${event.priority.toUpperCase()}</div>` : ''}
                    </div>
                    <button class="btn btn-sm btn-outline-dark w-100 mt-2" onclick="viewEventDetail(${event.id}, '${event.type}')">
                        Rincian ${isOrganizerEvent ? 'Event' : 'Acara'} ›
                    </button>
                </div>
            `;
        }).join('');
    }

    window.viewEventDetail = function(eventId, eventType) {
        if (eventType === 'organizer_event') {
            window.location.href = `/admin/organizer-event/${eventId}`;
        } else {
            window.location.href = `/admin/event/${eventId}`;
        }
    };
});
</script>
@endsection
