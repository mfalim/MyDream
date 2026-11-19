<?php

return [
    'name' => 'MyDream Wedding Organizer',
    'whatsapp' => env('MYDREAM_WHATSAPP', '6281233779967'),
    'whatsapp_display' => '081 233 779 967',
    'email' => 'organizermydream@gmail.com',
    'instagram' => '@mydreamorganizer',

    // Kota layanan yang dipakai di filter katalog.
    'cities' => ['Jember', 'Surabaya'],

    // Persentase DP minimum (ubah lewat .env: MYDREAM_DP_PERCENT=30).
    'dp_percent' => (int) env('MYDREAM_DP_PERCENT', 10),

    // Batas waktu pembayaran DP awal setelah booking dibuat (dalam jam).
    'dp_deadline_hours' => (int) env('MYDREAM_DP_DEADLINE_HOURS', 24),

    // Batas waktu pelunasan sisa tagihan sebelum tanggal acara (H-N hari).
    'payment_deadline_days' => (int) env('MYDREAM_PAYMENT_DEADLINE_DAYS', 14),

    // Rekening tujuan transfer. Diisi lewat .env, tidak ditulis permanen di kode/view.
    'bank' => [
        'name' => env('MYDREAM_BANK_NAME', 'BCA'),
        'number' => env('MYDREAM_BANK_NUMBER', '1234567890'),
        'holder' => env('MYDREAM_BANK_HOLDER', 'MyDream Wedding Organizer'),
    ],
];
