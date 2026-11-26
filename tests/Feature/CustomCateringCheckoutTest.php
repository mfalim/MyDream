<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Package;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CustomCateringCheckoutTest extends TestCase
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

    public function test_custom_cart_reprices_catering_by_guest_count_but_keeps_other_vendor_prices(): void
    {
        [$clientUser, $vendors] = $this->createOrderData();
        $this->actingAs($clientUser)->withSession([
            'cart' => [
                'type' => 'custom',
                'package_id' => null,
                'vendor_ids' => $vendors->take(2)->pluck('id')->all(),
            ],
        ]);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertDontSee('Jumlah tamu untuk catering')
            ->assertSee('Rp 13.000.000')
            ->assertDontSee('Rp 10.000.000')
            ->assertDontSee('Rp 3.000.000');

        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('id="guest_count"', false)
            ->assertSee('Rp 13.000.000')
            ->assertDontSee('Rp 10.000.000')
            ->assertDontSee('Rp 3.000.000')
            ->assertDontSee('data-base-price', false);

        $this->postJson(route('user.checkout.quote'), ['guest_count' => 50])
            ->assertOk()
            ->assertJsonPath('totalPrice', 5500000)
            ->assertJsonPath('dpAmount', (int) ceil(5500000 * config('mydream.dp_percent') / 100));

        $this->postJson(route('user.checkout.quote'), ['guest_count' => null])
            ->assertOk()
            ->assertJsonPath('totalPrice', 13000000);

        $this->post(route('checkout.store'), $this->checkoutData(201))
            ->assertSessionHasErrors('guest_count');

        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Rp 13.000.000')
            ->assertDontSee('Rp 10.000.000')
            ->assertDontSee('Rp 3.000.000');

        $this->post(route('checkout.store'), $this->checkoutData())
            ->assertRedirect(route('payment.index', 1));

        $this->assertDatabaseHas('bookings', [
            'id' => 1,
            'guest_count' => 50,
            'total_price' => 5500000,
        ]);
        $this->assertDatabaseHas('schedules', [
            'vendor_id' => $vendors[0]->id,
            'price' => 2500000,
            'agreed_fee' => 2500000,
        ]);
        $this->assertDatabaseHas('schedules', [
            'vendor_id' => $vendors[1]->id,
            'price' => 3000000,
            'is_open' => true,
            'agreed_fee' => 3000000,
        ]);
    }

    public function test_custom_cart_hides_total_until_two_vendors_are_selected(): void
    {
        [$clientUser, $vendors] = $this->createOrderData();
        $this->actingAs($clientUser)->withSession([
            'cart' => [
                'type' => 'custom',
                'package_id' => null,
                'vendor_ids' => [$vendors[0]->id],
            ],
        ]);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Total biaya ditampilkan setelah minimal 2 vendor dipilih.')
            ->assertDontSee('Rp 10.000.000')
            ->assertDontSee('DP minimal');

        $this->get(route('user.cart'))
            ->assertOk()
            ->assertSee('Total biaya ditampilkan setelah minimal 2 vendor dipilih.')
            ->assertDontSee('Rp 10.000.000');

        $this->withSession([
            'cart' => [
                'type' => 'custom',
                'package_id' => null,
                'vendor_ids' => $vendors->take(2)->pluck('id')->all(),
            ],
        ])->get(route('user.cart'))
            ->assertOk()
            ->assertSee('Rp 13.000.000')
            ->assertDontSee('Rp 10.000.000')
            ->assertDontSee('Rp 3.000.000');
    }

    public function test_package_catering_price_is_not_reduced_for_a_smaller_guest_count(): void
    {
        [$clientUser, $vendors] = $this->createOrderData();
        $package = Package::create([
            'name' => 'Full Catering Package',
            'guest_capacity' => 300,
        ]);
        $package->vendors()->attach($vendors[0]->id);

        $this->actingAs($clientUser)->withSession([
            'cart' => [
                'type' => 'package',
                'package_id' => $package->id,
                'vendor_ids' => [],
            ],
        ]);

        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Rp 10.000.000');

        $this->post(route('checkout.store'), $this->checkoutData(50))
            ->assertRedirect(route('payment.index', 1));

        $this->assertDatabaseHas('bookings', [
            'id' => 1,
            'guest_count' => 50,
            'total_price' => 10000000,
        ]);
        $this->assertDatabaseHas('schedules', [
            'vendor_id' => $vendors[0]->id,
            'price' => 10000000,
            'agreed_fee' => 10000000,
        ]);
    }

    public function test_public_catalog_hides_individual_vendor_prices_but_keeps_package_price(): void
    {
        [, $vendors] = $this->createOrderData();
        $package = Package::create([
            'name' => 'Decor Package',
            'guest_capacity' => 100,
        ]);
        $package->vendors()->attach($vendors[2]->id);

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Rp 4.000.000')
            ->assertDontSee('Rp 10.000.000')
            ->assertDontSee('Rp 3.000.000');
    }

    private function createOrderData(): array
    {
        $clientUser = User::create([
            'name' => 'Wedding Client',
            'email' => 'client@example.test',
            'role' => 'client',
            'status' => 'approved',
        ]);
        Client::create([
            'users_id' => $clientUser->id,
            'groom_name' => 'Groom',
            'bride_name' => 'Bride',
        ]);

        $cateringCategory = Category::create(['name' => 'Catering']);
        $photoCategory = Category::create(['name' => 'Photography']);
        $decorCategory = Category::create(['name' => 'Decoration']);

        $vendors = collect([
            Vendor::create([
                'name' => 'Catering Vendor',
                'category_id' => $cateringCategory->id,
                'price' => 10000000,
                'guest_capacity' => 200,
                'is_open' => true,
                'phone' => '081234567890',
                'address' => 'Jakarta',
            ]),
            Vendor::create([
                'name' => 'Photography Vendor',
                'category_id' => $photoCategory->id,
                'price' => 3000000,
                'is_open' => true,
                'phone' => '081234567891',
                'address' => 'Jakarta',
            ]),
            Vendor::create([
                'name' => 'Decoration Vendor',
                'category_id' => $decorCategory->id,
                'price' => 4000000,
                'is_open' => true,
                'phone' => '081234567892',
                'address' => 'Jakarta',
            ]),
        ]);

        return [$clientUser, $vendors];
    }

    private function checkoutData(?int $guestCount = null): array
    {
        return [
            'venue_name' => 'Grand Hall',
            'venue_address' => 'Jl. Merdeka',
            'venue_city' => 'Jakarta',
            'venue_province' => 'DKI Jakarta',
            'guest_count' => $guestCount ?? 50,
            'event_date' => now()->addDays(30)->toDateString(),
        ];
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
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
        });

        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->unsignedInteger('guest_capacity')->nullable();
            $table->boolean('is_open')->default(false);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->string('city')->nullable();
            $table->string('instagram')->nullable();
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
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->unsignedInteger('guest_capacity')->nullable();
            $table->timestamps();
        });

        Schema::create('package_vendor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id');
            $table->foreignId('vendor_id');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id');
            $table->unsignedBigInteger('package_id')->nullable();
            $table->string('venue_name');
            $table->text('venue_address');
            $table->string('venue_city');
            $table->string('venue_province');
            $table->integer('guest_count');
            $table->decimal('total_price', 15, 2)->nullable();
            $table->string('status')->default('pending');
            $table->string('invoice_number')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id');
            $table->string('name');
            $table->string('event_type')->nullable();
            $table->date('event_date');
            $table->integer('guest_count')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedBigInteger('package_id')->nullable();
            $table->string('status')->default('scheduled');
            $table->timestamps();
        });

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id');
            $table->foreignId('vendor_id')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->string('activity');
            $table->string('location')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('agreed_fee', 15, 2)->nullable();
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->string('payment_status')->default('unpaid');
            $table->timestamps();
        });
    }
}
