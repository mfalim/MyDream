<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - MyDream Wedding Organizer</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/auth/login.css'])
</head>

<body>

    <div class="login-container">

        <!-- LEFT HERO (LANDING PAGE STYLE) -->
        <div class="image-section">
            <div class="image-overlay"></div>

            <div class="image-top">
                <div class="hero-pill">
                    <span class="dot"></span>
                    <span>MyDream · Jember &amp; Surabaya</span>
                </div>
            </div>

            <div class="image-content">
                <div class="brand-logo">
                    My<span>Dream</span>
                </div>

                <h1>Pasti Nikahmu <span class="highlight">Beda!</span></h1>

                <p>
                    Bikin hari bahagiamu terasa lebih personal, hangat, dan tanpa beban bersama MyDream Wedding Organizer.
                </p>

                <div class="hero-stats-row">
                    <div class="hero-stat-badge">
                        <i class="bi bi-patch-check-fill"></i>
                        <span>Vendor Terkurasi</span>
                    </div>
                    <div class="hero-stat-badge">
                        <i class="bi bi-heart-fill"></i>
                        <span>Konsep Personal</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT LOGIN -->
        <div class="login-section">
            <a href="{{ url('/') }}" class="back-home-link">
                <i class="bi bi-arrow-left"></i>
                <span>Ke Beranda</span>
            </a>

            <div class="login-content">
                <div class="login-eyebrow">
                    PORTAL KLIEN &amp; KRU
                </div>

                <h2>Selamat Datang</h2>

                <p class="login-description">
                    Masuk dengan akun Google Anda untuk melanjutkan perencanaan dan memantau persiapan pernikahan impian.
                </p>

                <!-- GOOGLE LOGIN -->
                <a href="{{ url('/auth/google') }}" class="google-button">
                    <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#4285F4" d="M21.35 12.27c0-.68-.06-1.35-.17-1.98H12v3.75h5.22a4.47 4.47 0 0 1-1.94 2.93v2.44h3.14c1.84-1.69 2.93-4.18 2.93-7.14z"/>
                        <path fill="#34A853" d="M12 21.75c2.63 0 4.84-.87 6.45-2.34l-3.14-2.44c-.87.58-1.98.92-3.31.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.52A9.75 9.75 0 0 0 12 21.75z"/>
                        <path fill="#FBBC05" d="M6.54 13.86a5.86 5.86 0 0 1 0-3.72V7.62H3.3a9.75 9.75 0 0 0 0 8.76l3.24-2.52z"/>
                        <path fill="#EA4335" d="M12 6.11c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.84 3.22 14.63 2.25 12 2.25a9.75 9.75 0 0 0-8.7 5.37l3.24 2.52C7.31 7.83 9.46 6.11 12 6.11z"/>
                    </svg>
                    <span>Lanjutkan dengan Google</span>
                </a>

                <div class="login-divider">
                    <span>KEAMANAN</span>
                </div>

                <div class="security-badge">
                    <i class="bi bi-shield-check"></i>
                    <span>Login terenkripsi &amp; aman via Google OAuth</span>
                </div>

                <div class="copyright-text">
                    &copy; {{ date('Y') }} MyDream Wedding Organizer. All rights reserved.
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
