<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_code' => null,
            'name' => fake()->name(),
            'call_sign' => fake()->firstName(),
            'phone' => fake()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'domicile' => fake()->city(),
            'division' => fake()->randomElement(['Coordinator', 'Documentation', 'Technical', 'Logistics']),
            'position' => fake()->jobTitle(),
            'specialization' => fake()->randomElement(['Event Management', 'Photography', 'Sound System', 'Decoration']),
            'daily_fee' => fake()->numberBetween(300000, 750000),
            'status' => 'aktif',
        ];
    }
}
