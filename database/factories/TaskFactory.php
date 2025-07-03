<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => 'pending',
            'due_date' => fake()->dateTimeBetween('+1 day', '+1 month'),
            // Secara default, buat user baru untuk created_by dan assigned_to
            'created_by' => User::factory(),
            'assigned_to' => User::factory(),
        ];
    }
}