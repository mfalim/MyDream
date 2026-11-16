# MyDream API — Panduan Integrasi Flutter

Dokumen ini menjelaskan API backend MyDream yang tersedia untuk aplikasi mobile Flutter. Gunakan implementasi backend sebagai sumber kebenaran. Jangan mengarang endpoint, field, atau status yang tidak disebutkan di sini; jika ada perubahan API, periksa `routes/api.php` dan controller terkait.

## Petunjuk untuk AI coding assistant di proyek Flutter

- Implementasikan integrasi pada proyek Flutter yang sudah ada dan ikuti struktur serta pola yang sudah digunakan proyek.
- Jangan mengubah backend dari proyek Flutter.
- Gunakan endpoint, metode HTTP, field, dan aturan validasi yang tercantum di dokumen ini.
- Jangan menganggap keranjang otomatis menjadi booking: saat membuat booking, kirim `package_id` atau `vendor_ids` secara eksplisit.
- Simpan token API menggunakan penyimpanan aman seperti Flutter Secure Storage/Keychain/Keystore. Jangan menyimpan token autentikasi di SharedPreferences.
- SharedPreferences boleh dipakai untuk preferensi non-sensitif atau cache keranjang, tetapi data backend tetap menjadi sumber kebenaran.
- Jangan pernah menaruh Google client secret atau Midtrans Server Key di aplikasi Flutter.
- Pertahankan error dari API untuk ditampilkan/ditangani dengan layak; jangan menganggap semua respons sukses.

## Base URL dan format umum

Semua endpoint di bawah memakai awalan `/api`.

```text
Development: http://<alamat-server-development>/api
Production:  https://<domain-api>/api
```

Ganti alamat tersebut dengan URL server yang benar. Emulator Android biasanya tidak dapat mengakses `localhost` komputer host menggunakan `localhost`; gunakan alamat host yang dapat dijangkau emulator/perangkat. Gunakan HTTPS untuk production.

Untuk JSON:

```http
Accept: application/json
Content-Type: application/json
```

Untuk endpoint terlindungi, tambahkan:

```http
Authorization: Bearer <sanctum_token>
```

Endpoint unggah bukti pembayaran menggunakan `multipart/form-data`.

## Autentikasi

### Login Google

```http
POST /auth/google
```

Body:

```json
{
  "id_token": "<Google ID token dari proses login Flutter>"
}
```

Respons berhasil:

```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "user": {},
    "token": "<Sanctum token>",
    "token_type": "Bearer",
    "role": "client",
    "next_step": "complete_profile"
  }
}
```

Simpan `data.token` dengan aman dan gunakan sebagai Bearer token untuk permintaan terlindungi. `next_step` dapat berupa `complete_profile` atau `dashboard`; periksa juga keberadaan profil lewat endpoint profil. Akun `pending` atau `rejected` menerima HTTP 403. Token Google tidak valid atau kegagalan login menerima HTTP 401.

Backend ini menyediakan login Google, bukan endpoint registrasi email/password untuk mobile.

### Pengguna aktif dan logout

```http
GET  /me
POST /logout
```

Keduanya membutuhkan autentikasi. Logout menghapus token Sanctum yang sedang digunakan.

## Katalog publik

