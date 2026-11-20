<?php

namespace Tests\Feature\Vendor;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Client;
use App\Models\Event;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VendorPortalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach (['schedules', 'events', 'bookings', 'clients', 'vendor_photos', 'vendors', 'categories', 'users'] as $table) {
            Schema::dropIfExists($table);
        }

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
            $table->foreignId('user_id')->nullable()->unique();
            $table->foreignId('category_id')->nullable();
            $table->string('name')->unique();
            $table->string('slug')->nullable()->unique();
            $table->decimal('price', 15, 2)->nullable();
            $table->unsignedInteger('guest_capacity')->nullable();
            $table->string('phone')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('instagram')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('vendor_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id');
            $table->string('photo');
            $table->boolean('is_cover')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id');
            $table->string('groom_name');
            $table->string('bride_name');
            $table->string('groom_phone')->nullable();
            $table->string('bride_phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id');
            $table->foreignId('package_id')->nullable();
            $table->string('venue_name');
            $table->text('venue_address');
            $table->string('venue_city');
            $table->string('venue_province');
            $table->integer('guest_count');
            $table->decimal('total_price', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id');
            $table->string('name');
            $table->string('photo')->nullable();
            $table->string('event_type')->nullable();
            $table->date('event_date');
            $table->unsignedInteger('guest_count')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->foreignId('package_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('upcoming');
            $table->timestamps();
        });

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id');
            $table->foreignId('vendor_id')->nullable();
            $table->string('activity');
            $table->string('location')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function test_vendor_portal_only_shows_records_assigned_to_logged_in_vendor(): void
    {
        [$vendorUser, $vendor] = $this->createVendor('vendor-one');
        [, $otherVendor] = $this->createVendor('vendor-two');
        $ownEvent = $this->createEventForVendor($vendor, 'Own wedding');
        $otherEvent = $this->createEventForVendor($otherVendor, 'Other wedding');

        $this->actingAs($vendorUser);

        $this->get(route('vendor.dashboard'))
            ->assertOk()
            ->assertSee('Own wedding')
            ->assertDontSee('Other wedding');

        $this->get(route('vendor.kalender', [
            'bulan' => now()->month,
            'tahun' => now()->year,
            'tanggal' => $ownEvent->event_date->format('Y-m-d'),
        ]))
            ->assertOk()
            ->assertSee('Own wedding')
            ->assertDontSee('Other wedding')
            ->assertSee('data-calendar-event', false)
            ->assertSee('<dialog', false)
            ->assertSee('dialog.showModal()', false)
            ->assertDontSee('class="event-detail"', false);

        $this->get(route('vendor.tracking'))
            ->assertOk()
            ->assertSee('Own task')
            ->assertDontSee('Other task');

        $this->get(route('vendor.profil'))
            ->assertOk()
            ->assertSee($vendor->name)
            ->assertDontSee($otherVendor->name)
            ->assertDontSee('9.876.543');

        $this->get(route('vendor.profil.edit'))
            ->assertOk()
            ->assertSee($vendor->name)
            ->assertDontSee($otherVendor->name)
            ->assertDontSee('9.876.543')
            ->assertDontSee('name="price"', false);
    }

    public function test_vendor_can_update_only_its_own_profile(): void
    {
        [$vendorUser, $vendor] = $this->createVendor('vendor-one');
        [, $otherVendor] = $this->createVendor('vendor-two');
        $category = Category::create(['name' => 'Dekorasi']);
        $vendor->update(['price' => 9876543]);

        $this->actingAs($vendorUser)
            ->put(route('vendor.profil.update'), [
                'name' => 'Updated vendor',
                'category_id' => $category->id,
                'price' => 1500000,
                'phone' => '081234567890',
                'address' => 'Vendor street',
                'city' => 'Jakarta',
                'instagram' => '@updated',
                'description' => 'Updated description',
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '1234567890',
                'bank_account_holder' => 'Updated vendor',
            ])
            ->assertRedirect(route('vendor.profil'));

        $this->assertDatabaseHas('vendors', [
            'id' => $vendor->id,
            'user_id' => $vendorUser->id,
            'name' => 'Updated vendor',
            'city' => 'Jakarta',
            'price' => 9876543,
        ]);
        $this->assertDatabaseHas('vendors', [
            'id' => $otherVendor->id,
            'name' => $otherVendor->name,
        ]);
    }

    public function test_vendor_without_a_linked_profile_cannot_read_vendor_pages(): void
    {
        $vendorUser = User::factory()->create([
            'role' => 'vendor',
            'username' => 'unlinked-vendor',
            'password' => 'vendor-password',
        ]);

        $this->actingAs($vendorUser);

        $this->get(route('vendor.dashboard'))->assertNotFound();
        $this->get(route('vendor.kalender'))->assertNotFound();
        $this->get(route('vendor.tracking'))->assertNotFound();
        $this->get(route('vendor.profil'))->assertNotFound();
    }

    public function test_admin_can_create_vendor_profile_and_login_account_together(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Dekorasi']);

        $this->actingAs($admin)
            ->post(route('admin.vendors.store'), [
                'name' => 'Aster Decor',
                'username' => 'aster-decor',
                'email' => 'aster@example.test',
                'password' => 'vendor-secret-123',
                'password_confirmation' => 'vendor-secret-123',
                'category_id' => $category->id,
                'price' => 2500000,
                'phone' => '081234567890',
                'address' => 'Jakarta',
            ])
            ->assertRedirect(route('admin.vendors.index'));

        $vendorUser = User::where('username', 'aster-decor')->firstOrFail();
        $vendor = Vendor::where('user_id', $vendorUser->id)->firstOrFail();

        $this->assertSame('vendor', $vendorUser->role);
        $this->assertSame('approved', $vendorUser->status);
        $this->assertTrue(Hash::check('vendor-secret-123', $vendorUser->password));

        $this->post(route('login.attempt'), [
            'login' => 'aster-decor',
            'password' => 'vendor-secret-123',
        ])
            ->assertRedirect(route('vendor.dashboard'));

        $this->get(route('vendor.profil'))
            ->assertOk()
            ->assertSee($vendor->name);
    }

    public function test_admin_must_set_capacity_when_creating_a_catering_vendor(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Catering']);
        $vendorData = [
            'name' => 'Catering Example',
            'username' => 'catering-example',
            'email' => 'catering@example.test',
            'password' => 'vendor-secret-123',
            'password_confirmation' => 'vendor-secret-123',
            'category_id' => $category->id,
            'price' => 10000000,
            'phone' => '081234567890',
            'address' => 'Jakarta',
        ];

        $this->actingAs($admin)
            ->post(route('admin.vendors.store'), $vendorData)
            ->assertSessionHasErrors('guest_capacity');

        $this->post(route('admin.vendors.store'), $vendorData + ['guest_capacity' => 200])
            ->assertRedirect(route('admin.vendors.index'));

        $this->assertDatabaseHas('vendors', [
            'name' => 'Catering Example',
            'guest_capacity' => 200,
        ]);
    }

    public function test_admin_can_link_an_existing_vendor_login_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Dekorasi']);
        $vendorUser = User::factory()->create([
            'role' => 'vendor',
            'username' => null,
            'password' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.vendors.store'), [
                'user_id' => $vendorUser->id,
                'username' => 'linked-vendor',
                'email' => $vendorUser->email,
                'password' => 'linked-secret-123',
                'password_confirmation' => 'linked-secret-123',
                'name' => 'Linked Vendor',
                'category_id' => $category->id,
                'price' => 1000000,
                'phone' => '081234567890',
                'address' => 'Jakarta',
            ])
            ->assertRedirect(route('admin.vendors.index'));

        $this->assertDatabaseHas('vendors', [
            'user_id' => $vendorUser->id,
            'name' => 'Linked Vendor',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $vendorUser->id,
            'username' => 'linked-vendor',
            'role' => 'vendor',
        ]);
    }

    private function createVendor(string $username): array
    {
        $user = User::factory()->create([
            'role' => 'vendor',
            'username' => $username,
            'password' => 'vendor-password',
        ]);

        $vendor = Vendor::create([
            'user_id' => $user->id,
            'name' => $username,
            'price' => 9876543,
            'phone' => '081234567890',
            'address' => 'Jakarta',
        ]);

        return [$user, $vendor];
    }

    private function createEventForVendor(Vendor $vendor, string $eventName): Event
    {
        $clientUser = User::factory()->create();
        $client = Client::create([
            'users_id' => $clientUser->id,
            'groom_name' => 'Groom',
            'bride_name' => 'Bride',
        ]);
        $booking = Booking::create([
            'client_id' => $client->id,
            'venue_name' => 'Ballroom',
            'venue_address' => 'Jakarta',
            'venue_city' => 'Jakarta',
            'venue_province' => 'DKI Jakarta',
            'guest_count' => 100,
            'status' => 'approved',
        ]);
        $event = Event::create([
            'booking_id' => $booking->id,
            'name' => $eventName,
            'event_date' => now()->toDateString(),
            'status' => 'upcoming',
        ]);

        Schedule::create([
            'event_id' => $event->id,
            'vendor_id' => $vendor->id,
            'activity' => $vendor->name === 'vendor-one' ? 'Own task' : 'Other task',
            'start_time' => '08:00:00',
            'status' => 'pending',
        ]);

        return $event;
    }
}
