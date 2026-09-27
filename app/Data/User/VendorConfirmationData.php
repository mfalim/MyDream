<?php

namespace App\Data\User;

class VendorConfirmationData
{
    public static function all(): array
    {
        return [
            [
                'category' => 'Dekorasi & Floral',
                'name' => 'Lotus Design Atelier',
                'note' => 'Dekorasi & floral premium',
                'pic' => 'SN',
                'pic_role' => 'PIC Dekorasi',
                'channel' => 'WhatsApp',
                'schedule' => ['18 Okt 2025'],
                'status' => 'confirmed',
                'status_note' => 'Vendor telah dikonfirmasi',
            ],
            [
                'category' => 'Dokumentasi Visual',
                'name' => 'The Leonardi Photography',
                'note' => 'Photography & cinematography',
                'pic' => 'DP',
                'pic_role' => 'PIC Dokumentasi',
                'channel' => 'WhatsApp',
                'schedule' => ['25 Okt 2025'],
                'status' => 'confirmed',
                'status_note' => 'Vendor telah dikonfirmasi',
            ],
            [
                'category' => 'Katering & Jamuan VIP',
                'name' => 'Puspa Catering VIP',
                'note' => 'Premium catering',
                'pic' => 'SN',
                'pic_role' => 'PIC Catering',
                'channel' => 'WhatsApp',
                'schedule' => ['25 Okt 2025'],
                'status' => 'confirmed',
                'status_note' => 'Vendor telah dikonfirmasi',
            ],
            [
                'category' => 'Tata Rias Pengantin',
                'name' => 'Bennu Sorumba MUA & Team',
                'note' => 'Makeup & hairdo pengantin',
                'pic' => 'CP',
                'pic_role' => 'Bridal Liaison',
                'channel' => 'WhatsApp',
                'schedule' => ['25 Okt 2025'],
                'status' => 'pending',
                'status_note' => 'Menunggu konfirmasi jadwal',
            ],
            [
                'category' => 'Busana & Kebaya Adat',
                'name' => 'Griya Busana Suryo Handayani',
                'note' => 'Busana adat & resepsi',
                'pic' => 'DP',
                'pic_role' => 'PIC Busana',
                'channel' => 'WhatsApp',
                'schedule' => ['18 Okt 2025'],
                'status' => 'confirmed',
                'status_note' => 'Vendor telah dikonfirmasi',
            ],
            [
                'category' => 'Master of Ceremony & Host',
                'name' => 'MC Choky Sitohang',
                'note' => 'MC & wedding host',
                'pic' => 'SN',
                'pic_role' => 'Lead Wedding Director',
                'channel' => 'WhatsApp',
                'schedule' => ['25 Okt 2025'],
                'status' => 'rejected',
                'status_note' => 'Vendor tidak tersedia pada tanggal acara',
            ],
            [
                'category' => 'Musik & Entertaining Orchestra',
                'name' => 'Dwiki Jazz Bigband & Chamber',
                'note' => 'Live entertainment & orchestra',
                'pic' => 'DP',
                'pic_role' => 'Show Director',
                'channel' => 'WhatsApp',
                'schedule' => ['25 Okt 2025'],
                'status' => 'confirmed',
                'status_note' => 'Vendor telah dikonfirmasi',
            ],
            [
                'category' => 'Kue Pernikahan Megah',
                'name' => 'Le Novelle Cake Atelier',
                'note' => 'Wedding cake premium',
                'pic' => 'CP',
                'pic_role' => 'Bridal Liaison',
                'channel' => 'WhatsApp',
                'schedule' => ['25 Okt 2025'],
                'status' => 'confirmed',
                'status_note' => 'Vendor telah dikonfirmasi',
            ],
            [
                'category' => 'Tata Cahaya & Efek Visual',
                'name' => 'Sound & Light Dynamics',
                'note' => 'Lighting & visual effects',
                'pic' => 'DP',
                'pic_role' => 'Show Director',
                'channel' => 'WhatsApp',
                'schedule' => ['25 Okt 2025'],
                'status' => 'confirmed',
                'status_note' => 'Vendor telah dikonfirmasi',
            ],
            [
                'category' => 'Cinderamata Tamu VIP',
                'name' => 'Red Ribbon Souvenir Atelier',
                'note' => 'Souvenir & VIP gift',
                'pic' => 'CP',
                'pic_role' => 'Bridal Liaison',
                'channel' => 'WhatsApp',
                'schedule' => ['20 Okt 2025'],
                'status' => 'pending',
                'status_note' => 'Menunggu final quantity',
            ],
        ];
    }

    public static function stats(): array
    {
        $vendors = self::all();

        return [
            'total' => count($vendors),

            'confirmed' => count(
                array_filter($vendors, fn($vendor) => $vendor['status'] === 'confirmed'),
            ),

            'pending' => count(
                array_filter($vendors, fn($vendor) => $vendor['status'] === 'pending'),
            ),

            'rejected' => count(
                array_filter($vendors, fn($vendor) => $vendor['status'] === 'rejected'),
            ),
        ];
    }
}
