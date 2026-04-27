<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained('job_posts')->cascadeOnDelete();
            $table->foreignId('seeker_id')->constrained('users')->cascadeOnDelete();
            $table->text('cover_letter');
            // pending | reviewed | accepted | rejected
            $table->string('status')->default('pending');
            $table->timestamps();

            // A seeker may apply to a given job only once.
            $table->unique(['job_post_id', 'seeker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
