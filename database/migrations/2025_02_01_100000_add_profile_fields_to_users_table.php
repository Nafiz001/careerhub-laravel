<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rich profile fields. Seeker-facing columns (headline, bio, skills...)
     * and employer/company-facing columns (company_name, website, logo...).
     * All nullable so existing rows remain valid.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Shared.
            $table->string('avatar')->nullable()->after('role');
            $table->string('location')->nullable()->after('avatar');
            $table->string('phone')->nullable()->after('location');

            // Seeker profile.
            $table->string('headline')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('headline');
            $table->json('skills')->nullable()->after('bio');
            // entry | junior | mid | senior | lead
            $table->string('experience_level')->nullable()->after('skills');

            // Employer / company profile.
            $table->string('company_name')->nullable()->after('experience_level');
            $table->string('website')->nullable()->after('company_name');
            $table->string('logo')->nullable()->after('website');
            $table->text('about')->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'location',
                'phone',
                'headline',
                'bio',
                'skills',
                'experience_level',
                'company_name',
                'website',
                'logo',
                'about',
            ]);
        });
    }
};
