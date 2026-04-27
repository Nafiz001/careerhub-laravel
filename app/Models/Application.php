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
        'status',
    ];

    public const STATUSES = ['pending', 'reviewed', 'accepted', 'rejected'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_post_id');
    }

    public function seeker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seeker_id');
    }
}
