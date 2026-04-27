<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('company');
            $table->string('location');
            // full-time | part-time | remote | internship
            $table->string('type');
            $table->string('salary_range')->nullable();
            $table->text('description');
            $table->boolean('is_open')->default(true);
            $table->timestamps();

            $table->index('type');
            $table->index('location');
            $table->index('is_open');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
