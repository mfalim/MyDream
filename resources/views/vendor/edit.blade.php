@extends('vendor.layouts.app')

@section('title', 'Edit Profil Vendor')

@section('content')

<div style="padding:20px;">

    <h1>Edit Profil Vendor</h1>
    <p>Perbarui informasi profil vendor.</p>

    @if($errors->any())
        <div style="padding:12px;background:#ffe5e5;color:#a00;margin-bottom:15px;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vendor.profil.update') }}"
          method="POST"
          enctype="multipart/form-data"
          class="panel"
          style="padding:25px;">

        @csrf
        @method('PUT')

        <div style="margin-bottom:15px;">
            <label>Nama Vendor</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $vendor->name) }}"
                   required
                   style="display:block;width:100%;padding:10px;">
        </div>

        <div style="margin-bottom:15px;">
            <label>Kategori Vendor</label>
            <select name="category_id"
                    style="display:block;width:100%;padding:10px;">

                <option value="">Pilih Kategori</option>

                @foreach(\App\Models\Category::all() as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $vendor->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach

            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label>Nomor Telepon</label>
            <input type="text"
                   name="phone"
                   value="{{ old('phone', $vendor->phone) }}"
                   style="display:block;width:100%;padding:10px;">
        </div>

        <div style="margin-bottom:15px;">
            <label>Alamat</label>
            <textarea name="address"
                      rows="3"
                      style="display:block;width:100%;padding:10px;">{{ old('address', $vendor->address) }}</textarea>
        </div>

        <div style="margin-bottom:15px;">
            <label>Deskripsi Vendor</label>
            <textarea name="description"
                      rows="5"
                      style="display:block;width:100%;padding:10px;">{{ old('description', $vendor->description) }}</textarea>
        </div>

        <div style="margin-bottom:15px;">
            <label>Foto Sampul</label>
            <input type="file" name="photo" accept="image/*">
        </div>

        <div style="margin-bottom:20px;">
            <label>Foto Galeri</label>
            <input type="file"
                   name="photos[]"
                   accept="image/*"
                   multiple>
        </div>

        <button type="submit"
                style="padding:10px 20px;background:#344b78;color:white;border:0;border-radius:7px;">
            Simpan Perubahan
        </button>

        <a href="{{ route('vendor.profil') }}"
           style="margin-left:10px;">
            Batal
        </a>

    </form>

</div>

@endsection
