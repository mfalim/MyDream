@extends('vendor.layouts.app')

@section('title', 'Kalender & Jadwal Acara')

@section('content')

<style>
.calendar-page {
    padding: 0;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 25px;
}

.calendar-header h1 {
    margin: 5px 0;
}

.calendar-filter {
    display: flex;
    gap: 8px;
    align-items: center;
}

.calendar-filter select,
.calendar-filter button {
    padding: 9px 12px;
    border: 1px solid #ddd;
    border-radius: 7px;
}

.calendar-filter button {
    background: #344b78;
    color: white;
    cursor: pointer;
}

.calendar-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.calendar-table th,
.calendar-table td {
    border: 1px solid #eee;
    padding: 10px;
    vertical-align: top;
    width: 14.28%;
}

.calendar-table th {
    background: #f5f6fa;
    text-align: center;
}

.calendar-day {
    min-height: 90px;
}

.calendar-date {
    display: block;
    padding: 0;
    border: 0;
    background: transparent;
    font-family: inherit;
    text-decoration: none;
    color: #344b78;
    font-weight: bold;
    cursor: pointer;
}

.calendar-day.outside {
    background: #fafafa;
    color: #aaa;
}

.calendar-event {
    display: block;
    width: 100%;
    border: 0;
    background: #e8edf8;
    color: #344b78;
    padding: 5px;
    margin-top: 6px;
    border-radius: 5px;
    font-family: inherit;
    font-size: 15px;
    text-align: left;
    text-decoration: none;
    cursor: pointer;
}

.calendar-event:hover {
    background: #dce9dc;
}

.calendar-dialog {
    width: min(620px, calc(100% - 32px));
    max-height: min(80vh, 760px);
    overflow-y: auto;
    padding: 0;
    border: 0;
    border-radius: 16px;
    color: #263b35;
    box-shadow: 0 24px 70px rgba(0, 0, 0, .24);
}

.calendar-dialog::backdrop {
    background: rgba(15, 24, 21, .55);
}

.calendar-dialog-content {
    padding: 24px;
}

.calendar-dialog-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 20px;
}

.calendar-dialog-header h2 {
    margin: 4px 0;
    color: #294b43;
}

.calendar-dialog-header p {
    margin: 0;
    color: #718078;
}

.calendar-dialog-close {
    flex: 0 0 auto;
    padding: 8px 12px;
    border: 1px solid #dce4df;
    border-radius: 8px;
    background: white;
    color: #294b43;
    cursor: pointer;
}

.calendar-dialog-event {
    padding: 16px 0;
    border-top: 1px solid #e8eeea;
}

.calendar-dialog-event h3 {
    margin: 0 0 12px;
    color: #294b43;
}

