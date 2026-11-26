<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

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
    }

    public function test_client_is_returned_to_the_booking_page_after_login(): void
    {
        $user = User::create([
            'name' => 'Test Client',
            'username' => 'test-client',
            'email' => 'client@example.com',
            'password' => Hash::make('password', ['rounds' => 4]),
            'role' => 'client',
            'status' => 'approved',
        ]);
        Client::create([
            'users_id' => $user->id,
            'groom_name' => 'Groom',
            'bride_name' => 'Bride',
        ]);

        $redirect = url('/paket/contoh?pilihan=paket');

        $this->withoutVite()
            ->get(route('login', ['redirect' => url('/paket/contoh?pilihan=paket')]))
            ->assertOk()
            ->assertSee('name="redirect" value="/paket/contoh?pilihan=paket"', false)
            ->assertSee(route('auth.google.redirect', ['redirect' => '/paket/contoh?pilihan=paket']), false);

        $this->post(route('login.attempt'), [
            'login' => 'test-client',
            'password' => 'password',
            'redirect' => '/paket/contoh?pilihan=paket',
        ])->assertRedirect($redirect);
    }

    public function test_external_login_redirect_is_not_used(): void
    {
        $this->withoutVite()
            ->get(route('login', ['redirect' => 'https://example.com/']))
            ->assertOk()
            ->assertSessionMissing('url.intended');
    }

    public function test_new_client_registration_returns_to_booking_after_completing_profile(): void
    {
        $redirect = url('/paket/contoh?pilihan=paket');

        $this->post(route('register.attempt'), [
            'name' => 'New Client',
            'username' => 'new-client',
            'email' => 'new-client@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'redirect' => '/paket/contoh?pilihan=paket',
        ])->assertRedirect(route('client.profile'));

        $this->post(route('client.profile.store'), [
            'groom_name' => 'Groom',
            'bride_name' => 'Bride',
            'groom_phone' => '08123456789',
            'bride_phone' => '08123456788',
        ])->assertRedirect($redirect);
    }
}