Semua endpoint berikut tidak membutuhkan token.

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/home` | Data halaman utama |
| GET | `/about` | Informasi tentang |
| GET | `/store` | Kategori, paket pilihan, dan vendor |
| GET | `/catalog` | Katalog kategori, paket, dan vendor |
| GET | `/catalog/categories` | Daftar kategori |
| GET | `/catalog/packages` | Daftar paket |
| GET | `/catalog/packages/{slug}` | Detail paket |
| GET | `/catalog/vendors/{slug}` | Detail vendor |
| GET | `/content/articles` | Daftar artikel |
| GET | `/content/articles/{slug}` | Detail artikel |
| GET | `/content/events` | Daftar event |
| GET | `/content/events/{slug}` | Detail event |
| GET | `/content/inspirations` | Daftar inspirasi |
| GET | `/content/inspirations/{slug}` | Detail inspirasi |

`/catalog` menerima query opsional:

```text
?q=<kata pencarian>&per_page=15
```

`q` maksimal 100 karakter dan `per_page` harus 1–100. Endpoint daftar paket `/catalog/packages` juga menerima parameter tersebut. Respons daftar dapat berupa pagination Laravel; gunakan `data` dan metadata pagination yang dikembalikan server, jangan mengasumsikan seluruh data datang sekaligus.

## Profil dan area klien

Endpoint berikut membutuhkan token user dengan role `client`.

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/client/dashboard` | Ringkasan dashboard klien |
| GET | `/client/profile` | Membaca profil klien; `data` dapat bernilai `null` bila belum dibuat |
| PUT | `/client/profile` | Membuat atau memperbarui profil |
| POST | `/client/profile` | Alias untuk membuat atau memperbarui profil |
| GET | `/client/bookings` | Daftar booking (pagination) |
| POST | `/client/bookings` | Membuat booking |
| GET | `/client/bookings/{bookingId}/payments` | Detail booking dan ringkasan pembayaran |
| POST | `/client/bookings/{bookingId}/payments` | Mencatat pembayaran transfer bank |
| POST | `/client/bookings/{bookingId}/payments/midtrans` | Meminta token transaksi Midtrans |
| GET | `/client/events` | Daftar event klien |
| POST | `/client/events` | Membuat event untuk booking |

### Simpan profil

`PUT /client/profile` (atau `POST`) membutuhkan:

```json
{
  "groom_name": "Nama mempelai pria",
  "bride_name": "Nama mempelai wanita",
  "groom_phone": "081234567890",
  "bride_phone": "081234567891",
  "email": "contoh@email.com"
}
```

Nomor telepon harus 10–15 digit angka. Profil perlu tersedia sebelum membuat booking.

## Keranjang API

