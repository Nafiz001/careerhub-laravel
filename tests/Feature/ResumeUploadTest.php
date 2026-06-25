<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResumeUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeker_can_apply_with_a_pdf_resume(): void
    {
        Storage::fake('public');

        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create();

        $resume = UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf');

        $this->actingAs($seeker)
            ->post(route('applications.store', $job), [
                'cover_letter' => 'I am very interested in this role and believe I am a strong fit.',
                'resume' => $resume,
            ])
            ->assertRedirect(route('applications.index'));

        $application = Application::first();

        $this->assertNotNull($application->resume_path);
        $this->assertSame('cv.pdf', $application->resume_name);
        Storage::disk('public')->assertExists($application->resume_path);
    }

    public function test_resume_must_be_a_valid_document_type(): void
    {
        Storage::fake('public');

        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create();

        $bad = UploadedFile::fake()->create('malware.exe', 100, 'application/octet-stream');

        $this->actingAs($seeker)
            ->post(route('applications.store', $job), [
                'cover_letter' => 'I am very interested in this role and believe I am a strong fit.',
                'resume' => $bad,
            ])
            ->assertSessionHasErrors('resume');

        $this->assertDatabaseCount('applications', 0);
    }

    public function test_resume_is_optional_on_apply(): void
    {
        Storage::fake('public');

        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create();

        $this->actingAs($seeker)
            ->post(route('applications.store', $job), [
                'cover_letter' => 'I am very interested in this role and believe I am a strong fit.',
            ])
            ->assertRedirect(route('applications.index'));

        $this->assertDatabaseCount('applications', 1);
        $this->assertNull(Application::first()->resume_path);
    }

    public function test_owning_employer_can_download_the_resume(): void
    {
        Storage::fake('public');

        $employer = User::factory()->employer()->create();
        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create(['employer_id' => $employer->id]);

        $resume = UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf');
        $path = $resume->store('resumes', 'public');

        $application = Application::factory()->create([
            'job_post_id' => $job->id,
            'seeker_id' => $seeker->id,
            'resume_path' => $path,
            'resume_name' => 'cv.pdf',
        ]);

        $this->actingAs($employer)
            ->get(route('applications.resume', $application))
            ->assertOk()
            ->assertDownload('cv.pdf');
    }

    public function test_other_employer_cannot_download_the_resume(): void
    {
        Storage::fake('public');

        $owner = User::factory()->employer()->create();
        $intruder = User::factory()->employer()->create();
        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create(['employer_id' => $owner->id]);

        $path = UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf')->store('resumes', 'public');

        $application = Application::factory()->create([
            'job_post_id' => $job->id,
            'seeker_id' => $seeker->id,
            'resume_path' => $path,
            'resume_name' => 'cv.pdf',
        ]);

        $this->actingAs($intruder)
            ->get(route('applications.resume', $application))
            ->assertForbidden();
    }
}
