<?php

return [
    'name' => 'MyDream Wedding Organizer',
    'whatsapp' => env('MYDREAM_WHATSAPP', '6281233779967'),
    'whatsapp_display' => '081 233 779 967',
    'email' => 'organizermydream@gmail.com',
    'instagram' => '@mydreamorganizer',

    // Kota layanan yang dipakai di filter katalog.
    'cities' => ['Jember', 'Surabaya'],

    // Persentase DP minimum. Dipakai di tahap pembayaran (belum dipasang di tahap ini).
    'dp_percent' => (int) env('MYDREAM_DP_PERCENT', 10),
];
