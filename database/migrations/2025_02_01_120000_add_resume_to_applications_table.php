<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-application resume upload (PDF/DOC). Stored on the public disk,
     * path kept here; original filename preserved for friendly downloads.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('resume_path')->nullable()->after('cover_letter');
            $table->string('resume_name')->nullable()->after('resume_path');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['resume_path', 'resume_name']);
        });
    }
};
