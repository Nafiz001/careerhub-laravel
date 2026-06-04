<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Advanced search facets + analytics fields for job listings.
     */
    public function up(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->string('category')->nullable()->after('type');
            // entry | junior | mid | senior | lead
            $table->string('experience_level')->nullable()->after('category');
            $table->unsignedInteger('salary_min')->nullable()->after('salary_range');
            $table->unsignedInteger('salary_max')->nullable()->after('salary_min');
            $table->json('skills')->nullable()->after('description');
            $table->boolean('remote')->default(false)->after('skills');
            $table->date('application_deadline')->nullable()->after('remote');
            $table->boolean('is_featured')->default(false)->after('application_deadline');
            $table->unsignedInteger('views_count')->default(0)->after('is_featured');

            $table->index('category');
            $table->index('experience_level');
            $table->index('remote');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['experience_level']);
            $table->dropIndex(['remote']);
            $table->dropIndex(['is_featured']);

            $table->dropColumn([
                'category',
                'experience_level',
                'salary_min',
                'salary_max',
                'skills',
                'remote',
                'application_deadline',
                'is_featured',
                'views_count',
            ]);
        });
    }
};
