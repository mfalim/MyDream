<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\Package;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'google_id' => 'test_google_id_' . time(),
            'avatar' => 'https://via.placeholder.com/150',
            'role' => 'admin',
            'status' => 'approved',
        ]);

        // Create categories
        $categories = [
            'Catering',
            'Photography',
            'Decoration',
            'Music & Entertainment',
            'Makeup Artist',
            'Wedding Cake',
        ];

        foreach ($categories as $categoryName) {
            Category::create(['name' => $categoryName]);
        }

        // Create vendors
        $categoryModels = Category::all();
        
        foreach ($categoryModels as $category) {
            for ($i = 1; $i <= 3; $i++) {
                Vendor::create([
                    'user_id' => $user->id,
                    'name' => $category->name . ' Vendor ' . $i,
                    'category_id' => $category->id,
                    'phone' => '0812345678' . $i,
                    'address' => 'Jl. Test No. ' . $i,
                    'description' => 'Description for ' . $category->name . ' Vendor ' . $i,
                    'photo' => null,
                ]);
            }
        }

        // Create packages
        $packages = [
            ['name' => 'Standard Package', 'price' => 50000000],
            ['name' => 'Premium Package', 'price' => 75000000],
            ['name' => 'Grand Package', 'price' => 100000000],
            ['name' => 'VIP Package', 'price' => 150000000],
        ];

        foreach ($packages as $packageData) {
            Package::create($packageData);
        }
    }
}
