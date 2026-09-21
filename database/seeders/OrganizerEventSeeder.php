<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrganizerEvent;
use Carbon\Carbon;

class OrganizerEventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Technical Meeting - Oktober Planning',
                'description' => 'Rapat koordinasi untuk merencanakan acara bulan Oktober',
                'event_date' => Carbon::now()->addDays(2),
                'start_time' => '09:00:00',
                'end_time' => '11:00:00',
                'event_type' => 'technical_meeting',
                'location' => 'Office Meeting Room A',
                'priority' => 'high',
                'attendees' => 'All Team Leaders, Project Managers',
                'status' => 'scheduled',
                'notes' => 'Bring laptop dan dokumen project overview'
            ],
            [
                'title' => 'Vendor Briefing - Catering',
                'description' => 'Briefing dengan vendor catering untuk acara weekend ini',
                'event_date' => Carbon::now()->addDays(1),
                'start_time' => '14:00:00',
                'end_time' => '15:30:00',
                'event_type' => 'briefing',
                'location' => 'Office',
                'priority' => 'high',
                'attendees' => 'Vendor Manager, Catering Vendors',
                'status' => 'scheduled',
                'notes' => 'Konfirmasi menu dan jumlah porsi'
            ],
            [
                'title' => 'Site Visit - Ritz Carlton',
                'description' => 'Survey lokasi untuk persiapan acara bulan depan',
                'event_date' => Carbon::now()->addDays(5),
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'event_type' => 'site_visit',
                'location' => 'Ritz Carlton Mega Kuningan',
                'priority' => 'medium',
                'attendees' => 'Lead Director, Venue Coordinator',
                'status' => 'scheduled',
                'notes' => 'Cek sound system dan lighting'
            ],
            [
                'title' => 'Team Training - Crisis Management',
                'description' => 'Pelatihan manajemen krisis untuk tim lapangan',
                'event_date' => Carbon::now()->addDays(7),
                'start_time' => '13:00:00',
                'end_time' => '17:00:00',
                'event_type' => 'training',
                'location' => 'Training Room B',
                'priority' => 'medium',
                'attendees' => 'Field Team, Coordinators',
                'status' => 'scheduled',
                'notes' => 'Mandatory untuk semua field team'
            ],
            [
                'title' => 'Client Consultation - New Booking',
                'description' => 'Konsultasi dengan calon klien untuk acara Desember',
                'event_date' => Carbon::now()->addDays(3),
                'start_time' => '16:00:00',
                'end_time' => '17:30:00',
                'event_type' => 'consultation',
                'location' => 'Office - Client Lounge',
                'priority' => 'high',
                'attendees' => 'Sales Manager, Wedding Planner',
                'status' => 'scheduled',
                'notes' => 'Siapkan portfolio dan price list'
            ],
            [
                'title' => 'Monthly Review Meeting',
                'description' => 'Review performa tim dan evaluasi acara bulan lalu',
                'event_date' => Carbon::now()->addDays(10),
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'event_type' => 'technical_meeting',
                'location' => 'Main Office Hall',
                'priority' => 'medium',
                'attendees' => 'All Staff',
                'status' => 'scheduled',
                'notes' => 'Bawa laporan masing-masing'
            ],
            [
                'title' => 'Emergency Response Drill',
                'description' => 'Simulasi penanganan darurat saat acara berlangsung',
                'event_date' => Carbon::now()->addDays(14),
                'start_time' => '08:00:00',
                'end_time' => '10:00:00',
                'event_type' => 'training',
                'location' => 'Training Ground',
                'priority' => 'high',
                'attendees' => 'All Field Coordinators',
                'status' => 'scheduled',
                'notes' => 'Wajib hadir untuk sertifikasi'
            ],
        ];

        foreach ($events as $event) {
            OrganizerEvent::create($event);
        }
    }
}
