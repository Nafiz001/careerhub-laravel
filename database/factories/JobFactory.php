<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        return [
            'employer_id' => User::factory()->state(['role' => 'employer']),
            'title' => fake()->jobTitle(),
            'company' => fake()->company(),
            'location' => fake()->randomElement(['Dhaka, Bangladesh', 'Chattogram, Bangladesh', 'Remote']),
            'type' => fake()->randomElement(Job::TYPES),
            'salary_range' => 'BDT 50,000 - 90,000',
            'description' => fake()->paragraph(5),
            'is_open' => true,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => ['is_open' => false]);
    }
}
