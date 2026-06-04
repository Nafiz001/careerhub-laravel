<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * A seeker with a fleshed-out profile.
     */
    public function seeker(): static
    {
        return $this->state(fn () => [
            'role' => 'seeker',
            'headline' => fake()->jobTitle(),
            'bio' => fake()->paragraph(3),
            'location' => fake()->randomElement(['Dhaka, Bangladesh', 'Chattogram, Bangladesh', 'Remote']),
            'phone' => '+8801'.fake()->numerify('#########'),
            'skills' => fake()->randomElements(
                ['PHP', 'Laravel', 'Vue.js', 'React', 'TypeScript', 'MySQL', 'Docker', 'AWS', 'Python'],
                fake()->numberBetween(3, 6)
            ),
            'experience_level' => fake()->randomElement(User::EXPERIENCE_LEVELS),
        ]);
    }

    /**
     * An employer with a company profile.
     */
    public function employer(): static
    {
        $company = fake()->company();

        return $this->state(fn () => [
            'role' => 'employer',
            'company_name' => $company,
            'website' => 'https://'.Str::slug($company).'.com',
            'about' => fake()->paragraph(3),
            'location' => fake()->randomElement(['Dhaka, Bangladesh', 'Chattogram, Bangladesh']),
        ]);
    }
}