.calendar-dialog-fields {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.calendar-dialog-field {
    min-width: 0;
    padding: 10px 12px;
    border-radius: 9px;
    background: #f5f7f5;
}

.calendar-dialog-field span {
    display: block;
    margin-bottom: 4px;
    color: #718078;
    font-size: 12px;
}

.calendar-dialog-field strong {
    overflow-wrap: anywhere;
}

.calendar-dialog-notes {
    margin: 12px 0 0;
    overflow-wrap: anywhere;
}

.calendar-dialog-empty {
    margin: 0;
    padding: 16px;
    border-radius: 9px;
    background: #f5f7f5;
    color: #718078;
}

.event-detail {
    background: white;
    padding: 20px;
    margin-top: 25px;
    border-radius: 10px;
}

@media(max-width: 700px) {
    .calendar-table th,
    .calendar-table td {
        padding: 4px;
        font-size: 14px;
    }

    .calendar-day {
        min-height: 65px;
    }

    .calendar-event {
        font-size: 12px;
        padding: 3px;
    }

    .calendar-dialog-content {
        padding: 18px;
    }

    .calendar-dialog-fields {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="calendar-page">

    <div class="calendar-header">
        <div>
            <span>{{ $vendor->name }}</span>
            <h1>Kalender & Jadwal Acara</h1>
            <p>Acara yang memiliki penugasan untuk vendor Anda.</p>
        </div>

        <form action="{{ route('vendor.kalender') }}"
              method="GET"
              class="calendar-filter">

            <select name="bulan">
                @foreach($namaBulan as $nomor => $nama)
                    <option value="{{ $nomor }}"
                        {{ $bulan == $nomor ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>

            <select name="tahun">
                @for($y = now()->year - 5; $y <= now()->year + 5; $y++)
                    <option value="{{ $y }}"
                        {{ $tahun == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>

            <button type="submit">Tampilkan</button>
        </form>
    </div>

    <div style="overflow-x:auto;">
        <table class="calendar-table">
            <thead>
                <tr>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                        <th>{{ $hari }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($grid as $minggu)
                    <tr>
                        @foreach($minggu as $tanggal)
                            <td>
                                <div class="calendar-day
                                    {{ !$tanggal['in_month'] ? 'outside' : '' }}">

                                    @if ($tanggal['in_month'])
                                        <button type="button" class="calendar-date" data-calendar-date="{{ $tanggal['date'] }}">
                                            {{ $tanggal['day'] }}
                                        </button>
                                    @else
                                        <span class="calendar-date">{{ $tanggal['day'] }}</span>
                                    @endif

                                    @foreach(($events[$tanggal['date']] ?? []) as $event)
                                        <button
                                            type="button"
                                            class="calendar-event"
                                            data-calendar-event
                                            data-event-date="{{ $tanggal['date'] }}"
                                            data-event-name="{{ $event->name }}"
                                            data-event-type="{{ $event->event_type ?? '-' }}"
                                            data-event-guests="{{ $event->guest_count ?? '-' }}"
                                            data-event-start="{{ $event->start_time ?? '-' }}"
                                            data-event-end="{{ $event->end_time ?? '-' }}"
                                            data-event-status="{{ $event->status ?? '-' }}"
                                            data-event-notes="{{ $event->notes ?? '-' }}"
                                        >

                                            {{ $event->name }}

                                            @if($event->start_time)
                                                <br>
                                                {{ substr($event->start_time, 0, 5) }}
                                            @endif
                                        </button>
                                    @endforeach

                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <dialog id="calendar-event-dialog" class="calendar-dialog" aria-labelledby="calendar-dialog-title">
        <div class="calendar-dialog-content">
            <div class="calendar-dialog-header">
                <div>
                    <p id="calendar-dialog-date"></p>
                    <h2 id="calendar-dialog-title">Detail Acara</h2>
                </div>
                <form method="dialog">
                    <button class="calendar-dialog-close" aria-label="Tutup detail acara">Tutup</button>
                </form>
            </div>
            <div id="calendar-dialog-events"></div>
        </div>
    </dialog>

</div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dialog = document.getElementById('calendar-event-dialog');
            const dialogDate = document.getElementById('calendar-dialog-date');
            const dialogTitle = document.getElementById('calendar-dialog-title');
            const dialogEvents = document.getElementById('calendar-dialog-events');
            const eventButtons = Array.from(document.querySelectorAll('[data-calendar-event]'));

            const formatDate = (date) => {
                const [year, month, day] = date.split('-').map(Number);
                return new Date(year, month - 1, day).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                });
            };

            const appendField = (container, label, value) => {
                const field = document.createElement('div');
                field.className = 'calendar-dialog-field';
                const fieldLabel = document.createElement('span');
                fieldLabel.textContent = label;
                const fieldValue = document.createElement('strong');
                fieldValue.textContent = value || '-';
                field.append(fieldLabel, fieldValue);
                container.append(field);
            };

            const openDetails = (date, events) => {
                dialogDate.textContent = formatDate(date);
                dialogEvents.replaceChildren();

                if (!events.length) {
                    dialogTitle.textContent = 'Tidak ada acara';
                    const empty = document.createElement('p');
                    empty.className = 'calendar-dialog-empty';
                    empty.textContent = 'Belum ada acara pada tanggal ini.';
                    dialogEvents.append(empty);
                } else {
                    dialogTitle.textContent = events.length === 1 ? 'Detail Acara' : `${events.length} Acara`;
                    events.forEach((button) => {
                        const event = button.dataset;
                        const card = document.createElement('article');
                        card.className = 'calendar-dialog-event';
                        const title = document.createElement('h3');
                        title.textContent = event.eventName;
                        const fields = document.createElement('div');
                        fields.className = 'calendar-dialog-fields';
                        appendField(fields, 'Jenis acara', event.eventType);
                        appendField(fields, 'Jumlah tamu', event.eventGuests);
                        appendField(fields, 'Waktu mulai', event.eventStart);
                        appendField(fields, 'Waktu selesai', event.eventEnd);
                        appendField(fields, 'Status', event.eventStatus);
                        const notes = document.createElement('p');
                        notes.className = 'calendar-dialog-notes';
                        notes.textContent = `Catatan: ${event.eventNotes || '-'}`;
                        card.append(title, fields, notes);
                        dialogEvents.append(card);
                    });
                }

                dialog.showModal();
            };

            document.querySelectorAll('[data-calendar-date]').forEach((button) => {
                button.addEventListener('click', () => {
                    const date = button.dataset.calendarDate;
                    openDetails(date, eventButtons.filter((event) => event.dataset.eventDate === date));
                });
            });

            eventButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    openDetails(button.dataset.eventDate, [button]);
                });
            });

            dialog.addEventListener('click', (event) => {
                if (event.target === dialog) dialog.close();
            });
        });
    </script>
@endpush
