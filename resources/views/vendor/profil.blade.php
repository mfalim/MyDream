
@extends('vendor.layouts.app')

@section('title', 'Profil Vendor')

@section('content')

<div class="profile-page">

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;margin-bottom:25px;">

        <div>
            <span>VENDOR WORKSPACE</span>
            <h1>Profil Vendor</h1>
            <p>Informasi dan portofolio profil vendor.</p>
        </div>

        @if($vendor)
            <a href="{{ route('vendor.profil.edit') }}"
               style="padding:10px 18px;background:#344b78;color:white;text-decoration:none;border-radius:7px;">
                Edit Profil
            </a>
        @endif

    </div>

    @if($vendor)

        <div class="panel" style="padding:25px;">

            <h2>{{ $vendor->name }}</h2>

            <p>
                Kategori:
                {{ $vendor->category->name ?? '-' }}
            </p>

            <p>
                Nomor Telepon:
                {{ $vendor->phone ?? '-' }}
            </p>

            <p>
                Alamat:
                {{ $vendor->address ?? '-' }}
            </p>

            <h3>Deskripsi Vendor</h3>

            <p>
                {{ $vendor->description ?? 'Belum ada deskripsi.' }}
            </p>

            <h3>Galeri Foto Vendor</h3>

            <div style="display:flex;flex-wrap:wrap;gap:15px;">

                @forelse($vendor->photos as $photo)

                    <img
                        src="{{ asset('storage/' . $photo->photo) }}"
                        alt="Foto Vendor"
                        style="width:180px;height:140px;object-fit:cover;border-radius:8px;"
                    >

                @empty

                    <p>Belum ada foto vendor.</p>

                @endforelse

            </div>

        </div>

    @else

        <div class="panel" style="padding:25px;">
            <h3>Profil Vendor Belum Tersedia</h3>
            <p>
                Data profil vendor belum dimasukkan ke database.
                Silakan lakukan pengisian data setelah seluruh revisi selesai.
            </p>
        </div>

    @endif

</div>

@endsection
