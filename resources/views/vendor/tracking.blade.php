@extends('vendor.layouts.app')

@section('title', 'Tracking Tugas')

@section('content')

<div style="padding:20px;">

    <div style="margin-bottom:25px;">
        <span>VENDOR WORKSPACE</span>
        <h1>Tracking Tugas</h1>
        <p>Pantau kegiatan dan perkembangan tugas vendor.</p>
    </div>

    <div style="
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(150px,1fr));
        gap:15px;
        margin-bottom:25px;
    ">

        <div class="panel">
            <h3>{{ $totalTugas }}</h3>
            <p>Total Tugas</p>
        </div>

        <div class="panel">
            <h3>{{ $selesai }}</h3>
            <p>Selesai</p>
        </div>

        <div class="panel">
            <h3>{{ $berjalan }}</h3>
            <p>Sedang Berjalan</p>
        </div>

        <div class="panel">
            <h3>{{ $menunggu }}</h3>
            <p>Menunggu</p>
        </div>

    </div>

    <div class="panel">

        <h2>Daftar Tugas Vendor</h2>

        <div style="overflow-x:auto;">

            <table style="width:100%;border-collapse:collapse;">

                <thead>
                    <tr style="background:#f5f5f5;">
                        <th style="padding:12px;text-align:left;">No</th>
                        <th style="padding:12px;text-align:left;">Kegiatan</th>
                        <th style="padding:12px;text-align:left;">Acara</th>
                        <th style="padding:12px;text-align:left;">Lokasi</th>
                        <th style="padding:12px;text-align:left;">Waktu</th>
                        <th style="padding:12px;text-align:left;">Status</th>
                        <th style="padding:12px;text-align:left;">Catatan</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($schedules as $index => $schedule)

                        <tr style="border-bottom:1px solid #eee;">

                            <td style="padding:12px;">
                                {{ $index + 1 }}
                            </td>

                            <td style="padding:12px;">
                                {{ $schedule->activity }}
                            </td>

                            <td style="padding:12px;">
                                {{ $schedule->event_name ?? '-' }}
                            </td>

                            <td style="padding:12px;">
                                {{ $schedule->location ?? '-' }}
                            </td>

                            <td style="padding:12px;">
                                {{ $schedule->start_time ?? '-' }}
                                -
                                {{ $schedule->end_time ?? '-' }}
                            </td>

                            <td style="padding:12px;">
                                {{ $schedule->status ?? 'Belum ditentukan' }}
                            </td>

                            <td style="padding:12px;">
                                {{ $schedule->notes ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7"
                                style="padding:20px;text-align:center;">
                                Belum ada tugas yang tersedia.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
