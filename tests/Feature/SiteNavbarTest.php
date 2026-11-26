<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class SiteNavbarTest extends TestCase
{
    public function test_vendor_dashboard_button_links_to_vendor_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'vendor']))
            ->blade('<x-site.navbar />')
            ->assertSee(route('vendor.dashboard'), false)
            ->assertDontSee(route('user.dashboard'), false);
    }
}
