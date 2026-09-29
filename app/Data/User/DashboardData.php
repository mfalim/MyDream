<?php

namespace App\Data\User;

class DashboardData
{
    public static function get(): array
    {
        return [
            'couple' => [
                'names' => 'Aditya & Sarah',
                'package_name' => 'The Royal Emerald Wedding',
                'event_date_label' => '25 Oktober 2025',
                'venue' => 'Grand Ballroom Hotel Mulia Senayan',
            ],

            'countdown' => [
                'days' => 24,
                'hours' => 14,
                'minutes' => 32,
            ],

            'vendorReadiness' => [
                'total' => 12,
                'confirmed' => 9,
                'waiting' => 2,
                'review' => 1,
                'percent' => 75,
            ],

            'budget' => [
                'total' => 107625000,
                'paid' => 75337500,
                'paid_percent' => 70,
                'remaining' => 32287500,
            ],

            'checklist' => [
                'total' => 34,
                'done' => 28,
                'items' => [
                    [
                        'icon' => 'scissors',
                        'title' => 'Fitting Terakhir Busana Resepsi',
                        'when' => '18 Okt 2025',
                        'where' => 'Anne Avantie Atelier Jakarta',
                    ],
                    [
                        'icon' => 'people',
                        'title' => 'Technical Meeting Keluarga Inti',
                        'when' => '20 Okt 2025',
                        'where' => 'Hotel Mulia VIP Lounge',
                    ],
                ],
            ],

            'milestones' => [
                [
                    'state' => 'done',
                    'icon' => 'check',
                    'status_label' => 'Selesai',
                    'date' => '02 Okt 2025',
                    'title' => 'Food Tasting & Final Banquet Selection',
                    'desc' => 'Menu Western & Nusantara set 800 porsi approved.',
                    'tag' => 'Mulia Catering',
                ],
                [
                    'state' => 'active',
                    'icon' => 'clock',
                    'status_label' => 'Segera Datang',
                    'date' => '18 Okt 2025 (14:00 WIB)',
                    'title' => 'Fitting Final Busana Adat Solo Putri',
                    'desc' => 'Pemeriksaan detail kain prada, aksesori cunduk mentul & beskap pengantin pria.',
                    'action' => 'Konfirmasi Hadir',
                ],
                [
                    'state' => 'pending',
                    'icon' => 'geo-alt',
                    'status_label' => 'Agenda Resmi',
                    'date' => '20 Okt 2025 (10:00 WIB)',
                    'title' => 'Technical Meeting 12 Vendor di Grand Ballroom',
                    'desc' => 'Penataan panggung 18 meter, alur VIP, sound system 20.000 watt & simulasi lighting.',
                    'tag' => 'All Vendors',
                ],
                [
                    'state' => 'pending',
                    'icon' => 'stars',
                    'status_label' => 'Hari H Pernikahan',
                    'date' => '25 Okt 2025',
                    'title' => 'Akad Nikah & Resepsi Agung Aditya & Sarah',
                    'desc' => 'Akad Pagi (08:00) dilanjutkan Resepsi Malam (19:00 - 22:00 WIB).',
                    'tag' => 'Full Team WO (24 Kru)',
                ],
            ],

            'moodboard' => [
                [
                    'image' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=700&q=80',
                    'title' => 'Pelaminan Utama Adat Solo',
                    'subtitle' => 'Konsep Modern Royal Java',
                ],
                [
                    'image' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=700&q=80',
                    'title' => 'VIP Table Styling',
                    'subtitle' => 'Champagne & Emerald Botanicals',
                ],
                [
                    'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=700&q=80',
                    'title' => 'Attire Harmony',
                    'subtitle' => 'Beludru Hitam Sulam Emas',
                ],
            ],

            'team' => [
                [
                    'initials' => 'SN',
                    'name' => 'Sarah Nadia, S.Sn.',
                    'role' => 'Lead Wedding Director',
                    'note' => 'Pengalaman 140+ Royal Weddings',
                ],
                [
                    'initials' => 'DP',
                    'name' => 'Dimas Prasetyo',
                    'role' => 'Show & Stage Director',
                    'note' => 'Handling Rundown & Lighting',
                ],
                [
                    'initials' => 'CP',
                    'name' => 'Clarissa Putri',
                    'role' => 'Personal Bridal Liaison',
                    'note' => 'Pendamping Pengantin Wanita',
                ],
            ],

            'documents' => [
                [
                    'title' => 'SPK Kontrak Kerja WO PROJECT',
                    'note' => 'Tercatat Notaris • PDF (2.4 MB)',
                ],
                [
                    'title' => 'Surat Izin Loading & Sound',
                    'note' => 'Disetujui Hotel Mulia • PDF',
                ],
                [
                    'title' => 'Master Rundown Hari H v4.2',
                    'note' => 'Update 12 Okt 2025 • XLSX',
                ],
            ],
        ];
    }
}
