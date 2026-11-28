@php
    $otpMode = $mode ?? null;
    $u = fn (string $id) => "https://images.unsplash.com/{$id}?w=1200&q=80&auto=format&fit=crop";
    $loginSlides = [
        \App\Support\Media::home('hero-1', $u('photo-1519741497674-611481863552')),
        \App\Support\Media::home('hero-2', $u('photo-1522673607200-164d1b6ce486')),
        \App\Support\Media::home('hero-3', $u('photo-1465495976277-4387d4b0b4c6')),
    ];

    $headerCopy = match ($otpMode) {
        'forgot_request' => [
            'title' => 'Lupa Password',
            'eyebrow' => 'PEMULIHAN AKUN MANUAL',
            'heading' => 'Lupa / Ubah Password',
            'desc' => 'Masukkan alamat email terdaftar pada akun MyDream Anda untuk menerima 6 digit kode verifikasi OTP.',
        ],
        'password_reset' => [
            'title' => 'Atur Ulang Password',
            'eyebrow' => 'ATUR ULANG PASSWORD',
            'heading' => 'Buat Password Baru',
            'desc' => 'Masukkan 6 digit kode OTP yang dikirim ke <strong>' . e($meta['masked_email'] ?? '-') . '</strong> beserta password baru Anda.',
        ],
        'admin_login' => [
            'title' => 'Verifikasi Admin',
            'eyebrow' => 'VERIFIKASI MASUK ADMIN',
            'heading' => 'Masukkan Kode OTP',
            'desc' => 'Kami mengirimkan 6 digit kode verifikasi ke <strong>' . e($meta['masked_email'] ?? '-') . '</strong>. Berlaku 5 menit.',
        ],
        'register' => [
            'title' => 'Verifikasi Kode OTP',
            'eyebrow' => 'VERIFIKASI PENDAFTARAN KLIEN',
            'heading' => 'Masukkan Kode OTP',
            'desc' => 'Kami mengirimkan 6 digit kode verifikasi ke <strong>' . e($meta['masked_email'] ?? '-') . '</strong>. Berlaku 5 menit.',
        ],
        default => [
            'title' => 'Masuk',
            'eyebrow' => 'PORTAL KLIEN & KRU',
            'heading' => 'Selamat Datang',
            'desc' => 'Masuk menggunakan Username/Email &amp; Password atau akun Google Anda untuk melanjutkan perencanaan pernikahan impian.',
        ],
    };

    $activeTab = old('auth_tab', 'login');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $headerCopy['title'] }} - MyDream Wedding Organizer</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/auth/login.css'])
