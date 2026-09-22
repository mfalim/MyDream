@extends('layouts.admin')

@section('title', 'Detail Vendor')
@section('page-title', 'Detail Vendor')

@push('styles')
    @vite('resources/css/admin/vendors/show.css')
@endpush

@section('content')

    @php
        $photos = $vendor->photos;

        $coverPhoto = $photos->firstWhere('is_cover', true)
            ?? $photos->first();

        $whatsappNumber = preg_replace('/\D+/', '', $vendor->phone);

        if (str_starts_with($whatsappNumber, '0')) {
            $whatsappNumber = '62' . substr($whatsappNumber, 1);
        }

        $whatsappMessage = urlencode(
            'Halo ' . $vendor->name . ', saya ingin menanyakan mengenai layanan wedding.'
        );
    @endphp

    <div class="vendor-detail-page">

        {{-- Breadcrumb --}}
        <div class="vendor-breadcrumb">
            <a href="{{ route('admin.vendors.index') }}">
                Kelola Vendor
            </a>
            <span>/</span>
            <span>{{ $vendor->name }}</span>
        </div>


        {{-- Header --}}
        <div class="vendor-detail-header">

            <div>
                <span class="vendor-detail-category">
                    {{ $vendor->category?->name ?? '-' }}
                </span>

                <h2>{{ $vendor->name }}</h2>

                <p>
                    Vendor rekanan wedding yang tersedia dalam sistem.
                </p>
            </div>

            <div class="vendor-detail-header-actions">

                <a href="{{ route('admin.vendors.edit', $vendor) }}"
                    class="btn-edit-vendor">
                    ✏ Edit
                </a>

                <form action="{{ route('admin.vendors.destroy', $vendor) }}"
                    method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus vendor ini?')">

                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn-delete-vendor">
                        🗑 Hapus
                    </button>
                </form>

            </div>

        </div>


        {{-- Main --}}
        <div class="vendor-detail-grid">

            {{-- Gallery --}}
            <div class="vendor-gallery">

                <div class="vendor-main-photo">

                    @if ($coverPhoto)
                        <img id="mainVendorPhoto"
                            src="{{ asset('storage/' . $coverPhoto->photo) }}"
                            alt="{{ $vendor->name }}">
                    @else
                        <div class="vendor-no-main-photo">
                            <span>📷</span>
                            <p>Belum ada foto vendor</p>
                        </div>
                    @endif

                </div>


                @if ($photos->count() > 0)

                    <div class="vendor-thumbnails">

                        @foreach ($photos as $photo)

                            <button type="button"
                                class="vendor-thumbnail {{ $coverPhoto?->id === $photo->id ? 'active' : '' }}"
                                onclick="changeVendorPhoto(
                                    '{{ asset('storage/' . $photo->photo) }}',
                                    this
                                )">

                                <img src="{{ asset('storage/' . $photo->photo) }}"
                                    alt="Foto {{ $vendor->name }}">

                            </button>

                        @endforeach

                    </div>

                @endif

            </div>


            {{-- Information --}}
            <div class="vendor-info-column">

                <div class="vendor-info-card">

                    <div class="vendor-info-card-header">
                        <span>INFORMASI VENDOR</span>
                    </div>


                    <div class="vendor-info-item">
                        <small>Kategori</small>
                        <strong>
                            {{ $vendor->category?->name ?? '-' }}
                        </strong>
                    </div>


                    <div class="vendor-info-item">
                        <small>Harga Vendor</small>
                        <strong>
                            Rp {{ number_format($vendor->price, 0, ',', '.') }}
                        </strong>
                    </div>


                    <div class="vendor-info-item">
                        <small>Nomor Telepon</small>
                        <strong>
                            {{ $vendor->phone }}
                        </strong>
                    </div>


                    <div class="vendor-info-item">
                        <small>Alamat</small>
                        <strong>
                            {{ $vendor->address }}
                        </strong>
                    </div>


                    <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}"
                        target="_blank"
                        class="btn-whatsapp-detail">
                        ☎ Hubungi Vendor via WhatsApp
                    </a>

                </div>


                <div class="vendor-info-card">

                    <div class="vendor-info-card-header">
                        <span>DESKRIPSI VENDOR</span>
                    </div>

                    <div class="vendor-description-detail">

                        @if ($vendor->description)
                            {{ $vendor->description }}
                        @else
                            <span class="text-muted">
                                Belum ada deskripsi vendor.
                            </span>
                        @endif

                    </div>

                </div>


                <div class="vendor-info-card vendor-photo-count">

                    <span>Jumlah Foto Vendor</span>

                    <strong>
                        {{ $photos->count() }} Foto
                    </strong>

                </div>

            </div>

        </div>

    </div>


    <script>
        function changeVendorPhoto(imageUrl, button) {

            document.getElementById('mainVendorPhoto').src = imageUrl;

            document.querySelectorAll('.vendor-thumbnail')
                .forEach(function(item) {
                    item.classList.remove('active');
                });

            button.classList.add('active');
        }
    </script>

@endsection
