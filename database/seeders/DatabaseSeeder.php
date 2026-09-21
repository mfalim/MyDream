<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\Package;
use App\Models\Client;
use App\Models\Booking;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'google_id' => 'admin_123456',
            'avatar' => 'https://ui-avatars.com/api/?name=Admin+User',
            'role' => 'admin',
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

        // Create vendors
        $vendorData = [
            ['name' => 'Delicious Catering', 'category_id' => 1, 'phone' => '081234567890', 'address' => 'Jakarta Selatan'],
            ['name' => 'Golden Moments Photography', 'category_id' => 2, 'phone' => '081234567891', 'address' => 'Jakarta Pusat'],
            ['name' => 'Elegant Decor', 'category_id' => 3, 'phone' => '081234567892', 'address' => 'Jakarta Barat'],
            ['name' => 'Live Band Harmony', 'category_id' => 4, 'phone' => '081234567893', 'address' => 'Jakarta Timur'],
            ['name' => 'Perfect Shot Videography', 'category_id' => 5, 'phone' => '081234567894', 'address' => 'Jakarta Utara'],
            ['name' => 'Glamour Makeup Studio', 'category_id' => 6, 'phone' => '081234567895', 'address' => 'Tangerang'],
            ['name' => 'Sweet Dreams Cake', 'category_id' => 7, 'phone' => '081234567896', 'address' => 'Bekasi'],
            ['name' => 'Crystal Sound System', 'category_id' => 8, 'phone' => '081234567897', 'address' => 'Depok'],
        ];

        foreach ($vendorData as $vendor) {
            Vendor::create([
                'user_id' => null,
                'name' => $vendor['name'],
                'category_id' => $vendor['category_id'],
                'phone' => $vendor['phone'],
                'address' => $vendor['address'],
                'description' => 'Professional ' . $vendor['name'] . ' services',
                'photo' => null,
            ]);
        }

        // Create packages with vendors
        $packages = [
            [
                'name' => 'Standard Package',
                'price' => 25000000,
                'vendor_ids' => [1, 2, 3], // Catering, Photography, Decoration
            ],
            [
                'name' => 'Premium Package',
                'price' => 50000000,
                'vendor_ids' => [1, 2, 3, 4, 5], // + Entertainment, Videography
            ],
            [
                'name' => 'Grand Package',
                'price' => 75000000,
                'vendor_ids' => [1, 2, 3, 4, 5, 6, 7], // + Makeup, Cake
            ],
            [
                'name' => 'VIP Package',
                'price' => 100000000,
                'vendor_ids' => [1, 2, 3, 4, 5, 6, 7, 8], // All vendors
            ],
        ];

        foreach ($packages as $packageData) {
            $package = Package::create([
                'name' => $packageData['name'],
                'price' => $packageData['price'],
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

        echo "\nSeeding completed successfully!\n";
        echo "- Admin user created (admin@example.com)\n";
        echo "- " . count($categories) . " categories created\n";
        echo "- " . count($vendorData) . " vendors created\n";
        echo "- " . count($packages) . " packages created with vendors\n";
    }
}
