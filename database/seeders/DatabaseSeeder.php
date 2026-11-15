<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\Package;
use App\Models\Client;
use App\Models\Booking;
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

        // Create 15 member users and their corresponding member profiles.
        $this->call(MemberSeeder::class);

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

        // Add vendors to event via schedules with member assignments
        Schedule::create([
            'event_id' => $event1->id,
            'vendor_id' => 1,
            'member_id' => 1,
            'activity' => 'Delicious Catering',
            'location' => 'Grand Ballroom Hotel',
            'start_time' => now()->addDays(30)->setTime(18, 0),
            'end_time' => now()->addDays(30)->setTime(22, 0),
            'status' => 'pending',
            'notes' => 'Koordinasi setup catering dan menu tamu',
        ]);

        Schedule::create([
            'event_id' => $event1->id,
            'vendor_id' => 2,
            'member_id' => 2,
            'activity' => 'Golden Moments Photography',
            'location' => 'Grand Ballroom Hotel',
            'start_time' => now()->addDays(30)->setTime(17, 0),
            'end_time' => now()->addDays(30)->setTime(23, 0),
            'status' => 'pending',
            'notes' => 'Koordinasi setup foto dan videografi',
        ]);

        Schedule::create([
            'event_id' => $event1->id,
            'vendor_id' => 3,
            'member_id' => 3,
            'activity' => 'Elegant Decor',
            'location' => 'Grand Ballroom Hotel',
            'start_time' => now()->addDays(30)->setTime(14, 0),
            'end_time' => now()->addDays(30)->setTime(22, 0),
            'status' => 'pending',
            'notes' => 'Setup dekorasi dan lighting stage',
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
        echo "- 17 users created (1 admin, 15 members, 1 client)\n";
        echo "- " . count($categories) . " categories created\n";
        echo "- " . count($vendorData) . " vendors created\n";
        echo "- " . count($packages) . " packages created with vendors\n";
        echo "- 15 members created with user relations\n";
        echo "- 1 client, 1 booking, 1 event created\n";
    }
}
