<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_post_id',
        'seeker_id',
        'cover_letter',
        'resume_path',
        'resume_name',
        'status',
    ];

    protected $appends = ['resume_url'];

    public const STATUSES = ['pending', 'reviewed', 'accepted', 'rejected'];

    /**
     * Publicly-resolvable URL for the uploaded resume, if any.
     */
    public function getResumeUrlAttribute(): ?string
    {
        return $this->resume_path ? asset('storage/'.$this->resume_path) : null;
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_post_id');
    }

    public function seeker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seeker_id');
    }
}
