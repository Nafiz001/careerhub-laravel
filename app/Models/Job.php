<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'salary_range',
        'description',
        'is_open',
    ];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    public const TYPES = ['full-time', 'part-time', 'remote', 'internship'];

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_post_id');
    }

    /**
     * Filter listings by keyword (title or company) and facets (type, location).
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%");
                });
            })
            ->when($filters['type'] ?? null, fn (Builder $q, string $type) => $q->where('type', $type))
            ->when($filters['location'] ?? null, fn (Builder $q, string $location) => $q->where('location', 'like', "%{$location}%"));
    }
}
