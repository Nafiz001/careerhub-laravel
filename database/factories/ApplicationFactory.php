<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'job_post_id' => Job::factory(),
            'seeker_id' => User::factory()->state(['role' => 'seeker']),
            'cover_letter' => fake()->paragraph(3),
            'status' => 'pending',
        ];
    }
}
