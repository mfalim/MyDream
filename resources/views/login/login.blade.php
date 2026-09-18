<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - MyDream</title>

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

        .login-container {
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
           LEFT - IMAGE
        ========================= */

        .image-section {
            width: 50%;
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
                rgba(0, 0, 0, 0.65),
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

            margin-bottom: 10px;
        }

        .brand span {
            color: #d5b46a;
        }

        .image-content h1 {
            font-family: Georgia, serif;
            font-size: 32px;
            line-height: 1.2;

            margin-bottom: 12px;
        }

        .image-content p {
            font-size: 14px;
            line-height: 1.6;

            color: #eeeeee;
        }

        /* =========================
           RIGHT - LOGIN
        ========================= */

        .login-section {
            width: 50%;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 50px;
        }

        .login-content {
            width: 100%;
            max-width: 360px;

            text-align: center;
        }

        .login-label {
            font-size: 12px;
            letter-spacing: 2px;

            color: #a68b4a;

            margin-bottom: 12px;
        }

        .login-content h2 {
            font-family: Georgia, serif;

            font-size: 32px;
            color: #243d3d;

            margin-bottom: 15px;
        }

        .login-description {
            font-size: 14px;
            line-height: 1.6;

            color: #777;

            margin-bottom: 35px;
        }

        /* =========================
           GOOGLE BUTTON
        ========================= */

        .google-button {
            width: 100%;
            height: 52px;

            border: 1px solid #dddddd;
            border-radius: 10px;

            background: white;

            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;

            color: #333;
            text-decoration: none;

            font-size: 14px;
            font-weight: 500;

            transition: 0.2s;
        }

        .google-button:hover {
            background: #f8f8f8;

            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);

            transform: translateY(-1px);
        }

        .google-icon {
            width: 20px;
            height: 20px;
        }

        .security {
            margin-top: 35px;

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

            .login-container {
                min-height: auto;
            }

            .image-section {
                display: none;
            }

            .login-section {
                width: 100%;
                padding: 45px 30px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">

        <!-- LEFT -->
        <div class="image-section">

            <div class="image-overlay"></div>

            <div class="image-content">

                <div class="brand">
                    My<span>Dream</span>
                </div>

                <h1>
                    Rencanakan Hari Bahagiamu
                </h1>

                <p>
                    Wujudkan pernikahan impianmu bersama
                    MyDream Wedding Organizer.
                </p>

            </div>

        </div>


        <!-- RIGHT -->
        <div class="login-section">

            <div class="login-content">

                <div class="login-label">
                    MYDREAM WEDDING ORGANIZER
                </div>

                <h2>
                    Selamat Datang
                </h2>

                <p class="login-description">
                    Masuk untuk melanjutkan dan mengelola
                    kebutuhan pernikahan impianmu.
                </p>


                <!-- GOOGLE LOGIN -->
                <a href="{{ url('/auth/google') }}" class="google-button">

                    <svg
                        class="google-icon"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill="#4285F4"
                            d="M21.35 12.27c0-.68-.06-1.35-.17-1.98H12v3.75h5.22a4.47 4.47 0 0 1-1.94 2.93v2.44h3.14c1.84-1.69 2.93-4.18 2.93-7.14z"
                        />

                        <path
                            fill="#34A853"
                            d="M12 21.75c2.63 0 4.84-.87 6.45-2.34l-3.14-2.44c-.87.58-1.98.92-3.31.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.52A9.75 9.75 0 0 0 12 21.75z"
                        />

                        <path
                            fill="#FBBC05"
                            d="M6.54 13.86a5.86 5.86 0 0 1 0-3.72V7.62H3.3a9.75 9.75 0 0 0 0 8.76l3.24-2.52z"
                        />

                        <path
                            fill="#EA4335"
                            d="M12 6.11c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.84 3.22 14.63 2.25 12 2.25a9.75 9.75 0 0 0-8.7 5.37l3.24 2.52C7.31 7.83 9.46 6.11 12 6.11z"
                        />
                    </svg>

                    <span>
                        Lanjutkan dengan Google
                    </span>

                </a>


                <div class="security">
                    🔒 <span>Login aman menggunakan Google OAuth</span>
                </div>

            </div>

        </div>

    </div>

</body>
</html>