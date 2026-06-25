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
        $skillPool = [
            'PHP', 'Laravel', 'Vue.js', 'React', 'TypeScript', 'MySQL',
            'PostgreSQL', 'Redis', 'Docker', 'AWS', 'Tailwind CSS', 'Inertia.js',
            'Python', 'Node.js', 'GraphQL', 'CI/CD', 'Figma', 'SQL',
        ];

        $salaryMin = fake()->numberBetween(20, 130) * 1000;
        $salaryMax = $salaryMin + fake()->numberBetween(20, 70) * 1000;

        return [
            'employer_id' => User::factory()->state(['role' => 'employer']),
            'title' => fake()->jobTitle(),
            'company' => fake()->company(),
            'location' => fake()->randomElement(['Dhaka, Bangladesh', 'Chattogram, Bangladesh', 'Sylhet, Bangladesh', 'Remote']),
            'type' => fake()->randomElement(Job::TYPES),
            'category' => fake()->randomElement(Job::CATEGORIES),
            'experience_level' => fake()->randomElement(Job::EXPERIENCE_LEVELS),
            'salary_range' => 'BDT '.number_format($salaryMin).' - '.number_format($salaryMax),
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMax,
            'description' => fake()->paragraph(5),
            'skills' => fake()->randomElements($skillPool, fake()->numberBetween(3, 6)),
            'remote' => fake()->boolean(30),
            'application_deadline' => fake()->dateTimeBetween('+1 week', '+2 months')->format('Y-m-d'),
            'is_featured' => fake()->boolean(20),
            'is_open' => true,
            'views_count' => fake()->numberBetween(0, 500),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => ['is_open' => false]);
    }

    public function remote(): static
    {
        return $this->state(fn () => ['remote' => true, 'location' => 'Remote', 'type' => 'remote']);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
