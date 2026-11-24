<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VendorAvailabilityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        $this->createSchema();
    }

    public function test_vendor_can_open_and_close_their_booking_session(): void
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'name' => 'Vendor Session',
            'slug' => 'vendor-session',
            'phone' => '08123456789',
            'address' => 'Jember',
        ]);

        $this->actingAs($user)
            ->patch(route('vendor.availability.update'), ['is_open' => 1])
            ->assertRedirect(route('vendor.dashboard'));

        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'is_open' => true]);

        $this->patch(route('vendor.availability.update'), ['is_open' => 0])
            ->assertRedirect(route('vendor.dashboard'));

        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'is_open' => false]);
    }

    public function test_homepage_hides_closed_vendors_and_featured_packages_with_any_closed_vendor(): void
    {
        $closedVendor = Vendor::create([
            'name' => 'Closed Vendor',
            'slug' => 'closed-vendor',
            'price' => 100000,
            'phone' => '08123456789',
            'address' => 'Jember',
            'is_open' => false,
        ]);
        $openVendor = Vendor::create([
            'name' => 'Open Vendor',
            'slug' => 'open-vendor',
            'price' => 200000,
            'phone' => '08123456788',
            'address' => 'Jember',
            'is_open' => true,
        ]);
        $availablePackage = Package::create([
            'name' => 'Featured And Available',
            'availability_date' => now()->addMonth()->toDateString(),
            'is_featured' => true,
        ]);
        $availablePackage->vendors()->attach($openVendor->id);
        $featuredPackage = Package::create([
            'name' => 'Featured But Unavailable',
            'availability_date' => now()->addMonth()->toDateString(),
            'is_featured' => true,
        ]);
        $featuredPackage->vendors()->attach($closedVendor->id);
        $featuredPackage->vendors()->attach($openVendor->id);

        $this->withoutVite()
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Open Vendor')
            ->assertDontSee('Closed Vendor')
            ->assertSee('Featured And Available')
            ->assertDontSee('Featured But Unavailable');

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Open Vendor')
            ->assertDontSee('Closed Vendor')
            ->assertSee('Featured And Available')
            ->assertDontSee('Featured But Unavailable');

        $this->get(route('store'))
            ->assertOk()
            ->assertSee('Open Vendor')
            ->assertDontSee('Closed Vendor')
            ->assertSee('Featured And Available')
            ->assertDontSee('Featured But Unavailable');

        $this->getJson('/api/home')
            ->assertOk()
            ->assertJsonMissing(['name' => 'Closed Vendor'])
            ->assertJsonMissing(['name' => 'Featured But Unavailable']);

        $this->get(route('catalog.show', $featuredPackage->slug))->assertNotFound();
        $this->get(route('vendor.show', 'closed-vendor'))->assertNotFound();
    }

    public function test_closed_vendor_or_package_cannot_be_added_to_a_cart(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::create([
            'name' => 'Offline Vendor',
            'slug' => 'offline-vendor',
            'phone' => '08123456789',
            'address' => 'Jember',
            'is_open' => false,
        ]);
        $package = Package::create([
            'name' => 'Offline Package',
            'availability_date' => now()->addMonth()->toDateString(),
            'is_featured' => true,
        ]);
        $package->vendors()->attach($vendor->id);

        $this->actingAs($client)
            ->from(route('catalog.index'))
            ->post(route('user.cart.add-vendor', $vendor->id))
            ->assertRedirect(route('catalog.index'))
            ->assertSessionHas('error');

        $this->from(route('catalog.index'))
            ->post(route('user.cart.add-package', $package->id))
            ->assertRedirect(route('catalog.index'))
            ->assertSessionHas('error');

        $this->withSession([
            'cart' => ['type' => 'package', 'package_id' => $package->id, 'vendor_ids' => []],
        ])->get(route('user.checkout'))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->withSession([
            'cart' => ['type' => 'package', 'package_id' => $package->id, 'vendor_ids' => []],
        ])->post(route('user.checkout.store'), [
            'venue_name' => 'Gedung Utama',
            'venue_address' => 'Jalan Merdeka',
            'venue_city' => 'Jember',
            'venue_province' => 'Jawa Timur',
            'guest_count' => 100,
            'event_date' => now()->addMonth()->toDateString(),
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->string('role')->default('client');
            $table->string('status')->default('approved');
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->unsignedInteger('guest_capacity')->nullable();
            $table->string('phone')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->string('city')->nullable();
            $table->string('instagram')->nullable();
            $table->boolean('is_open')->default(false);
            $table->timestamps();
        });

        Schema::create('vendor_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->string('photo');
            $table->boolean('is_cover')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->date('availability_date')->nullable();
            $table->unsignedInteger('guest_capacity')->nullable();
            $table->decimal('duration', 8, 2)->nullable();
            $table->string('event_period')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('package_vendor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('vendor_id');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_published')->default(false);
            $table->date('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('public_events', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('inspirations', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('inspiration_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspiration_id');
            $table->string('photo');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
        });
    }
}
