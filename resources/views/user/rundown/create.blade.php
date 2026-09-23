{{-- resources/views/user/rundown/create.blade.php --}}
@extends('user.layouts.app')

@section('title', 'Tambah Jadwal Acara Baru')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/wo-shared.css') }}">
    <style>
        .form-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }
        .form-section h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #1f2937;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #374151;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .repeater-item {
            background: #f9fafb;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 12px;
            position: relative;
        }
        .repeater-item .remove-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #ef4444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }
        .add-btn {
            background: #16302a;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
        }
        .add-btn:hover {
            background: #0f1f1a;
        }
        .btn-primary {
            background: #16302a;
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
        }
        .btn-primary:hover {
            background: #0f1f1a;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }
    </style>
@endpush

@section('content')
<main class="wo-page" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
    
    <div style="margin-bottom: 32px;">
        <a href="{{ route('user.rundown') }}" class="wo-link"><i class="bi bi-arrow-left"></i> Kembali ke Kalender</a>
        <h1 style="font-size: 32px; font-weight: 700; margin: 16px 0 8px;">Tambah Jadwal Acara Baru</h1>
        <p style="color: #6b7280;">Lengkapi informasi acara, vendor, dan tim yang akan bertugas.</p>
    </div>

    <form action="{{ route('user.rundown.store') }}" method="POST">
        @csrf

        {{-- Informasi Acara --}}
        <div class="form-section">
            <h3><i class="bi bi-calendar-event"></i> Informasi Acara</h3>
            
            <div class="form-group">
                <label for="booking_id">Pilih Booking / Client <span style="color: red;">*</span></label>
                <select name="booking_id" id="booking_id" required>
                    <option value="">-- Pilih Booking --</option>
                    @foreach(\App\Models\Booking::with('client')->where('status', 'approved')->get() as $booking)
                        <option value="{{ $booking->id }}">
                            {{ $booking->client->groom_name }} & {{ $booking->client->bride_name }} - {{ $booking->venue_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="name">Nama Acara <span style="color: red;">*</span></label>
                <input type="text" name="name" id="name" placeholder="Contoh: Resepsi Pernikahan" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="event_type">Tipe Acara <span style="color: red;">*</span></label>
                    <select name="event_type" id="event_type" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="akad">Akad Nikah</option>
                        <option value="resepsi">Resepsi</option>
                        <option value="akad_resepsi">Akad + Resepsi</option>
                        <option value="gladi">Gladi Resik</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="event_date">Tanggal Acara <span style="color: red;">*</span></label>
                    <input type="date" name="event_date" id="event_date" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="guest_count">Jumlah Tamu <span style="color: red;">*</span></label>
                    <input type="number" name="guest_count" id="guest_count" min="1" placeholder="Contoh: 500" required>
                </div>

                <div class="form-group">
                    <label for="package_id">Paket <span style="color: red;">*</span></label>
                    <select name="package_id" id="package_id" required>
                        <option value="">-- Pilih Paket --</option>
                        @foreach(\App\Models\Package::all() as $package)
                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Catatan Tambahan</label>
                <textarea name="notes" id="notes" placeholder="Catatan khusus untuk acara ini..."></textarea>
            </div>
        </div>

        {{-- Vendor & Jadwal --}}
        <div class="form-section">
            <h3><i class="bi bi-shop"></i> Vendor & Jadwal Kegiatan</h3>
            
            <div id="vendors-container">
                <div class="repeater-item vendor-item">
                    <div class="form-group">
                        <label>Pilih Vendor</label>
                        <select name="vendors[0][vendor_id]" required>
                            <option value="">-- Pilih Vendor --</option>
                            @foreach(\App\Models\Vendor::with('category')->get() as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }} - {{ $vendor->category->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Aktivitas</label>
                        <input type="text" name="vendors[0][activity]" placeholder="Contoh: Setup Dekorasi Pelaminan" required>
                    </div>

                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" name="vendors[0][location]" placeholder="Contoh: Grand Ballroom">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Waktu Mulai</label>
                            <input type="time" name="vendors[0][start_time]" required>
                        </div>
                        <div class="form-group">
                            <label>Waktu Selesai</label>
                            <input type="time" name="vendors[0][end_time]" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="vendors[0][notes]" placeholder="Catatan untuk vendor..."></textarea>
                    </div>
                </div>
            </div>

            <button type="button" class="add-btn" onclick="addVendor()">
                <i class="bi bi-plus-circle"></i> Tambah Vendor
            </button>
        </div>

        {{-- Tim / Member --}}
        <div class="form-section">
            <h3><i class="bi bi-people"></i> Tim Organizer</h3>
            
            <div id="members-container">
                <div class="repeater-item member-item">
                    <div class="form-group">
                        <label>Pilih Member</label>
                        <select name="members[0][member_id]" required>
                            <option value="">-- Pilih Member --</option>
                            @foreach(\App\Models\Member::all() as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->call_sign }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Role / Posisi</label>
                        <input type="text" name="members[0][role]" placeholder="Contoh: Lead Wedding Director" required>
                    </div>

                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="members[0][notes]" placeholder="Catatan untuk member..."></textarea>
                    </div>
                </div>
            </div>

            <button type="button" class="add-btn" onclick="addMember()">
                <i class="bi bi-plus-circle"></i> Tambah Member
            </button>
        </div>

        <div class="form-actions">
            <a href="{{ route('user.rundown') }}" class="wo-btn wo-btn-outline">Batal</a>
            <button type="submit" class="btn-primary">
                <i class="bi bi-check-circle"></i> Simpan Jadwal Acara
            </button>
        </div>
    </form>

</main>

<script>
let vendorIndex = 1;
let memberIndex = 1;

function addVendor() {
    const container = document.getElementById('vendors-container');
    const template = `
        <div class="repeater-item vendor-item">
            <button type="button" class="remove-btn" onclick="this.parentElement.remove()">
                <i class="bi bi-trash"></i> Hapus
            </button>
            
            <div class="form-group">
                <label>Pilih Vendor</label>
                <select name="vendors[${vendorIndex}][vendor_id]" required>
                    <option value="">-- Pilih Vendor --</option>
                    @foreach(\App\Models\Vendor::with('category')->get() as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->name }} - {{ $vendor->category->name ?? '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Aktivitas</label>
                <input type="text" name="vendors[${vendorIndex}][activity]" placeholder="Contoh: Setup Dekorasi Pelaminan" required>
            </div>

            <div class="form-group">
                <label>Lokasi</label>
                <input type="text" name="vendors[${vendorIndex}][location]" placeholder="Contoh: Grand Ballroom">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Waktu Mulai</label>
                    <input type="time" name="vendors[${vendorIndex}][start_time]" required>
                </div>
                <div class="form-group">
                    <label>Waktu Selesai</label>
                    <input type="time" name="vendors[${vendorIndex}][end_time]" required>
                </div>
            </div>

            <div class="form-group">
                <label>Catatan</label>
                <textarea name="vendors[${vendorIndex}][notes]" placeholder="Catatan untuk vendor..."></textarea>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', template);
    vendorIndex++;
}

function addMember() {
    const container = document.getElementById('members-container');
    const template = `
        <div class="repeater-item member-item">
            <button type="button" class="remove-btn" onclick="this.parentElement.remove()">
                <i class="bi bi-trash"></i> Hapus
            </button>
            
            <div class="form-group">
                <label>Pilih Member</label>
                <select name="members[${memberIndex}][member_id]" required>
                    <option value="">-- Pilih Member --</option>
                    @foreach(\App\Models\Member::all() as $member)
                        <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->call_sign }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Role / Posisi</label>
                <input type="text" name="members[${memberIndex}][role]" placeholder="Contoh: Lead Wedding Director" required>
            </div>

            <div class="form-group">
                <label>Catatan</label>
                <textarea name="members[${memberIndex}][notes]" placeholder="Catatan untuk member..."></textarea>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', template);
    memberIndex++;
}
</script>
@endsection
