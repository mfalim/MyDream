<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil - MyDream Wedding Organizer</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/auth/profile.css'])
</head>

<body>

    <div class="profile-container">

        <!-- LEFT HERO (LANDING PAGE STYLE) -->
        <div class="image-section">
            <div class="image-overlay"></div>

            <div class="image-top">
                <div class="hero-pill">
                    <span class="dot"></span>
                    <span>Registrasi Mempelai</span>
                </div>
            </div>

            <div class="image-content">
                <div class="brand-logo">
                    My<span>Dream</span>
                </div>

                <h1>Wujudkan Hari <span class="highlight">Bahagiamu</span></h1>

                <p>
                    Data calon mempelai dibutuhkan untuk mempermudah tim kami menyusun konsep, jadwal, serta koordinasi vendor pernikahan impianmu.
                </p>

                <div class="step-indicator">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Langkah 1 dari 1: Informasi Kontak Mempelai</span>
                </div>
            </div>
        </div>

        <!-- RIGHT FORM -->
        <div class="profile-section">
            <div class="profile-content">
                <div class="profile-eyebrow">
                    MYDREAM WEDDING ORGANIZER
                </div>

                <h2>Data Calon Mempelai</h2>

                <p class="description">
                    Silakan isi data kedua calon mempelai dengan benar untuk sinkronisasi akun.
                </p>

                <form action="{{ route('client.profile.store') }}" method="POST">
                    @csrf

                    <div class="form-grid">
                        <!-- GROOM NAME -->
                        <div class="form-group">
                            <label for="groom_name">
                                <span>Nama Calon Pengantin Pria</span>
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="bi bi-person"></i>
                                <input
                                    type="text"
                                    id="groom_name"
                                    name="groom_name"
                                    value="{{ old('groom_name') }}"
                                    placeholder="Contoh: Raden Surya"
                                    required
                                >
                            </div>
                            @error('groom_name')
                                <div class="error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <!-- BRIDE NAME -->
                        <div class="form-group">
                            <label for="bride_name">
                                <span>Nama Calon Pengantin Wanita</span>
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="bi bi-person-heart"></i>
                                <input
                                    type="text"
                                    id="bride_name"
                                    name="bride_name"
                                    value="{{ old('bride_name') }}"
                                    placeholder="Contoh: Putri Anindya"
                                    required
                                >
                            </div>
                            @error('bride_name')
                                <div class="error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <!-- GROOM PHONE -->
                        <div class="form-group">
                            <label for="groom_phone">
                                <span>WhatsApp / No. HP Pria</span>
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="bi bi-telephone"></i>
                                <input
                                    type="text"
                                    id="groom_phone"
                                    name="groom_phone"
                                    value="{{ old('groom_phone') }}"
                                    placeholder="081234567890"
                                    pattern="[0-9]{10,15}"
                                    required
                                >
                            </div>
                            @error('groom_phone')
                                <div class="error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <!-- BRIDE PHONE -->
                        <div class="form-group">
                            <label for="bride_phone">
                                <span>WhatsApp / No. HP Wanita</span>
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="bi bi-telephone-plus"></i>
                                <input
                                    type="text"
                                    id="bride_phone"
                                    name="bride_phone"
                                    value="{{ old('bride_phone') }}"
                                    placeholder="081234567890"
                                    pattern="[0-9]{10,15}"
                                    required
                                >
                            </div>
                            @error('bride_phone')
                                <div class="error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <!-- EMAIL (FULL WIDTH) -->
                        <div class="form-group full-width">
                            <label for="email">
                                <span>Alamat Email Utama</span>
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="bi bi-envelope"></i>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email', auth()->user()?->email ?? '') }}"
                                    placeholder="contoh@email.com"
                                    required
                                >
                            </div>
                            @error('email')
                                <div class="error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="submit-button">
                        <span>Simpan dan Lanjutkan</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <div class="security">
                    <i class="bi bi-lock-fill"></i>
                    <span>Data Anda tersimpan aman sesuai kebijakan privasi MyDream</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Snowfall & Golden Shimmer Effect Script -->
    <script src="{{ asset('js/wedding-snowfall.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof initWeddingSnowfall === 'function') {
                initWeddingSnowfall({
                    particleCount: window.innerWidth < 768 ? 35 : 65,
                    goldRatio: 0.35,
                    speed: 0.65,
                    wind: 0.2,
                    twinkle: true,
                    zIndex: 99
                });
            }
        });
    </script>
</body>
</html>
