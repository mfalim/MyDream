<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\Package;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Member;
use App\Models\Event;
use App\Models\Schedule;
use App\Models\EventMember;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'google_id' => 'admin_123456',
            'avatar' => 'https://ui-avatars.com/api/?name=Admin+User',
            'role' => 'admin',
            'status' => 'approved',
        ]);

        // Create member users
        $memberUser1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'google_id' => 'budi_123456',
            'avatar' => 'https://ui-avatars.com/api/?name=Budi+Santoso',
            'role' => 'member',
            'status' => 'approved',
        ]);

        $memberUser2 = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@example.com',
            'google_id' => 'siti_123456',
            'avatar' => 'https://ui-avatars.com/api/?name=Siti+Rahayu',
            'role' => 'member',
            'status' => 'approved',
        ]);

        $memberUser3 = User::create([
            'name' => 'Ahmad Fadli',
            'email' => 'fadli@example.com',
            'google_id' => 'fadli_123456',
            'avatar' => 'https://ui-avatars.com/api/?name=Ahmad+Fadli',
            'role' => 'member',
            'status' => 'approved',
        ]);

        // Create client user
        $clientUser = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'google_id' => 'john_123456',
            'avatar' => 'https://ui-avatars.com/api/?name=John+Doe',
            'role' => 'client',
            'status' => 'approved',
        ]);

        // Create categories
        $categories = [
            'Catering',
            'Photography',
            'Decoration',
            'Entertainment',
            'Videography',
            'Makeup Artist',
            'Wedding Cake',
            'Sound System',
        ];

        foreach ($categories as $categoryName) {
            Category::create(['name' => $categoryName]);
        }

        // Create vendors (simplified - only 3 vendors)
        $vendorData = [
            ['name' => 'Delicious Catering', 'category_id' => 1, 'phone' => '081234567890', 'address' => 'Jakarta Selatan'],
            ['name' => 'Golden Moments Photography', 'category_id' => 2, 'phone' => '081234567891', 'address' => 'Jakarta Pusat'],
            ['name' => 'Elegant Decor', 'category_id' => 3, 'phone' => '081234567892', 'address' => 'Jakarta Barat'],
        ];

        foreach ($vendorData as $vendor) {
            Vendor::create([
                'user_id' => null,
                'name' => $vendor['name'],
                'category_id' => $vendor['category_id'],
                'price' => 5000000,
                'phone' => $vendor['phone'],
                'address' => $vendor['address'],
                'description' => 'Professional ' . $vendor['name'] . ' services',
            ]);
        }

        // Create packages with vendors (simplified - only 2 packages)
        $packages = [
            [
                'name' => 'Standard Package',
                'vendor_ids' => [1, 2],
            ],
            [
                'name' => 'Premium Package',
                'vendor_ids' => [1, 2, 3],
            ],
        ];

        foreach ($packages as $packageData) {
            $package = Package::create([
                'name' => $packageData['name'],
                'availability_date' => now()->addDays(30),
                'duration' => 8,
                'guest_capacity' => 500,
                'photo' => null,
            ]);

            // Attach vendors to package
            foreach ($packageData['vendor_ids'] as $vendorId) {
                $package->vendors()->attach($vendorId, [
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create Members
        $members = [
            [
                'user_id' => $memberUser1->id,
                'member_code' => 'MBR001',
                'name' => 'Budi Santoso',
                'call_sign' => 'Budi',
                'phone' => '081234567800',
                'email' => 'budi@example.com',
                'domicile' => 'Jakarta',
                'division' => 'Coordinator',
                'position' => 'Lead Coordinator',
                'specialization' => 'Event Management',
                'daily_fee' => 500000,
                'status' => 'active',
            ],
            [
                'user_id' => $memberUser2->id,
                'member_code' => 'MBR002',
                'name' => 'Siti Rahayu',
                'call_sign' => 'Siti',
                'phone' => '081234567801',
                'email' => 'siti@example.com',
                'domicile' => 'Jakarta',
                'division' => 'Documentation',
                'position' => 'Photographer',
                'specialization' => 'Photography',
                'daily_fee' => 400000,
                'status' => 'active',
            ],
            [
                'user_id' => $memberUser3->id,
                'member_code' => 'MBR003',
                'name' => 'Ahmad Fadli',
                'call_sign' => 'Fadli',
                'phone' => '081234567802',
                'email' => 'fadli@example.com',
                'domicile' => 'Jakarta',
                'division' => 'Technical',
                'position' => 'Technical Support',
                'specialization' => 'Sound System',
                'daily_fee' => 350000,
                'status' => 'active',
            ],
        ];

        foreach ($members as $memberData) {
            Member::create($memberData);
        }

        // Create Clients & Bookings & Events
        $client1 = Client::create([
            'users_id' => $clientUser->id,
            'groom_name' => 'John Doe',
            'bride_name' => 'Jane Smith',
            'groom_phone' => '081234567777',
            'bride_phone' => '081234567778',
            'email' => 'johnjane@example.com',
        ]);

        $booking1 = Booking::create([
            'client_id' => $client1->id,
            'package_id' => 2,
            'venue_name' => 'Grand Ballroom Hotel',
            'venue_address' => 'Jl. Sudirman No. 123',
            'venue_city' => 'Jakarta',
            'venue_province' => 'DKI Jakarta',
            'guest_count' => 300,
            'total_price' => 50000000,
            'status' => 'approved',
            'notes' => 'Premium package booking',
        ]);

        $event1 = Event::create([
            'booking_id' => $booking1->id,
            'name' => 'Resepsi Pernikahan',
            'event_type' => 'resepsi',
            'event_date' => now()->addDays(30),
            'guest_count' => 300,
            'start_time' => '18:00:00',
            'end_time' => '22:00:00',
            'package_id' => 2,
            'status' => 'scheduled',
            'notes' => 'Evening reception',
        ]);

        // Add vendors to event via schedules
        Schedule::create([
            'event_id' => $event1->id,
            'vendor_id' => 1,
            'activity' => 'Delicious Catering',
            'location' => null,
            'start_time' => '18:00:00',
            'end_time' => '22:00:00',
            'status' => 'approved',
        ]);

        Schedule::create([
            'event_id' => $event1->id,
            'vendor_id' => 2,
            'activity' => 'Golden Moments Photography',
            'location' => null,
            'start_time' => '18:00:00',
            'end_time' => '22:00:00',
            'status' => 'approved',
        ]);

        Schedule::create([
            'event_id' => $event1->id,
            'vendor_id' => 3,
            'activity' => 'Elegant Decor',
            'location' => null,
            'start_time' => '16:00:00',
            'end_time' => '22:00:00',
            'status' => 'pending',
        ]);

        // Add members to event
        EventMember::create([
            'event_id' => $event1->id,
            'member_id' => 1,
            'role' => 'Koordinator Utama',
            'status' => 'assigned',
        ]);

        EventMember::create([
            'event_id' => $event1->id,
            'member_id' => 2,
            'role' => 'Dokumentasi',
            'status' => 'assigned',
        ]);

        EventMember::create([
            'event_id' => $event1->id,
            'member_id' => 3,
            'role' => 'Technical Support',
            'status' => 'assigned',
        ]);

        echo "\nSeeding completed successfully!\n";
        echo "- 5 users created (1 admin, 3 members, 1 client)\n";
        echo "- " . count($categories) . " categories created\n";
        echo "- " . count($vendorData) . " vendors created\n";
        echo "- " . count($packages) . " packages created with vendors\n";
        echo "- " . count($members) . " members created with user relations\n";
        echo "- 1 client, 1 booking, 1 event created\n";
    }
}