</head>
<body>
    <div class="login-container">
        <!-- LEFT HERO (SHARED ACROSS LOGIN, REGISTER & OTP) -->
        <div class="image-section" data-hero>
            <div class="login-hero-slides" aria-hidden="true">
                @foreach ($loginSlides as $i => $slideUrl)
                    <img src="{{ $slideUrl }}" alt="" class="login-hero-slide" style="animation-delay: {{ $i * 6 }}s">
                @endforeach
            </div>
            <div class="image-overlay"></div>
            <div class="hero-glow" aria-hidden="true"></div>

            <div class="image-top">
                <div class="hero-pill">
                    <span class="dot"></span>
                    <span>MyDream · Jember &amp; Surabaya</span>
                </div>
            </div>

            <div class="image-content">
                <div class="brand-logo">My<span>Dream</span></div>

                @if ($otpMode)
                    <h1>Aman &amp; <span class="highlight">Terjaga</span></h1>
                    <p>Verifikasi satu kali pakai memastikan akun dan rencana pernikahan impianmu selalu terlindungi bersama MyDream.</p>
                @else
                    <h1>Pasti Nikahmu <span class="highlight">Beda!</span></h1>
                    <p>Bikin hari bahagiamu terasa lebih personal, hangat, dan tanpa beban bersama MyDream Wedding Organizer.</p>
                @endif

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

        <!-- RIGHT PANEL -->
        <div class="login-section">
            <a href="{{ $otpMode ? route('login') : url('/') }}" class="back-home-link">
                <i class="bi bi-arrow-left"></i>
                <span>{{ $otpMode ? 'Kembali ke Masuk' : 'Ke Beranda' }}</span>
            </a>

            <div class="login-content">
                <div class="login-eyebrow">{{ $headerCopy['eyebrow'] }}</div>
                <h2>{{ $headerCopy['heading'] }}</h2>
                <p class="login-description">{!! $headerCopy['desc'] !!}</p>

                @if (! empty($meta['dev_code']))
                    <div class="auth-alert auth-alert-dev">
                        <i class="bi bi-terminal-fill"></i>
                        <span>Mode Lokal (<code>MAIL_MAILER=log</code>): Kode OTP uji: <strong>{{ $meta['dev_code'] }}</strong></span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="auth-alert auth-alert-error">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('success'))
                    <div class="auth-alert auth-alert-success">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="auth-alert auth-alert-error">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @if ($otpMode === 'forgot_request')
                    <div class="auth-panel active">
                        <form action="{{ route('password.otp.send') }}" method="POST" class="auth-form">
                            @csrf
                            <div class="auth-field">
                                <label for="reset_email">Alamat Email Terdaftar <span class="req">*</span></label>
                                <div class="auth-input-wrap">
                                    <i class="bi bi-envelope"></i>
                                    <input type="email" id="reset_email" name="email" value="{{ old('email') }}" placeholder="Ketik email akun Anda" autocomplete="email" required autofocus>
                                </div>
                            </div>

                            <button type="submit" class="auth-submit-btn">
                                <span>Kirim Kode OTP</span>
                                <i class="bi bi-send"></i>
                            </button>
                        </form>
                    </div>
                @elseif ($otpMode)
                    <div class="auth-panel active">
                        <form action="{{ $otpMode === 'password_reset' ? route('password.otp.update') : route('auth.otp.verify') }}" method="POST" class="auth-form">
                            @csrf
                            <div class="auth-field">
                                <label for="otp_code">Kode OTP 6 Digit <span class="req">*</span></label>
                                <div class="auth-input-wrap">
                                    <i class="bi bi-shield-lock"></i>
                                    <input type="text" id="otp_code" name="code" class="otp-code-input" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="000000" autocomplete="one-time-code" required autofocus>
                                </div>
                            </div>

                            @if ($otpMode === 'password_reset')
                                <div class="auth-field-row">
                                    <div class="auth-field">
                                        <label for="new_password">Password Baru <span class="req">*</span></label>
                                        <div class="auth-input-wrap">
                                            <i class="bi bi-lock"></i>
                                            <input type="password" id="new_password" name="password" placeholder="Min. 6 karakter" autocomplete="new-password" required>
                                            <button type="button" class="toggle-password-btn" data-target="new_password" aria-label="Tampilkan Password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="auth-field">
                                        <label for="new_password_confirmation">Ulangi Password <span class="req">*</span></label>
                                        <div class="auth-input-wrap">
                                            <i class="bi bi-shield-check"></i>
                                            <input type="password" id="new_password_confirmation" name="password_confirmation" placeholder="Ketik ulang" autocomplete="new-password" required>
                                            <button type="button" class="toggle-password-btn" data-target="new_password_confirmation" aria-label="Tampilkan Password">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <button type="submit" class="auth-submit-btn">
                                <span>
                                    {{ match ($otpMode) {
                                        'password_reset' => 'Simpan Password Baru',
                                        'register' => 'Verifikasi & Buat Akun',
                                        default => 'Verifikasi & Masuk',
                                    } }}
                                </span>
                                <i class="bi bi-check2-circle"></i>
                            </button>
                        </form>

                        <form action="{{ route('auth.otp.resend') }}" method="POST" class="otp-resend-form">
                            @csrf
                            <span>Belum menerima email? </span>
                            <button type="submit" id="resendBtn" class="otp-resend-btn" data-wait="{{ (int) ($meta['resend_wait'] ?? 0) }}">
                                Kirim Ulang Kode
                            </button>
                        </form>
                    </div>
                @else
                    <!-- AUTH TAB SWITCHER -->
                    <div class="auth-tabs" role="tablist">
                        <button type="button" class="auth-tab-btn {{ $activeTab === 'login' ? 'active' : '' }}" data-tab="login">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Masuk Akun</span>
                        </button>
                        <button type="button" class="auth-tab-btn {{ $activeTab === 'register' ? 'active' : '' }}" data-tab="register">
                            <i class="bi bi-person-plus"></i>
                            <span>Daftar Klien</span>
                        </button>
                    </div>

                    <!-- TAB 1: LOGIN FORM -->
                    <div id="auth-panel-login" class="auth-panel {{ $activeTab === 'login' ? 'active' : '' }}">
                        <form action="{{ route('login.attempt') }}" method="POST" class="auth-form">
                            @csrf
                            <input type="hidden" name="auth_tab" value="login">
                            <input type="hidden" name="redirect" value="{{ session('url.intended') }}">

                            <div class="auth-field">
                                <label for="login_identifier">Username atau Email</label>
                                <div class="auth-input-wrap">
                                    <i class="bi bi-person"></i>
                                    <input type="text" id="login_identifier" name="login" value="{{ old('login') }}" placeholder="Ketik username atau email Anda" autocomplete="username" required>
                                </div>
                            </div>

                            <div class="auth-field">
                                <div class="auth-label-row">
                                    <label for="login_password">Password</label>
                                    <a href="{{ route('password.request') }}" class="forgot-password-link">Lupa / Ubah Password?</a>
                                </div>
                                <div class="auth-input-wrap">
                                    <i class="bi bi-lock"></i>
                                    <input type="password" id="login_password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
                                    <button type="button" class="toggle-password-btn" data-target="login_password" aria-label="Tampilkan Password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="auth-submit-btn">
                                <span>Masuk Sekarang</span>
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </form>

                        <div class="login-divider"><span>ATAU MASUK DENGAN</span></div>

                        <a href="{{ route('auth.google.redirect', ['redirect' => session('url.intended')]) }}" class="google-button" data-no-transition>
                            <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#4285F4" d="M21.35 12.27c0-.68-.06-1.35-.17-1.98H12v3.75h5.22a4.47 4.47 0 0 1-1.94 2.93v2.44h3.14c1.84-1.69 2.93-4.18 2.93-7.14z"/>
                                <path fill="#34A853" d="M12 21.75c2.63 0 4.84-.87 6.45-2.34l-3.14-2.44c-.87.58-1.98.92-3.31.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.52A9.75 9.75 0 0 0 12 21.75z"/>
                                <path fill="#FBBC05" d="M6.54 13.86a5.86 5.86 0 0 1 0-3.72V7.62H3.3a9.75 9.75 0 0 0 0 8.76l3.24-2.52z"/>
                                <path fill="#EA4335" d="M12 6.11c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.84 3.22 14.63 2.25 12 2.25a9.75 9.75 0 0 0-8.7 5.37l3.24 2.52C7.31 7.83 9.46 6.11 12 6.11z"/>
                            </svg>
                            <span>Lanjutkan dengan Google</span>
                        </a>
                    </div>

                    <!-- TAB 2: REGISTER FORM (CLIENT) -->
                    <div id="auth-panel-register" class="auth-panel {{ $activeTab === 'register' ? 'active' : '' }}">
                        <a href="{{ route('auth.google.redirect', ['redirect' => session('url.intended')]) }}" class="google-button" data-no-transition>
                            <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#4285F4" d="M21.35 12.27c0-.68-.06-1.35-.17-1.98H12v3.75h5.22a4.47 4.47 0 0 1-1.94 2.93v2.44h3.14c1.84-1.69 2.93-4.18 2.93-7.14z"/>
                                <path fill="#34A853" d="M12 21.75c2.63 0 4.84-.87 6.45-2.34l-3.14-2.44c-.87.58-1.98.92-3.31.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.52A9.75 9.75 0 0 0 12 21.75z"/>
                                <path fill="#FBBC05" d="M6.54 13.86a5.86 5.86 0 0 1 0-3.72V7.62H3.3a9.75 9.75 0 0 0 0 8.76l3.24-2.52z"/>
                                <path fill="#EA4335" d="M12 6.11c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.84 3.22 14.63 2.25 12 2.25a9.75 9.75 0 0 0-8.7 5.37l3.24 2.52C7.31 7.83 9.46 6.11 12 6.11z"/>
                            </svg>
                            <span>Daftar Instan dengan Google</span>
                        </a>

                        <div class="login-divider"><span>ATAU DAFTAR MANUAL</span></div>

                        <form action="{{ route('register.attempt') }}" method="POST" class="auth-form">
                            @csrf
                            <input type="hidden" name="auth_tab" value="register">
                            <input type="hidden" name="redirect" value="{{ session('url.intended') }}">

                            <div class="auth-field">
                                <label for="reg_name">Nama Panggilan / Lengkap <span class="req">*</span></label>
                                <div class="auth-input-wrap">
                                    <i class="bi bi-person-badge"></i>
                                    <input type="text" id="reg_name" name="name" value="{{ old('name') }}" placeholder="Contoh: Surya & Putri" required>
                                </div>
                            </div>

                            <div class="auth-field-row">
                                <div class="auth-field">
                                    <label for="reg_username">Username <span class="req">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="bi bi-at"></i>
                                        <input type="text" id="reg_username" name="username" value="{{ old('username') }}" placeholder="suryaputri" autocomplete="username" required>
                                    </div>
                                </div>

                                <div class="auth-field">
                                    <label for="reg_email">Email Aktif <span class="req">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="bi bi-envelope"></i>
                                        <input type="email" id="reg_email" name="email" value="{{ old('email') }}" placeholder="nama@email.com (untuk OTP)" autocomplete="email" required>
                                    </div>
                                </div>
                            </div>

                            <div class="auth-field-row">
                                <div class="auth-field">
                                    <label for="reg_password">Password <span class="req">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="bi bi-lock"></i>
                                        <input type="password" id="reg_password" name="password" placeholder="Min. 6 karakter" autocomplete="new-password" required>
                                    </div>
                                </div>

                                <div class="auth-field">
                                    <label for="reg_password_confirmation">Ulangi Password <span class="req">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="bi bi-shield-check"></i>
                                        <input type="password" id="reg_password_confirmation" name="password_confirmation" placeholder="Ketik ulang" autocomplete="new-password" required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="auth-submit-btn">
                                <span>Buat Akun &amp; Lanjutkan</span>
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                @endif

                <div class="copyright-text">
                    &copy; {{ date('Y') }} MyDream Wedding Organizer. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/site.js') }}" defer></script>
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

            const tabButtons = document.querySelectorAll('.auth-tab-btn');
            const loginPanel = document.getElementById('auth-panel-login');
            const registerPanel = document.getElementById('auth-panel-register');

            tabButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const isReg = btn.getAttribute('data-tab') === 'register';
                    tabButtons.forEach(function (b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                    loginPanel?.classList.toggle('active', !isReg);
                    registerPanel?.classList.toggle('active', isReg);
                });
            });

            document.querySelectorAll('.toggle-password-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const input = document.getElementById(btn.getAttribute('data-target'));
                    const icon = btn.querySelector('i');
                    if (!input || !icon) return;
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    icon.classList.replace(show ? 'bi-eye' : 'bi-eye-slash', show ? 'bi-eye-slash' : 'bi-eye');
                });
            });

            const otpInput = document.getElementById('otp_code');
            otpInput?.addEventListener('input', function () {
                otpInput.value = otpInput.value.replace(/\D+/g, '').slice(0, 6);
            });

            const resendBtn = document.getElementById('resendBtn');
            let wait = parseInt(resendBtn?.dataset.wait || '0', 10);
            if (resendBtn && wait > 0) {
                const tick = function () {
                    if (wait <= 0) {
                        resendBtn.disabled = false;
                        resendBtn.textContent = 'Kirim Ulang Kode';
                        return;
                    }
                    resendBtn.disabled = true;
                    resendBtn.textContent = 'Kirim Ulang (' + wait + 'd)';
                    wait -= 1;
                    setTimeout(tick, 1000);
                };
                tick();
            }
        });
    </script>
</body>
</html>
