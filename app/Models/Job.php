<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    /**
     * The "jobs" name is reserved by Laravel's queue system, so the
     * underlying table is "job_posts" while the model stays "Job".
     */
    protected $table = 'job_posts';

    protected $fillable = [
        'employer_id',
        'title',
        'company',
        'location',
        'type',
        'category',
        'experience_level',
        'salary_range',
        'salary_min',
        'salary_max',
        'description',
        'skills',
        'remote',
        'application_deadline',
        'is_featured',
        'is_open',
        'views_count',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'remote' => 'boolean',
        'is_featured' => 'boolean',
        'skills' => 'array',
        'salary_min' => 'integer',
        'salary_max' => 'integer',
        'views_count' => 'integer',
        'application_deadline' => 'date',
    ];

    public const TYPES = ['full-time', 'part-time', 'remote', 'internship'];

    public const EXPERIENCE_LEVELS = ['entry', 'junior', 'mid', 'senior', 'lead'];

    public const CATEGORIES = [
        'Engineering',
        'Design',
        'Data',
        'Product',
        'Marketing',
        'Sales',
        'Customer Support',
        'Operations',
    ];

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_post_id');
    }

    /**
     * Users (seekers) who have bookmarked this job.
     */
    public function savers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_jobs', 'job_post_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Advanced filtering: keyword (title/company/description), type, location,
     * category, experience level, remote flag, salary range overlap, and skills.
     *
     * Salary uses range-overlap semantics: a job matches when its [min,max]
     * intersects the requested [salary_min, salary_max] window.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['type'] ?? null, fn (Builder $q, string $type) => $q->where('type', $type))
            ->when($filters['location'] ?? null, fn (Builder $q, string $location) => $q->where('location', 'like', "%{$location}%"))
            ->when($filters['category'] ?? null, fn (Builder $q, string $category) => $q->where('category', $category))
            ->when($filters['experience_level'] ?? null, fn (Builder $q, string $level) => $q->where('experience_level', $level))
            ->when(
                isset($filters['remote']) && filter_var($filters['remote'], FILTER_VALIDATE_BOOLEAN),
                fn (Builder $q) => $q->where('remote', true)
            )
            ->when(isset($filters['salary_min']) && $filters['salary_min'] !== '', function (Builder $q) use ($filters) {
                // Job's max pay must reach at least the requested floor.
                $q->where(function (Builder $inner) use ($filters) {
                    $inner->whereNull('salary_max')
                        ->orWhere('salary_max', '>=', (int) $filters['salary_min']);
                });
            })
            ->when(isset($filters['salary_max']) && $filters['salary_max'] !== '', function (Builder $q) use ($filters) {
                // Job's min pay must not exceed the requested ceiling.
                $q->where(function (Builder $inner) use ($filters) {
                    $inner->whereNull('salary_min')
                        ->orWhere('salary_min', '<=', (int) $filters['salary_max']);
                });
            })
            ->when($this->normalizeSkills($filters['skills'] ?? null), function (Builder $q, array $skills) {
                // Match if any requested skill appears in the job's skills JSON.
                $q->where(function (Builder $inner) use ($skills) {
                    foreach ($skills as $skill) {
                        $inner->orWhere('skills', 'like', '%"'.$skill.'"%');
                    }
                });
            });
    }

    /**
     * Accept skills as a comma-separated string or array; return a clean list.
     */
    private function normalizeSkills(mixed $skills): ?array
    {
        if (is_string($skills)) {
            $skills = explode(',', $skills);
        }

        if (! is_array($skills)) {
            return null;
        }

        $skills = array_values(array_filter(array_map('trim', $skills)));

        return $skills === [] ? null : $skills;
    }
}
