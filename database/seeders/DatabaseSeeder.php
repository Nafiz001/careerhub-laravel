<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdated;
use App\Notifications\NewApplicationReceived;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed a realistic Bangladesh-flavoured job board:
     * employers + company profiles, seekers + rich profiles, featured/remote
     * listings with salary ranges and skills, applications, saved jobs, and
     * sample notifications.
     */
    public function run(): void
    {
        // ---- Employers --------------------------------------------------
        $brainStation = User::create([
            'name' => 'BrainStation 23',
            'email' => 'hr@brainstation-23.com',
            'role' => 'employer',
            'company_name' => 'BrainStation 23',
            'website' => 'https://brainstation-23.com',
            'location' => 'Dhaka, Bangladesh',
            'about' => 'BrainStation 23 is one of the largest software companies in Bangladesh, delivering enterprise solutions to clients across the globe.',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $pathao = User::create([
            'name' => 'Pathao',
            'email' => 'careers@pathao.com',
            'role' => 'employer',
            'company_name' => 'Pathao',
            'website' => 'https://pathao.com',
            'location' => 'Dhaka, Bangladesh',
            'about' => 'Pathao is the leading on-demand digital platform in Bangladesh, powering ride-sharing, food delivery, and logistics for millions of users.',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // ---- Seekers ----------------------------------------------------
        $nafiz = User::create([
            'name' => 'Md. Nafiz Ahmed',
            'email' => 'nafiz@example.com',
            'role' => 'seeker',
            'headline' => 'Full-Stack Developer (Laravel + Vue)',
            'bio' => 'Bangladeshi full-stack engineer who loves shipping clean, tested features across the Laravel + Inertia + Vue stack.',
            'location' => 'Dhaka, Bangladesh',
            'phone' => '+8801712345678',
            'skills' => ['PHP', 'Laravel', 'Vue.js', 'Inertia.js', 'Tailwind CSS', 'MySQL'],
            'experience_level' => 'mid',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $rumi = User::create([
            'name' => 'Rumana Akter',
            'email' => 'rumana@example.com',
            'role' => 'seeker',
            'headline' => 'Frontend Engineer specialising in Vue 3',
            'bio' => 'Frontend engineer focused on accessible, performant component libraries with Vue 3 and Tailwind.',
            'location' => 'Chattogram, Bangladesh',
            'phone' => '+8801898765432',
            'skills' => ['Vue.js', 'TypeScript', 'Tailwind CSS', 'React', 'Figma'],
            'experience_level' => 'senior',
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
                'category' => 'Engineering',
                'experience_level' => 'mid',
                'salary_range' => 'BDT 60,000 - 90,000',
                'salary_min' => 60000,
                'salary_max' => 90000,
                'skills' => ['PHP', 'Laravel', 'MySQL', 'Redis'],
                'remote' => false,
                'is_featured' => true,
                'description' => 'Build and maintain RESTful APIs and web applications using Laravel. You will work with MySQL, Redis, and queue workers in an Agile team. Strong Eloquent and PHP 8 skills required.',
            ],
            [
                'employer' => $brainStation,
                'title' => 'Vue.js Frontend Engineer',
                'company' => 'BrainStation 23',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'category' => 'Engineering',
                'experience_level' => 'mid',
                'salary_range' => 'BDT 55,000 - 85,000',
                'salary_min' => 55000,
                'salary_max' => 85000,
                'skills' => ['Vue.js', 'Inertia.js', 'Tailwind CSS', 'Vite'],
                'remote' => false,
                'description' => 'Craft delightful UIs with Vue 3 and the Composition API. Experience with Inertia.js, Tailwind CSS, and Vite is a big plus. You will collaborate closely with backend and design teams.',
            ],
            [
                'employer' => $brainStation,
                'title' => 'Full-Stack Developer (Laravel + Vue)',
                'company' => 'BrainStation 23',
                'location' => 'Remote',
                'type' => 'remote',
                'category' => 'Engineering',
                'experience_level' => 'senior',
                'salary_range' => 'BDT 70,000 - 110,000',
                'salary_min' => 70000,
                'salary_max' => 110000,
                'skills' => ['PHP', 'Laravel', 'Vue.js', 'Inertia.js'],
                'remote' => true,
                'is_featured' => true,
                'description' => 'Own features end-to-end across a Laravel + Inertia + Vue stack. Write clean, tested code and ship to production weekly. Remote-first role open to candidates across Bangladesh.',
            ],
            [
                'employer' => $brainStation,
                'title' => 'QA Automation Engineer',
                'company' => 'BrainStation 23',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'category' => 'Engineering',
                'experience_level' => 'junior',
                'salary_range' => 'BDT 45,000 - 70,000',
                'salary_min' => 45000,
                'salary_max' => 70000,
                'skills' => ['PHPUnit', 'Pest', 'Cypress', 'CI/CD'],
                'remote' => false,
                'description' => 'Design and maintain automated test suites with PHPUnit, Pest, and Cypress. Champion quality across the SDLC and integrate tests into our CI pipeline.',
            ],
            [
                'employer' => $brainStation,
                'title' => 'DevOps Engineer',
                'company' => 'BrainStation 23',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'category' => 'Engineering',
                'experience_level' => 'senior',
                'salary_range' => 'BDT 80,000 - 130,000',
                'salary_min' => 80000,
                'salary_max' => 130000,
                'skills' => ['Docker', 'AWS', 'CI/CD', 'Laravel'],
                'remote' => true,
                'description' => 'Manage CI/CD, Docker, and cloud infrastructure on AWS. Automate deployments for Laravel applications and keep our systems observable and resilient.',
            ],
            [
                'employer' => $brainStation,
                'title' => 'Software Engineering Intern',
                'company' => 'BrainStation 23',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'internship',
                'category' => 'Engineering',
                'experience_level' => 'entry',
                'salary_range' => 'BDT 15,000 - 20,000',
                'salary_min' => 15000,
                'salary_max' => 20000,
                'skills' => ['PHP', 'Laravel', 'Vue.js'],
                'remote' => false,
                'description' => 'A 6-month paid internship for final-year CSE students. Learn modern PHP, Laravel, and Vue while shipping real features under senior mentorship.',
            ],
            [
                'employer' => $pathao,
                'title' => 'Senior PHP Engineer',
                'company' => 'Pathao',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'category' => 'Engineering',
                'experience_level' => 'lead',
                'salary_range' => 'BDT 120,000 - 180,000',
                'salary_min' => 120000,
                'salary_max' => 180000,
                'skills' => ['PHP', 'Laravel', 'PostgreSQL', 'Redis', 'AWS'],
                'remote' => false,
                'is_featured' => true,
                'description' => 'Lead the design of high-throughput services powering ride-sharing and food delivery. Mentor engineers, review code, and drive architectural decisions for systems at scale.',
            ],
            [
                'employer' => $pathao,
                'title' => 'React Native Developer',
                'company' => 'Pathao',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'category' => 'Engineering',
                'experience_level' => 'mid',
                'salary_range' => 'BDT 90,000 - 140,000',
                'salary_min' => 90000,
                'salary_max' => 140000,
                'skills' => ['React', 'TypeScript', 'Node.js'],
                'remote' => false,
                'description' => 'Build cross-platform mobile apps used by millions across Bangladesh. Optimize performance, integrate native modules, and ship features for both Android and iOS.',
            ],
            [
                'employer' => $pathao,
                'title' => 'Data Analyst',
                'company' => 'Pathao',
                'location' => 'Remote',
                'type' => 'remote',
                'category' => 'Data',
                'experience_level' => 'mid',
                'salary_range' => 'BDT 60,000 - 95,000',
                'salary_min' => 60000,
                'salary_max' => 95000,
                'skills' => ['SQL', 'Python', 'PostgreSQL'],
                'remote' => true,
                'description' => 'Turn raw operational data into actionable insight with SQL, Python, and dashboards. Partner with product and operations to measure and grow key metrics.',
            ],
            [
                'employer' => $pathao,
                'title' => 'Product Designer (UI/UX)',
                'company' => 'Pathao',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'part-time',
                'category' => 'Design',
                'experience_level' => 'mid',
                'salary_range' => 'BDT 40,000 - 60,000',
                'salary_min' => 40000,
                'salary_max' => 60000,
                'skills' => ['Figma', 'Tailwind CSS'],
                'remote' => false,
                'description' => 'Shape intuitive, accessible experiences across web and mobile. Create wireframes, prototypes, and polished UI in Figma, and validate designs with real users.',
            ],
            [
                'employer' => $pathao,
                'title' => 'Customer Support Associate',
                'company' => 'Pathao',
                'location' => 'Chattogram, Bangladesh',
                'type' => 'part-time',
                'category' => 'Customer Support',
                'experience_level' => 'entry',
                'salary_range' => 'BDT 18,000 - 25,000',
                'salary_min' => 18000,
                'salary_max' => 25000,
                'skills' => [],
                'remote' => false,
                'description' => 'Be the friendly voice that helps riders and merchants succeed. Resolve queries over chat and phone, and escalate issues with empathy and speed.',
            ],
            [
                'employer' => $pathao,
                'title' => 'Machine Learning Engineer',
                'company' => 'Pathao',
                'location' => 'Dhaka, Bangladesh',
                'type' => 'full-time',
                'category' => 'Data',
                'experience_level' => 'senior',
                'salary_range' => 'BDT 130,000 - 200,000',
                'salary_min' => 130000,
                'salary_max' => 200000,
                'skills' => ['Python', 'SQL', 'AWS'],
                'remote' => false,
                'description' => 'Build and deploy ML models for demand forecasting, ETA prediction, and fraud detection. Productionize models with Python, and collaborate with data and backend teams.',
            ],
        ];

        $created = [];
        foreach ($jobs as $data) {
            $employer = $data['employer'];
            unset($data['employer']);
            $data['is_open'] = true;
            $data['application_deadline'] = now()->addDays(rand(14, 60))->toDateString();
            $data['views_count'] = rand(20, 600);
            $created[] = $employer->jobs()->create($data);
        }

        // Close one listing to demonstrate the "closed" state.
        $created[count($created) - 1]->update(['is_open' => false]);

        // ---- Applications (+ employer notifications) -------------------
        $applications = [
            [$created[0], $nafiz, 'I am a Bangladeshi full-stack developer with hands-on Laravel and Vue experience. I have shipped production apps using Inertia.js and would love to bring that to your backend team.', 'reviewed'],
            [$created[2], $nafiz, 'Full-stack Laravel and Vue is exactly my wheelhouse. I enjoy owning features end-to-end and shipping weekly. Excited about the remote-first culture.', 'pending'],
            [$created[6], $nafiz, 'I would relish the challenge of high-throughput PHP services at Pathao scale. I care deeply about clean architecture, testing, and mentoring.', 'pending'],
            [$created[1], $rumi, 'Vue 3 and Tailwind are my daily tools. I have built component libraries and care about accessible, performant interfaces.', 'accepted'],
            [$created[7], $rumi, 'I have shipped React Native apps with thousands of users and love optimizing mobile performance. The Pathao mission to serve millions is inspiring.', 'pending'],
        ];

        foreach ($applications as [$job, $seeker, $coverLetter, $status]) {
            $application = Application::create([
                'job_post_id' => $job->id,
                'seeker_id' => $seeker->id,
                'cover_letter' => $coverLetter,
                'status' => $status,
            ]);

            // Notify the employer that an application arrived.
            $application->setRelation('job', $job);
            $application->setRelation('seeker', $seeker);
            $job->employer->notify(new NewApplicationReceived($application));

            // For non-pending statuses, also notify the seeker of the decision.
            if ($status !== 'pending') {
                $seeker->notify(new ApplicationStatusUpdated($application));
            }
        }

        // ---- Saved jobs (bookmarks) ------------------------------------
        $nafiz->savedJobs()->syncWithoutDetaching([$created[1]->id, $created[4]->id]);
        $rumi->savedJobs()->syncWithoutDetaching([$created[2]->id, $created[8]->id, $created[9]->id]);

        $this->command->info('Seeded 2 employers, 2 seekers, 12 jobs, 5 applications, saved jobs, and notifications.');
        $this->command->info('Login: nafiz@example.com / password (seeker) | hr@brainstation-23.com / password (employer)');
    }
}
