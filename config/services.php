<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID') ? trim(env('GOOGLE_CLIENT_ID')) : null,
        'client_secret' => env('GOOGLE_CLIENT_SECRET') ? trim(env('GOOGLE_CLIENT_SECRET')) : null,
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        'android_client_id' => env('ANDROID_CLIENT_ID') ? trim(env('ANDROID_CLIENT_ID')) : null,
    ],
    'auth' => [
        // Opsional: aktifkan OTP setiap login password Admin jika SMTP produksi sudah siap.
        'admin_login_otp' => (bool) env('AUTH_ADMIN_OTP', false),
    ],
    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
        'base_url' => env(
            'MIDTRANS_BASE_URL',
            env('MIDTRANS_IS_PRODUCTION', false)
                ? 'https://app.midtrans.com'
                : 'https://app.sandbox.midtrans.com'
        ),
        'api_url' => env(
            'MIDTRANS_API_URL',
            env('MIDTRANS_IS_PRODUCTION', false)
                ? 'https://api.midtrans.com'
                : 'https://api.sandbox.midtrans.com'
        ),
    ],
    'firebase' => [
        'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase-service-account.json')),
    ],

];