Endpoint cart membutuhkan token Sanctum. Keranjang disimpan di database per user.

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/cart` | Membaca keranjang |
| POST | `/cart/packages/{packageId}` | Memilih paket; menggantikan isi cart sebelumnya |
| POST | `/cart/vendors/{vendorId}` | Menambahkan vendor ke cart custom |
| DELETE | `/cart/vendors/{vendorId}` | Menghapus vendor dari cart |
| DELETE | `/cart` | Mengosongkan cart |

Penambahan paket/vendor tidak memerlukan body JSON. Respons cart berisi `data.cart`, `data.package`, `data.vendors`, dan `data.total_price`. `type` dapat bernilai `package`, `custom`, atau `null`.

Aturan:

- Cart hanya memuat satu pilihan jenis: paket atau vendor custom.
- Memilih paket mengganti vendor custom yang tersimpan.
- Menambahkan vendor ketika paket sedang dipilih menghasilkan HTTP 409. Kosongkan cart atau ubah pilihan terlebih dahulu.
- `total_price` dihitung backend dari harga vendor saat respons dibuat.
- Cart tidak otomatis digunakan ketika booking dibuat; aplikasi perlu mengirim pilihan booking secara eksplisit.

## Booking

`POST /client/bookings` membuat booking beserta event dan jadwal vendor awal. Profil klien harus sudah ada.

Untuk paket:

```json
{
  "package_id": 12,
  "venue_name": "Gedung Contoh",
  "venue_address": "Jalan Contoh No. 1",
  "venue_city": "Jakarta",
  "venue_province": "DKI Jakarta",
  "venue_maps": "https://maps.example/...",
  "guest_count": 200,
  "event_date": "2027-06-20",
  "notes": "Catatan opsional"
}
```

Untuk vendor custom, ganti `package_id` dengan sekurangnya tiga ID vendor:

```json
{
  "vendor_ids": [3, 7, 11],
  "venue_name": "Gedung Contoh",
  "venue_address": "Jalan Contoh No. 1",
  "venue_city": "Jakarta",
  "venue_province": "DKI Jakarta",
  "guest_count": 200,
  "event_date": "2027-06-20"
}
```

Field wajib: `venue_name`, `venue_address`, `venue_city`, `venue_province`, `guest_count`, dan `event_date`. Tanggal harus setelah hari ini. `guest_count` 1–10.000; paket juga membatasi jumlah tamu menurut kapasitasnya. Harga dihitung backend berdasarkan paket/vendor, bukan dipercaya dari harga yang dikirim aplikasi. Respons berhasil HTTP 201 dengan booking di `data`.

## Pembayaran

### Membaca status pembayaran

```http
GET /client/bookings/{bookingId}/payments
```

Hanya pemilik booking yang dapat membacanya. Respons memuat booking pada `data` dan `summary`, termasuk `total`, `paid`, `pending`, `remaining`, dan `payment_status`.

Status pembayaran yang digunakan model mencakup `unpaid`, `awaiting_verification`, `dp_paid`, dan `paid`.

### Midtrans Snap

```http
POST /client/bookings/{bookingId}/payments/midtrans
Content-Type: application/json
```

Body memilih skema pembayaran yang tersedia bagi booking:

```json
{
  "scheme": "dp"
}
```

Skema yang mungkin tersedia: `dp` atau `full` saat belum ada pembayaran; `remaining` setelah DP terbayar. Pilihan aktual bergantung pada status dan sisa tagihan. Jika berhasil, backend mengembalikan:

```json
{
  "token": "<Midtrans Snap token>"
}
```

Gunakan token tersebut dengan integrasi Midtrans Snap yang sesuai untuk Flutter. Jangan memanggil Midtrans dari aplikasi menggunakan Server Key. Konfigurasi Midtrans belum lengkap dapat menghasilkan HTTP 503; kegagalan membuat transaksi dapat menghasilkan HTTP 502.

### Transfer bank manual (opsional)

`POST /client/bookings/{bookingId}/payments` menerima `multipart/form-data`:

```text
scheme=dp
proof=<file opsional: jpg, jpeg, png, atau pdf; maksimum 2 MB>
```

Skema mengikuti status pembayaran (`dp`, `full`, atau `remaining`). Endpoint ini akan membuat catatan pembayaran transfer bank dengan status menunggu verifikasi.

## Event (opsional untuk fitur lanjutan)

`GET /client/events` mengembalikan event klien. Filter bulan opsional memakai `?month=YYYY-MM`.

`POST /client/events` membuat event untuk booking milik klien. Field minimal:

```json
{
  "booking_id": 25,
  "name": "Acara pernikahan",
  "event_type": "wedding",
  "event_date": "2027-06-20",
  "guest_count": 200
}
```

Controller juga menerima field tambahan seperti `package_id`, `notes`, `photo`, `vendors`, dan `members`; periksa validasi aktual sebelum membangun UI untuk field tersebut.

## Error dan penanganannya

Respons validasi Laravel biasanya HTTP 422 dan berisi `message` serta `errors`. Status umum:

| HTTP | Arti umum |
|---|---|
| 401 | Belum login, token tidak valid, atau login Google gagal |
| 403 | Role/akses tidak diizinkan, atau akun tidak dapat digunakan |
| 404 | Resource tidak ditemukan atau bukan milik user |
| 409 | Konflik aturan cart, misalnya menambah vendor saat paket dipilih |
| 422 | Validasi gagal atau data tidak sesuai aturan |
| 502 | Gagal memulai transaksi Midtrans |
| 503 | Konfigurasi layanan pembayaran belum siap |

Tangani respons non-2xx di Flutter. Jangan menganggap bahwa setiap respons memiliki bentuk JSON `data`; beberapa error hanya mengembalikan `message`, sementara error validasi juga memiliki `errors`.

## Batasan dan hal yang perlu diuji

- Domain/base URL, konfigurasi Google OAuth, dan kredensial Midtrans harus disiapkan sesuai lingkungan deployment.
- Login Google harus diuji dari aplikasi Flutter dengan ID token yang benar untuk konfigurasi OAuth aplikasi.
- Uji alur ujung-ke-ujung: login → profil → katalog/cart → booking → pembayaran sandbox → notifikasi Midtrans.
- Endpoint webhook `/api/midtrans/notification` hanya untuk notifikasi server Midtrans, bukan endpoint yang dipanggil langsung oleh aplikasi Flutter.
- URL gambar/resource bergantung pada data yang dikembalikan API dan konfigurasi `APP_URL`/storage server.
- Dokumentasi ini mencerminkan API yang ada saat dibuat; periksa implementasi backend bila respons aktual berbeda.
