<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed a realistic Bangladesh-flavoured job board:
     * 2 employers, 12 jobs, 2 seekers, sample applications.
     */
    public function run(): void
    {
        // ---- Employers --------------------------------------------------
        $brainStation = User::create([
            'name' => 'BrainStation 23',
            'email' => 'hr@brainstation-23.com',
            'role' => 'employer',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $pathao = User::create([
            'name' => 'Pathao',
            'email' => 'careers@pathao.com',
            'role' => 'employer',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // ---- Seekers ----------------------------------------------------
        $nafiz = User::create([
            'name' => 'Md. Nafiz Ahmed',
            'email' => 'nafiz@example.com',
            'role' => 'seeker',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $rumi = User::create([
            'name' => 'Rumana Akter',
            'email' => 'rumana@example.com',
            'role' => 'seeker',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // ---- Jobs -------------------------------------------------------
        $jobs = [
            [
                'employer' => $brainStation,
                'title' => 'Laravel Backend Developer',
                'company' => 'BrainStation 23',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'salary_range' => 'BDT 60,000 - 90,000',
                'description' => 'Build and maintain RESTful APIs and web applications using Laravel. You will work with MySQL, Redis, and queue workers in an Agile team. Strong Eloquent and PHP 8 skills required.',
            ],
            [
                'employer' => $brainStation,
                'title' => 'Vue.js Frontend Engineer',
                'company' => 'BrainStation 23',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'salary_range' => 'BDT 55,000 - 85,000',
                'description' => 'Craft delightful UIs with Vue 3 and the Composition API. Experience with Inertia.js, Tailwind CSS, and Vite is a big plus. You will collaborate closely with backend and design teams.',
            ],
            [
                'employer' => $brainStation,
                'title' => 'Full-Stack Developer (Laravel + Vue)',
                'company' => 'BrainStation 23',
                'location' => 'Remote',
                'type' => 'remote',
                'salary_range' => 'BDT 70,000 - 110,000',
                'description' => 'Own features end-to-end across a Laravel + Inertia + Vue stack. Write clean, tested code and ship to production weekly. Remote-first role open to candidates across Bangladesh.',
            ],
            [
                'employer' => $brainStation,
                'title' => 'QA Automation Engineer',
                'company' => 'BrainStation 23',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'salary_range' => 'BDT 45,000 - 70,000',
                'description' => 'Design and maintain automated test suites with PHPUnit, Pest, and Cypress. Champion quality across the SDLC and integrate tests into our CI pipeline.',
            ],
            [
                'employer' => $brainStation,
                'title' => 'DevOps Engineer',
                'company' => 'BrainStation 23',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'salary_range' => 'BDT 80,000 - 130,000',
                'description' => 'Manage CI/CD, Docker, and cloud infrastructure on AWS. Automate deployments for Laravel applications and keep our systems observable and resilient.',
            ],
            [
                'employer' => $brainStation,
                'title' => 'Software Engineering Intern',
                'company' => 'BrainStation 23',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'internship',
                'salary_range' => 'BDT 15,000 - 20,000',
                'description' => 'A 6-month paid internship for final-year CSE students. Learn modern PHP, Laravel, and Vue while shipping real features under senior mentorship.',
            ],
            [
                'employer' => $pathao,
                'title' => 'Senior PHP Engineer',
                'company' => 'Pathao',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'salary_range' => 'BDT 120,000 - 180,000',
                'description' => 'Lead the design of high-throughput services powering ride-sharing and food delivery. Mentor engineers, review code, and drive architectural decisions for systems at scale.',
            ],
            [
                'employer' => $pathao,
                'title' => 'React Native Developer',
                'company' => 'Pathao',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'salary_range' => 'BDT 90,000 - 140,000',
                'description' => 'Build cross-platform mobile apps used by millions across Bangladesh. Optimize performance, integrate native modules, and ship features for both Android and iOS.',
            ],
            [
                'employer' => $pathao,
                'title' => 'Data Analyst',
                'company' => 'Pathao',
                'location' => 'Remote',
                'type' => 'remote',
                'salary_range' => 'BDT 60,000 - 95,000',
                'description' => 'Turn raw operational data into actionable insight with SQL, Python, and dashboards. Partner with product and operations to measure and grow key metrics.',
            ],
            [
                'employer' => $pathao,
                'title' => 'Product Designer (UI/UX)',
                'company' => 'Pathao',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'part-time',
                'salary_range' => 'BDT 40,000 - 60,000',
                'description' => 'Shape intuitive, accessible experiences across web and mobile. Create wireframes, prototypes, and polished UI in Figma, and validate designs with real users.',
            ],
            [
                'employer' => $pathao,
                'title' => 'Customer Support Associate',
                'company' => 'Pathao',
                'location' => 'Chattogram, Bangladesh',
                'type' => 'part-time',
                'salary_range' => 'BDT 18,000 - 25,000',
                'description' => 'Be the friendly voice that helps riders and merchants succeed. Resolve queries over chat and phone, and escalate issues with empathy and speed.',
            ],
            [
                'employer' => $pathao,
                'title' => 'Machine Learning Engineer',
                'company' => 'Pathao',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'salary_range' => 'BDT 130,000 - 200,000',
                'description' => 'Build and deploy ML models for demand forecasting, ETA prediction, and fraud detection. Productionize models with Python, and collaborate with data and backend teams.',
            ],
        ];

        $created = [];
        foreach ($jobs as $data) {
            $employer = $data['employer'];
            unset($data['employer']);
            $created[] = $employer->jobs()->create(array_merge($data, ['is_open' => true]));
        }

        // Close one listing to demonstrate the "closed" state.
        $created[count($created) - 1]->update(['is_open' => false]);

        // ---- Applications ----------------------------------------------
        Application::create([
            'job_post_id' => $created[0]->id,
            'seeker_id' => $nafiz->id,
            'cover_letter' => 'I am a Bangladeshi full-stack developer with hands-on Laravel and Vue experience. I have shipped production apps using Inertia.js and would love to bring that to your backend team.',
            'status' => 'reviewed',
        ]);

        Application::create([
            'job_post_id' => $created[2]->id,
            'seeker_id' => $nafiz->id,
            'cover_letter' => 'Full-stack Laravel and Vue is exactly my wheelhouse. I enjoy owning features end-to-end and shipping weekly. Excited about the remote-first culture.',
            'status' => 'pending',
        ]);

        Application::create([
            'job_post_id' => $created[6]->id,
            'seeker_id' => $nafiz->id,
            'cover_letter' => 'I would relish the challenge of high-throughput PHP services at Pathao scale. I care deeply about clean architecture, testing, and mentoring.',
            'status' => 'pending',
        ]);

        Application::create([
            'job_post_id' => $created[1]->id,
            'seeker_id' => $rumi->id,
            'cover_letter' => 'Vue 3 and Tailwind are my daily tools. I have built component libraries and care about accessible, performant interfaces.',
            'status' => 'accepted',
        ]);

        Application::create([
            'job_post_id' => $created[7]->id,
            'seeker_id' => $rumi->id,
            'cover_letter' => 'I have shipped React Native apps with thousands of users and love optimizing mobile performance. The Pathao mission to serve millions is inspiring.',
            'status' => 'pending',
        ]);

        $this->command->info('Seeded 2 employers, 2 seekers, 12 jobs, 5 applications.');
        $this->command->info('Login: nafiz@example.com / password (seeker) | hr@brainstation-23.com / password (employer)');
    }
}
