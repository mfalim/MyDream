<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_categories(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        Category::create([
            'name' => 'Dekorasi',
        ]);

        $response = $this->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertViewHas('categories');
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);
        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Dekorasi',
        ]);

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Dekorasi',
        ]);
    }

    public function test_category_name_is_required(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);
        $response = $this->post(route('admin.categories.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_category_name_must_be_unique(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);
        Category::create([
            'name' => 'Dekorasi',
        ]);

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Dekorasi',
        ]);

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('categories', 1);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);
        $category = Category::create([
            'name' => 'Dekorasi',
        ]);

        $response = $this->put(
            route('admin.categories.update', $category),
            [
                'name' => 'Katering',
            ]
        );

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Katering',
        ]);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);
        $category = Category::create([
            'name' => 'Dekorasi',
        ]);

        $response = $this->delete(
            route('admin.categories.destroy', $category)
        );

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}