<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lengkapi Profil - MyDream</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f3ef;
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 30px;
        }

        .profile-container {
            width: 900px;
            max-width: 100%;
            min-height: 520px;

            background: white;
            border-radius: 20px;

            overflow: hidden;

            display: flex;

            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        /* =========================
           LEFT
        ========================= */

        .image-section {
            width: 45%;

            position: relative;

            background-image: url('/images/wedding.jpg');
            background-size: cover;
            background-position: center;
        }

        .image-overlay {
            position: absolute;
            inset: 0;

            background: linear-gradient(
                to top,
                rgba(0, 0, 0, 0.7),
                rgba(0, 0, 0, 0.05)
            );
        }

        .image-content {
            position: absolute;

            bottom: 40px;
            left: 40px;
            right: 40px;

            color: white;
        }

        .brand {
            font-size: 24px;
            font-weight: bold;

            margin-bottom: 12px;
        }

        .brand span {
            color: #d5b46a;
        }

        .image-content h1 {
            font-family: Georgia, serif;

            font-size: 30px;
            line-height: 1.25;

            margin-bottom: 12px;
        }

        .image-content p {
            font-size: 14px;
            line-height: 1.6;

            color: #eeeeee;
        }

        /* =========================
           RIGHT
        ========================= */

        .profile-section {
            width: 55%;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 50px;
        }

        .profile-content {
            width: 100%;
            max-width: 400px;
        }

        .profile-label {
            font-size: 12px;
            letter-spacing: 2px;

            color: #a68b4a;

            margin-bottom: 10px;
        }

        .profile-content h2 {
            font-family: Georgia, serif;

            font-size: 30px;
            color: #243d3d;

            margin-bottom: 10px;
        }

        .description {
            font-size: 14px;
            line-height: 1.6;

            color: #777;

            margin-bottom: 28px;
        }

        /* =========================
           FORM
        ========================= */

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;

            font-size: 13px;
            font-weight: 600;

            color: #444;

            margin-bottom: 7px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;

            border: 1px solid #dddddd;
            border-radius: 9px;

            padding: 12px 14px;

            font-size: 14px;
            font-family: Arial, sans-serif;

            outline: none;

            transition: 0.2s;
        }

        .form-group input {
            height: 45px;
        }

        .form-group textarea {
            height: 85px;

            resize: none;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #486b69;

            box-shadow: 0 0 0 3px rgba(72, 107, 105, 0.08);
        }

        .error {
            color: #d9534f;

            font-size: 12px;

            margin-top: 5px;
        }

        .submit-button {
            width: 100%;
            height: 48px;

            border: none;
            border-radius: 9px;

            background: #486b69;

            color: white;

            font-size: 14px;
            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;
        }

        .submit-button:hover {
            background: #3d5d5b;

            transform: translateY(-1px);
        }

        .security {
            text-align: center;

            margin-top: 20px;

            font-size: 12px;
            color: #999;
        }

        .security span {
            color: #486b69;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 700px) {

            body {
                padding: 15px;
            }

            .profile-container {
                min-height: auto;
            }

            .image-section {
                display: none;
            }

            .profile-section {
                width: 100%;

                padding: 40px 30px;
            }
        }
    </style>
</head>

<body>

    <div class="profile-container">

        <!-- LEFT -->
        <div class="image-section">

            <div class="image-overlay"></div>

            <div class="image-content">

                <div class="brand">
                    My<span>Dream</span>
                </div>

                <h1>
                    Lengkapi Informasi Pribadimu
                </h1>

                <p>
                    Beberapa informasi diperlukan agar
                    MyDream dapat membantu mengelola
                    kebutuhan pernikahanmu.
                </p>

            </div>

        </div>


        <!-- RIGHT -->
        <div class="profile-section">

            <div class="profile-content">

                <div class="profile-label">
                    MYDREAM WEDDING ORGANIZER
                </div>

                <h2>
                    Informasi Pribadi
                </h2>

                <p class="description">
                    Lengkapi data berikut untuk melanjutkan
                    ke dalam sistem MyDream.
                </p>


                <form action="{{ route('client.profile.store') }}" method="POST">

                    @csrf


                    <!-- FULL NAME -->
                    <div class="form-group">

                        <label for="full_name">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            value="{{ old('full_name') }}"
                            placeholder="Masukkan nama lengkap"
                            required
                        >

                        @error('full_name')
                            <div class="error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <!-- PHONE -->
                    <div class="form-group">

                        <label for="phone">
                            Nomor Telepon
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="Contoh: 081234567890"
                            required
                        >

                        @error('phone')
                            <div class="error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <!-- ADDRESS -->
                    <div class="form-group">

                        <label for="address">
                            Alamat
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            placeholder="Masukkan alamat lengkap"
                            required
                        >{{ old('address') }}</textarea>

                        @error('address')
                            <div class="error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <button
                        type="submit"
                        class="submit-button"
                    >
                        Simpan dan Lanjutkan
                    </button>

                </form>


                <div class="security">
                    🔒 <span>Data pribadi Anda tersimpan dengan aman</span>
                </div>

            </div>

        </div>

    </div>

</body>

</html>