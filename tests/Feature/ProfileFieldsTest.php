<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeker_can_update_rich_profile_fields(): void
    {
        $seeker = User::factory()->seeker()->create();

        $this->actingAs($seeker)
            ->patch(route('profile.update'), [
                'name' => $seeker->name,
                'email' => $seeker->email,
                'headline' => 'Senior Laravel Engineer',
                'bio' => 'I build robust backends.',
                'location' => 'Dhaka, Bangladesh',
                'phone' => '+8801712345678',
                'skills' => ['PHP', 'Laravel', 'Vue.js'],
                'experience_level' => 'senior',
            ])
            ->assertRedirect(route('profile.edit'));

        $seeker->refresh();

        $this->assertSame('Senior Laravel Engineer', $seeker->headline);
        $this->assertSame(['PHP', 'Laravel', 'Vue.js'], $seeker->skills);
        $this->assertSame('senior', $seeker->experience_level);
    }

    public function test_employer_can_update_company_profile(): void
    {
        $employer = User::factory()->employer()->create();

        $this->actingAs($employer)
            ->patch(route('profile.update'), [
                'name' => $employer->name,
                'email' => $employer->email,
                'company_name' => 'Acme Corp',
                'website' => 'https://acme.test',
                'about' => 'We make everything.',
            ])
            ->assertRedirect(route('profile.edit'));

        $employer->refresh();

        $this->assertSame('Acme Corp', $employer->company_name);
        $this->assertSame('https://acme.test', $employer->website);
    }

    public function test_user_can_upload_an_avatar(): void
    {
        Storage::fake('public');

        $seeker = User::factory()->seeker()->create();

        // Note: UploadedFile::fake()->image() needs the GD extension, which is
        // not guaranteed in CI. A created file with an image mime exercises the
        // same validation + storage path without requiring GD.
        $avatar = UploadedFile::fake()->create('me.jpg', 100, 'image/jpeg');

        $this->actingAs($seeker)
            ->patch(route('profile.update'), [
                'name' => $seeker->name,
                'email' => $seeker->email,
                'avatar' => $avatar,
            ])
            ->assertRedirect(route('profile.edit'));

        $seeker->refresh();
        $this->assertNotNull($seeker->avatar);
        Storage::disk('public')->assertExists($seeker->avatar);
    }

    public function test_invalid_experience_level_is_rejected(): void
    {
        $seeker = User::factory()->seeker()->create();

        $this->actingAs($seeker)
            ->patch(route('profile.update'), [
                'name' => $seeker->name,
                'email' => $seeker->email,
                'experience_level' => 'wizard',
            ])
            ->assertSessionHasErrors('experience_level');
    }
}
