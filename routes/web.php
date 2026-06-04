<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SavedJobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/', [JobController::class, 'index'])->name('home');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Employer: manage jobs (placed before the {job} wildcard binding above is fine
    // because these have distinct prefixes).
    Route::get('/manage/jobs/create', [JobController::class, 'create'])->name('jobs.create');
    Route::post('/manage/jobs', [JobController::class, 'store'])->name('jobs.store');
    Route::get('/manage/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit');
    Route::put('/manage/jobs/{job}', [JobController::class, 'update'])->name('jobs.update');
    Route::delete('/manage/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy');
    Route::get('/manage/jobs/{job}/applicants', [ApplicationController::class, 'forJob'])->name('jobs.applicants');
    Route::patch('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])->name('applications.status');
    Route::get('/applications/{application}/resume', [ApplicationController::class, 'downloadResume'])->name('applications.resume');

    // Seeker: apply + view my applications.
    Route::post('/jobs/{job}/apply', [ApplicationController::class, 'store'])->name('applications.store');
    Route::get('/my/applications', [ApplicationController::class, 'index'])->name('applications.index');

    // Seeker: saved jobs (bookmarks).
    Route::get('/my/saved-jobs', [SavedJobController::class, 'index'])->name('saved-jobs.index');
    Route::post('/jobs/{job}/save', [SavedJobController::class, 'toggle'])->name('saved-jobs.toggle');

    // Notifications (in-app feed).
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // Profile (Breeze).
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
