<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;

class JobPolicy
{
    /**
     * Only employers may create jobs.
     */
    public function create(User $user): bool
    {
        return $user->isEmployer();
    }

    /**
     * Only the owning employer may update their job.
     */
    public function update(User $user, Job $job): bool
    {
        return $user->isEmployer() && $user->id === $job->employer_id;
    }

    public function delete(User $user, Job $job): bool
    {
        return $this->update($user, $job);
    }

    /**
     * Owning employer may view the applicant list for the job.
     */
    public function viewApplicants(User $user, Job $job): bool
    {
        return $this->update($user, $job);
    }

    /**
     * Seekers may apply; employers (and the owner) may not.
     */
    public function apply(User $user, Job $job): bool
    {
        return $user->isSeeker() && $job->is_open;
    }
}
