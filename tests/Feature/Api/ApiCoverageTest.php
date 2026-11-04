<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Package;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_and_content_routes_return_json(): void
    {
        Category::create(['name' => 'Dekorasi']);

        $this->getJson('/api/catalog/categories')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Dekorasi');

        $this->getJson('/api/content/articles')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'current_page']);
    }

    public function test_about_api_returns_the_existing_page_content_as_json(): void
    {
        $response = $this->getJson('/api/about')
            ->assertOk()
            ->assertJsonPath('data.title', 'Tentang Kami - MyDream Wedding Organizer')
            ->assertJsonPath('data.description', 'Kenali MyDream Wedding Organizer, pendamping pernikahan di Jember dan Surabaya.');

        $response->assertJsonPath('data.hero.title', 'Bagian dari MyDream Group')
            ->assertJsonPath('data.values.0.title', 'Personal & Hangat');

        $this->withoutVite()->get('/tentang-kami')
            ->assertOk()
            ->assertSee('Bagian dari MyDream Group')
            ->assertSee('Personal &amp; Hangat', false);
    }

    public function test_authenticated_api_cart_is_persisted_and_isolated_per_user(): void
    {
        $vendor = Vendor::create([
            'name' => 'Vendor Cart',
            'slug' => 'vendor-cart',
            'price' => 125000,
            'phone' => '08123456789',
            'address' => 'Jakarta',
        ]);
        $firstUser = User::factory()->create(['role' => 'client']);
        $secondUser = User::factory()->create(['role' => 'client']);

        Sanctum::actingAs($firstUser);
        $this->postJson("/api/cart/vendors/{$vendor->id}", [])->assertOk()
            ->assertJsonPath('data.cart.type', 'custom')
            ->assertJsonPath('data.cart.vendor_ids.0', $vendor->id)
            ->assertJsonPath('data.total_price', 125000);

        Sanctum::actingAs($secondUser);
        $this->getJson('/api/cart')->assertOk()
            ->assertJsonPath('data.cart.type', null)
            ->assertJsonCount(0, 'data.vendors');

        Sanctum::actingAs($firstUser);
        $this->getJson('/api/cart')->assertOk()
            ->assertJsonPath('data.cart.vendor_ids.0', $vendor->id);
        $this->deleteJson("/api/cart/vendors/{$vendor->id}")->assertOk()
            ->assertJsonPath('data.cart.type', null);

        $this->postJson("/api/cart/vendors/{$vendor->id}", [])->assertOk();
        $this->deleteJson('/api/cart')->assertOk()->assertJsonCount(0, 'data.vendors');
    }

    public function test_cart_package_selection_replaces_custom_items_and_blocks_vendor_addition(): void
    {
        $vendor = Vendor::create([
            'name' => 'Vendor Bundle',
            'slug' => 'vendor-bundle',
            'price' => 250000,
            'phone' => '08123456789',
            'address' => 'Jakarta',
        ]);
        $package = Package::create([
            'name' => 'Paket Cart',
            'slug' => 'paket-cart',
            'availability_date' => now()->addMonth()->toDateString(),
        ]);
        $package->vendors()->attach($vendor->id);
        Sanctum::actingAs(User::factory()->create(['role' => 'client']));

        $this->postJson("/api/cart/vendors/{$vendor->id}", [])->assertOk();
        $this->postJson("/api/cart/packages/{$package->id}", [])->assertOk()
            ->assertJsonPath('data.cart.type', 'package')
            ->assertJsonPath('data.cart.package_id', $package->id)
            ->assertJsonPath('data.total_price', 250000);
        $this->postJson("/api/cart/vendors/{$vendor->id}", [])->assertStatus(409);
        $this->deleteJson('/api/cart')->assertOk()->assertJsonPath('data.cart.type', null);
    }

    public function test_admin_crud_requires_sanctum_and_admin_role(): void
    {
        $this->postJson('/api/admin/categories', ['name' => 'Dekorasi'])->assertUnauthorized();

        Sanctum::actingAs(User::factory()->create(['role' => 'client']));
        $this->postJson('/api/admin/categories', ['name' => 'Dekorasi'])->assertForbidden();

        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $this->postJson('/api/admin/categories', ['name' => 'Dekorasi'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Dekorasi');
        $this->getJson('/api/admin/categories')->assertOk()->assertJsonPath('data.0.name', 'Dekorasi');
        $categoryId = Category::where('name', 'Dekorasi')->value('id');
        $this->putJson("/api/admin/categories/{$categoryId}", ['name' => 'Dekorasi Baru'])
            ->assertOk()->assertJsonPath('data.name', 'Dekorasi Baru');
        $this->deleteJson("/api/admin/categories/{$categoryId}")->assertOk();
    }

    public function test_client_can_create_and_list_a_booking_through_json_api(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        Sanctum::actingAs($user);

        $this->postJson('/api/client/profile', [
            'groom_name' => 'Ari',
            'bride_name' => 'Dina',
            'groom_phone' => '081234567890',
            'bride_phone' => '081234567891',
            'email' => 'ari@example.test',
        ])->assertOk()->assertJsonPath('data.groom_name', 'Ari');

        $category = Category::create(['name' => 'Dekorasi']);
        $vendors = collect(range(1, 3))->map(fn ($i) => Vendor::create([
            'name' => "Vendor {$i}",
            'slug' => "vendor-{$i}",
            'category_id' => $category->id,
            'price' => 100000 * $i,
            'phone' => '08123456789',
            'address' => 'Jakarta',
        ]));

        $bookingResponse = $this->postJson('/api/client/bookings', [
            'vendor_ids' => $vendors->pluck('id')->all(),
            'venue_name' => 'Gedung Utama',
            'venue_address' => 'Jalan Mawar 1',
            'venue_city' => 'Jakarta',
            'venue_province' => 'DKI Jakarta',
            'guest_count' => 100,
            'event_date' => now()->addMonth()->toDateString(),
        ])->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonCount(3, 'data.events.0.schedules');

        $this->getJson('/api/client/bookings')->assertOk()->assertJsonPath('data.0.venue_name', 'Gedung Utama');
        $this->postJson('/api/client/events', [
            'booking_id' => $bookingResponse->json('data.id'),
            'name' => 'Acara Tambahan',
            'event_type' => 'reception',
            'event_date' => now()->addMonth()->toDateString(),
            'guest_count' => 100,
            'vendors' => [
                [
                    'vendor_id' => $vendors->first()->id,
                    'activity' => 'Dekorasi',
                    'start_time' => '09:00',
                    'end_time' => '12:00',
                ],
            ],
        ])->assertCreated()->assertJsonPath('data.name', 'Acara Tambahan');
        $this->getJson('/api/client/events')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_admin_vendor_mutations_are_not_public(): void
    {
        $this->postJson('/api/vendors', [])->assertUnauthorized();
        $this->postJson('/api/admin/vendors', [])->assertNotFound();

        Sanctum::actingAs(User::factory()->create(['role' => 'member']));
        $this->postJson('/api/vendors', [])->assertForbidden();
        $this->postJson('/api/admin/vendors', [])->assertNotFound();

        $category = Category::create(['name' => 'Katering']);
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $this->postJson('/api/admin/vendors', [])->assertNotFound();
        $this->postJson('/api/vendors', [
            'name' => 'Catering Nusantara',
            'category_id' => $category->id,
            'price' => 1000000,
            'phone' => '08123456789',
            'address' => 'Jakarta',
        ])->assertCreated()->assertJsonPath('data.name', 'Catering Nusantara');
    }

    public function test_admin_can_create_an_event_and_read_it_from_the_calendar_api(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $client = Client::create([
            'users_id' => $user->id,
            'groom_name' => 'Ari',
            'bride_name' => 'Dina',
        ]);
        $booking = Booking::create([
            'client_id' => $client->id,
            'venue_name' => 'Gedung Utama',
            'venue_address' => 'Jalan Mawar',
            'venue_city' => 'Jakarta',
            'venue_province' => 'DKI Jakarta',
            'guest_count' => 100,
            'total_price' => 500000,
            'status' => 'approved',
        ]);
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $date = now()->addMonth()->toDateString();
        $eventId = $this->postJson('/api/admin/events', [
            'booking_id' => $booking->id,
            'name' => 'Akad Nikah',
            'event_date' => $date,
            'status' => 'upcoming',
            'schedules' => [
                ['activity' => 'Persiapan', 'start_time' => '09:00', 'status' => 'pending'],
            ],
        ])->assertCreated()
            ->assertJsonPath('data.name', 'Akad Nikah')
            ->json('data.id');

        $this->getJson('/api/admin/calendar/events?date=' . $date)
            ->assertOk()->assertJsonPath('data.0.id', $eventId);
        $this->getJson("/api/admin/tracking/events/{$eventId}")
            ->assertOk()->assertJsonPath('progress.total', 1);
    }
}
